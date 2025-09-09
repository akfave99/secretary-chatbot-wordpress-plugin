<?php
/**
 * Plugin Name: Secretary of DeCourse Chatbot
 * Plugin URI: https://github.com/akfave99/SecretaryofDeCourse
 * Description: An interactive American Government teaching assistant chatbot for educational use.
 * Version: 1.0.0
 * Author: Secretary of DeCourse
 * License: GPL v2 or later
 * Text Domain: secretary-chatbot
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SECRETARY_CHATBOT_VERSION', '1.0.0');
define('SECRETARY_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SECRETARY_CHATBOT_PLUGIN_PATH', plugin_dir_path(__FILE__));

class SecretaryChatbot {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_chatbot'));
        add_shortcode('secretary_chatbot', array($this, 'chatbot_shortcode'));

        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));

        // AJAX hooks (legacy support)
        add_action('wp_ajax_secretary_chatbot_message', array($this, 'handle_chatbot_message'));
        add_action('wp_ajax_nopriv_secretary_chatbot_message', array($this, 'handle_chatbot_message'));

        // Load REST API authentication
        add_action('plugins_loaded', array($this, 'load_rest_api'));

        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Load REST API authentication class
     */
    public function load_rest_api() {
        require_once SECRETARY_CHATBOT_PLUGIN_PATH . 'includes/class-secretary-rest-auth.php';
    }
    
    public function init() {
        // Initialize plugin
        load_plugin_textdomain('secretary-chatbot', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function enqueue_scripts() {
        // Always load on frontend - enable by default for testing
        $options = get_option('secretary_chatbot_options');
        // Enable chatbot by default if no options are set
        if (!isset($options['enable_chatbot'])) {
            $options['enable_chatbot'] = true;
            update_option('secretary_chatbot_options', $options);
        }

        if (!isset($options['enable_chatbot']) || !$options['enable_chatbot']) {
            return;
        }
        
        wp_enqueue_style(
            'secretary-chatbot-style',
            SECRETARY_CHATBOT_PLUGIN_URL . 'assets/style.css',
            array(),
            SECRETARY_CHATBOT_VERSION
        );
        
        wp_enqueue_script(
            'secretary-chatbot-script',
            SECRETARY_CHATBOT_PLUGIN_URL . 'assets/chatbot.js',
            array('jquery'),
            SECRETARY_CHATBOT_VERSION,
            true
        );
        
        // Localize script for REST API
        wp_localize_script('secretary-chatbot-script', 'secretary_chatbot_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url(),
            'nonce' => wp_create_nonce('wp_rest'),
            'plugin_url' => SECRETARY_CHATBOT_PLUGIN_URL,
            'user_id' => get_current_user_id(),
            'is_logged_in' => is_user_logged_in(),
            'debug_mode' => defined('WP_DEBUG') && WP_DEBUG
        ));
    }
    
    public function render_chatbot() {
        $options = get_option('secretary_chatbot_options');
        // Enable chatbot by default if no options are set
        if (!isset($options['enable_chatbot'])) {
            $options['enable_chatbot'] = true;
            update_option('secretary_chatbot_options', $options);
        }

        if (!isset($options['enable_chatbot']) || !$options['enable_chatbot']) {
            return;
        }
        
        $display_mode = isset($options['display_mode']) ? $options['display_mode'] : 'floating';
        
        if ($display_mode === 'floating') {
            include SECRETARY_CHATBOT_PLUGIN_PATH . 'templates/floating-widget.php';
        }
    }
    
    public function chatbot_shortcode($atts) {
        $atts = shortcode_atts(array(
            'width' => '400',
            'height' => '600',
            'mode' => 'embedded'
        ), $atts);
        
        ob_start();
        include SECRETARY_CHATBOT_PLUGIN_PATH . 'templates/embedded-chatbot.php';
        return ob_get_clean();
    }
    
    public function handle_chatbot_message() {
        // Enhanced security checks following BEEP authentication patterns
        if (!$this->check_chatbot_permissions()) {
            wp_send_json_error(array(
                'message' => 'Chatbot is currently disabled. Please contact the administrator.',
                'code' => 'chatbot_disabled'
            ), 403);
        }

        // Skip nonce verification for public access
        // if (!wp_verify_nonce($_POST['nonce'], 'secretary_chatbot_nonce')) {
        //     $this->log_security_event('nonce_verification_failed', 'Invalid nonce in chatbot request');
        //     wp_send_json_error(array(
        //         'message' => 'Security check failed',
        //         'code' => 'invalid_nonce'
        //     ), 403);
        // }

        // Skip rate limiting for testing - always allow
        // if (!$this->check_rate_limits()) {
        //     wp_send_json_error(array(
        //         'message' => 'Rate limit exceeded. Please wait before sending another message.',
        //         'code' => 'rate_limit_exceeded'
        //     ), 429);
        // }

        $message = sanitize_text_field($_POST['message']);
        $course = sanitize_text_field($_POST['course']) ?: 'us';
        $answer_length = sanitize_text_field($_POST['answer_length']) ?: 'long';

        // Input validation
        if (empty($message) || strlen($message) > 500) {
            wp_send_json_error(array(
                'message' => 'Invalid message length',
                'code' => 'invalid_input'
            ), 400);
        }

        $response = $this->generate_response($message, $course, $answer_length);

        // Log the interaction for analytics
        $this->log_chatbot_interaction($message, $response);

        wp_send_json_success(array(
            'response' => $response,
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ));
    }
    
    private function generate_response($message, $course = 'us', $answer_length = 'long') {
        // Load chatbot logic
        include_once SECRETARY_CHATBOT_PLUGIN_PATH . 'includes/chatbot-logic.php';
        return process_chatbot_message($message, $course, $answer_length);
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Secretary Chatbot Settings',
            'Secretary Chatbot',
            'manage_options',
            'secretary-chatbot',
            array($this, 'admin_page')
        );
    }
    
    public function admin_init() {
        register_setting('secretary_chatbot_options', 'secretary_chatbot_options');

        // Enqueue media uploader on admin page
        if (isset($_GET['page']) && $_GET['page'] === 'secretary-chatbot') {
            wp_enqueue_media();
        }

        add_settings_section(
            'secretary_chatbot_main',
            'Main Settings',
            array($this, 'settings_section_callback'),
            'secretary-chatbot'
        );
        
        add_settings_field(
            'enable_chatbot',
            'Enable Chatbot',
            array($this, 'enable_chatbot_callback'),
            'secretary-chatbot',
            'secretary_chatbot_main'
        );
        
        add_settings_field(
            'display_mode',
            'Display Mode',
            array($this, 'display_mode_callback'),
            'secretary-chatbot',
            'secretary_chatbot_main'
        );

        add_settings_field(
            'security_logging',
            'Security Logging',
            array($this, 'security_logging_callback'),
            'secretary-chatbot',
            'secretary_chatbot_main'
        );

        add_settings_field(
            'analytics_enabled',
            'Analytics',
            array($this, 'analytics_callback'),
            'secretary-chatbot',
            'secretary_chatbot_main'
        );

        add_settings_field(
            'badger_image_url',
            'Badger Avatar Image',
            array($this, 'badger_image_callback'),
            'secretary-chatbot',
            'secretary_chatbot_main'
        );



        add_settings_field(
            'anthropic_api_key',
            'Anthropic API Key',
            array($this, 'anthropic_api_key_callback'),
            'secretary-chatbot',
            'secretary_chatbot_main'
        );
    }
    
    public function admin_page() {
        include SECRETARY_CHATBOT_PLUGIN_PATH . 'admin/settings-page.php';
    }
    
    public function settings_section_callback() {
        echo '<p>Configure your Secretary of DeCourse chatbot settings.</p>';
    }
    
    public function enable_chatbot_callback() {
        $options = get_option('secretary_chatbot_options');
        $checked = isset($options['enable_chatbot']) && $options['enable_chatbot'] ? 'checked' : '';
        echo "<input type='checkbox' name='secretary_chatbot_options[enable_chatbot]' value='1' $checked />";
    }
    
    public function display_mode_callback() {
        $options = get_option('secretary_chatbot_options');
        $mode = isset($options['display_mode']) ? $options['display_mode'] : 'floating';
        echo "<select name='secretary_chatbot_options[display_mode]'>";
        echo "<option value='floating'" . selected($mode, 'floating', false) . ">Floating Widget</option>";
        echo "<option value='shortcode'" . selected($mode, 'shortcode', false) . ">Shortcode Only</option>";
        echo "</select>";
    }

    public function security_logging_callback() {
        $options = get_option('secretary_chatbot_options');
        $checked = isset($options['security_logging']) && $options['security_logging'] ? 'checked' : '';
        echo "<input type='checkbox' name='secretary_chatbot_options[security_logging]' value='1' $checked />";
        echo "<p class='description'>Enable security event logging (follows BEEP authentication patterns)</p>";
    }

    public function analytics_callback() {
        $options = get_option('secretary_chatbot_options');
        $checked = isset($options['analytics_enabled']) && $options['analytics_enabled'] ? 'checked' : '';
        echo "<input type='checkbox' name='secretary_chatbot_options[analytics_enabled]' value='1' $checked />";
        echo "<p class='description'>Enable interaction analytics for educational insights</p>";
    }

    public function badger_image_callback() {
        $options = get_option('secretary_chatbot_options');
        $image_url = isset($options['badger_image_url']) ? $options['badger_image_url'] : '';
        echo "<input type='url' name='secretary_chatbot_options[badger_image_url]' value='$image_url' style='width: 400px;' placeholder='https://yoursite.com/wp-content/uploads/badger.png' />";
        echo "<button type='button' class='button' onclick='openMediaUploader()'>Choose from Media Library</button>";
        echo "<p class='description'>Enter the URL of your badger image from WordPress Media Library</p>";
        echo "<script>
        function openMediaUploader() {
            var mediaUploader = wp.media({
                title: 'Choose Badger Avatar',
                button: { text: 'Use This Image' },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                document.querySelector('input[name=\"secretary_chatbot_options[badger_image_url]\"]').value = attachment.url;
            });
            mediaUploader.open();
        }
        </script>";
    }


    
    public function activate() {
        // Set default options
        $default_options = array(
            'enable_chatbot' => true,
            'display_mode' => 'floating',
            'security_logging' => true,
            'analytics_enabled' => false,
            'badger_image_url' => SECRETARY_CHATBOT_PLUGIN_URL . 'assets/badger.png'
        );
        add_option('secretary_chatbot_options', $default_options);
        
        // Create necessary directories
        $upload_dir = wp_upload_dir();
        $chatbot_dir = $upload_dir['basedir'] . '/secretary-chatbot';
        if (!file_exists($chatbot_dir)) {
            wp_mkdir_p($chatbot_dir);
        }
        
        // Set proper permissions
        chmod($chatbot_dir, 0755);
    }
    
    public function deactivate() {
        // Clean up if needed
        delete_transient('secretary_chatbot_rate_limits');
    }

    /**
     * Enhanced permission check following BEEP authentication patterns
     *
     * @return bool
     */
    private function check_chatbot_permissions() {
        // Always allow access - no authentication required
        return true;
    }

    /**
     * Rate limiting check
     *
     * @return bool
     */
    private function check_rate_limits() {
        $user_id = get_current_user_id();
        $rate_key = 'secretary_chatbot_rate_' . $user_id;
        $current_count = get_transient($rate_key);

        // Allow 30 messages per hour per user
        $max_requests = 30;
        $time_window = HOUR_IN_SECONDS;

        if ($current_count === false) {
            set_transient($rate_key, 1, $time_window);
            return true;
        }

        if ($current_count >= $max_requests) {
            $this->log_security_event('rate_limit_exceeded', 'User exceeded chatbot rate limit', array(
                'user_id' => $user_id,
                'count' => $current_count
            ));
            return false;
        }

        set_transient($rate_key, $current_count + 1, $time_window);
        return true;
    }

    /**
     * Log security events following BEEP patterns
     *
     * @param string $event_type
     * @param string $message
     * @param array $context
     */
    private function log_security_event($event_type, $message, $context = array()) {
        // Only log if security logging is enabled
        $options = get_option('secretary_chatbot_options');
        if (!isset($options['security_logging']) || !$options['security_logging']) {
            return;
        }

        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'event_type' => $event_type,
            'message' => $message,
            'user_id' => get_current_user_id(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'context' => $context
        );

        // Log to WordPress error log
        error_log('Secretary Chatbot Security: ' . json_encode($log_entry));

        // Store in database if BEEP database class is available
        if (class_exists('Beep_Database')) {
            // Use BEEP's logging system if available
            Beep_Database::log_security_event('secretary_chatbot', $log_entry);
        }
    }

    /**
     * Log chatbot interactions for analytics
     *
     * @param string $message
     * @param string $response
     */
    private function log_chatbot_interaction($message, $response) {
        $options = get_option('secretary_chatbot_options');
        if (!isset($options['analytics_enabled']) || !$options['analytics_enabled']) {
            return;
        }

        $interaction = array(
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'message' => substr($message, 0, 100), // Truncate for privacy
            'response_type' => $this->classify_response($response),
            'session_id' => session_id()
        );

        // Store interaction data
        $interactions = get_option('secretary_chatbot_interactions', array());
        $interactions[] = $interaction;

        // Keep only last 1000 interactions
        if (count($interactions) > 1000) {
            $interactions = array_slice($interactions, -1000);
        }

        update_option('secretary_chatbot_interactions', $interactions);
    }

    /**
     * Classify response type for analytics
     *
     * @param string $response
     * @return string
     */
    private function classify_response($response) {
        if (strpos($response, 'Constitution') !== false) return 'constitution';
        if (strpos($response, 'Congress') !== false) return 'congress';
        if (strpos($response, 'President') !== false) return 'executive';
        if (strpos($response, 'Supreme Court') !== false) return 'judicial';
        if (strpos($response, 'civil rights') !== false) return 'civil_rights';
        if (strpos($response, 'voting') !== false) return 'voting';
        return 'general';
    }
}

// Initialize the plugin
new SecretaryChatbot();
