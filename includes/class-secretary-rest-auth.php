<?php
/**
 * Secretary of DeCourse REST API Authentication
 * 
 * Integrates with BEEP authentication patterns for enhanced security
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Secretary_REST_Auth {
    
    /**
     * API namespace
     */
    const NAMESPACE = 'secretary/v1';
    
    /**
     * Rate limiting constants
     */
    const CHATBOT_REQUESTS_PER_HOUR = 30;
    const CHATBOT_REQUESTS_PER_DAY = 200;
    
    /**
     * Security manager
     */
    private $security_manager;
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
        $this->init_security_manager();
        
        // Hook into WordPress authentication
        add_filter('rest_authentication_errors', array($this, 'rest_authentication_errors'));
    }
    
    /**
     * Initialize security manager
     */
    private function init_security_manager() {
        $options = get_option('secretary_chatbot_options', array());
        
        $this->security_manager = array(
            'rate_limiting_enabled' => true,
            'security_logging' => isset($options['security_logging']) && $options['security_logging'],
            'analytics_enabled' => isset($options['analytics_enabled']) && $options['analytics_enabled'],
            'beep_integration' => class_exists('Beep_Security')
        );
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Chatbot message endpoint
        register_rest_route(self::NAMESPACE, '/chat/message', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_chat_message'),
            'permission_callback' => array($this, 'check_chat_permissions'),
            'args' => $this->get_chat_message_args()
        ));
        
        // Session verification endpoint
        register_rest_route(self::NAMESPACE, '/auth/verify', array(
            'methods' => 'POST',
            'callback' => array($this, 'verify_session'),
            'permission_callback' => array($this, 'check_basic_permissions'),
        ));
        
        // Analytics endpoint (admin only)
        register_rest_route(self::NAMESPACE, '/analytics/interactions', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_analytics'),
            'permission_callback' => array($this, 'check_admin_permissions'),
        ));
    }
    
    /**
     * Handle chat message endpoint
     */
    public function handle_chat_message($request) {
        $message = sanitize_text_field($request->get_param('message'));
        
        // Input validation
        if (empty($message) || strlen($message) > 500) {
            return new WP_Error('invalid_input', 'Invalid message length', array('status' => 400));
        }
        
        // Rate limiting
        if (!$this->check_rate_limits()) {
            return new WP_Error('rate_limit_exceeded', 'Rate limit exceeded', array('status' => 429));
        }
        
        // Get user preferences
        $course = sanitize_text_field($request->get_param('course')) ?: 'us';
        $answer_length = sanitize_text_field($request->get_param('answer_length')) ?: 'long';

        // Generate response using chatbot logic
        include_once SECRETARY_CHATBOT_PLUGIN_PATH . 'includes/chatbot-logic.php';
        $response = process_chatbot_message($message, $course, $answer_length);
        
        // Log interaction
        $this->log_interaction($message, $response);
        
        return rest_ensure_response(array(
            'response' => $response,
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ));
    }
    
    /**
     * Verify session endpoint
     */
    public function verify_session($request) {
        $user_id = get_current_user_id();
        
        if (!$user_id) {
            return new WP_Error('not_authenticated', 'User not authenticated', array('status' => 401));
        }
        
        return rest_ensure_response(array(
            'authenticated' => true,
            'user_id' => $user_id,
            'user_login' => wp_get_current_user()->user_login,
            'capabilities' => array(
                'can_use_chatbot' => $this->can_use_chatbot(),
                'is_admin' => current_user_can('manage_options')
            )
        ));
    }
    
    /**
     * Get analytics data
     */
    public function get_analytics($request) {
        $interactions = get_option('secretary_chatbot_interactions', array());
        
        // Basic analytics
        $analytics = array(
            'total_interactions' => count($interactions),
            'unique_users' => count(array_unique(array_column($interactions, 'user_id'))),
            'response_types' => array_count_values(array_column($interactions, 'response_type')),
            'recent_interactions' => array_slice($interactions, -50) // Last 50
        );
        
        return rest_ensure_response($analytics);
    }
    
    /**
     * Check chat permissions
     */
    public function check_chat_permissions($request) {
        // Always allow access - no authentication required
        return true;
    }
    
    /**
     * Check basic permissions
     */
    public function check_basic_permissions($request) {
        return is_user_logged_in();
    }
    
    /**
     * Check admin permissions
     */
    public function check_admin_permissions($request) {
        return current_user_can('manage_options');
    }
    
    /**
     * Check if user can use chatbot
     */
    private function can_use_chatbot() {
        return current_user_can('read');
    }
    
    /**
     * Check if chatbot is enabled
     */
    private function is_chatbot_enabled() {
        $options = get_option('secretary_chatbot_options');
        return isset($options['enable_chatbot']) && $options['enable_chatbot'];
    }
    
    /**
     * Rate limiting check
     */
    private function check_rate_limits() {
        $user_id = get_current_user_id();
        $rate_key = 'secretary_chat_rate_' . $user_id;
        $current_count = get_transient($rate_key);
        
        if ($current_count === false) {
            set_transient($rate_key, 1, HOUR_IN_SECONDS);
            return true;
        }
        
        if ($current_count >= self::CHATBOT_REQUESTS_PER_HOUR) {
            $this->log_security_event('rate_limit_exceeded', 'User exceeded rate limit', array(
                'user_id' => $user_id,
                'count' => $current_count
            ));
            return false;
        }
        
        set_transient($rate_key, $current_count + 1, HOUR_IN_SECONDS);
        return true;
    }
    
    /**
     * Get chat message arguments
     */
    private function get_chat_message_args() {
        return array(
            'message' => array(
                'required' => true,
                'type' => 'string',
                'description' => 'The chat message',
                'validate_callback' => function($param) {
                    return is_string($param) && strlen($param) <= 500;
                }
            ),
            'course' => array(
                'required' => false,
                'type' => 'string',
                'description' => 'Course preference: us or texas',
                'default' => 'us',
                'enum' => array('us', 'texas'),
                'validate_callback' => function($param) {
                    return in_array($param, array('us', 'texas'));
                }
            ),
            'answer_length' => array(
                'required' => false,
                'type' => 'string',
                'description' => 'Answer length preference: short or long',
                'default' => 'long',
                'enum' => array('short', 'long'),
                'validate_callback' => function($param) {
                    return in_array($param, array('short', 'long'));
                }
            )
        );
    }
    
    /**
     * Log security events
     */
    private function log_security_event($event_type, $message, $context = array()) {
        if (!$this->security_manager['security_logging']) {
            return;
        }
        
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'event_type' => $event_type,
            'message' => $message,
            'user_id' => get_current_user_id(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'context' => $context
        );
        
        error_log('Secretary Chatbot Security: ' . json_encode($log_entry));
        
        // Use BEEP logging if available
        if (class_exists('Beep_Database')) {
            Beep_Database::log_security_event('secretary_chatbot', $log_entry);
        }
    }
    
    /**
     * Log interaction
     */
    private function log_interaction($message, $response) {
        if (!$this->security_manager['analytics_enabled']) {
            return;
        }
        
        $interaction = array(
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'message_length' => strlen($message),
            'response_type' => $this->classify_response($response),
            'session_id' => session_id()
        );
        
        $interactions = get_option('secretary_chatbot_interactions', array());
        $interactions[] = $interaction;
        
        // Keep only last 1000 interactions
        if (count($interactions) > 1000) {
            $interactions = array_slice($interactions, -1000);
        }
        
        update_option('secretary_chatbot_interactions', $interactions);
    }
    
    /**
     * Classify response type
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
    

    /**
     * REST authentication errors filter
     */
    public function rest_authentication_errors($result) {
        // Only handle our namespace
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/secretary/v1/') === false) {
            return $result;
        }

        // Allow all access - no authentication required for chatbot
        return $result;
    }
}

// Initialize the REST API authentication
new Secretary_REST_Auth();
