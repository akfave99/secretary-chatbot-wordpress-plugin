<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="secretary-admin-notice">
        <h3>🎓 Secretary of DeCourse Chatbot</h3>
        <p>An interactive American Government teaching assistant for your WordPress site.</p>
    </div>
    
    <form action="options.php" method="post">
        <?php
        settings_fields('secretary_chatbot_options');
        do_settings_sections('secretary-chatbot');
        ?>
        
        <div class="secretary-settings-section">
            <h2>🔐 Security & Authentication</h2>
            <?php if (class_exists('Beep_Security')): ?>
                <div class="notice notice-success inline">
                    <p><strong>✅ BEEP Integration Active:</strong> Using enhanced security from BEEP Assessment plugin</p>
                </div>
            <?php else: ?>
                <div class="notice notice-info inline">
                    <p><strong>ℹ️ BEEP Integration:</strong> Install BEEP Assessment plugin for enhanced security features</p>
                </div>
            <?php endif; ?>

            <div class="notice notice-info inline">
                <p><strong>🔧 Troubleshooting:</strong> If chat isn't responding, check that:</p>
                <ul style="margin-left: 20px;">
                    <li>✅ You are logged into WordPress</li>
                    <li>✅ The plugin is activated</li>
                    <li>✅ "Enable Chatbot" is checked below</li>
                    <li>✅ Your browser console shows no JavaScript errors</li>
                </ul>
            </div>
        </div>

        <div class="secretary-settings-section">
            <h2>Display Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Chatbot</th>
                    <td>
                        <?php
                        $options = get_option('secretary_chatbot_options');
                        $checked = isset($options['enable_chatbot']) && $options['enable_chatbot'] ? 'checked' : '';
                        ?>
                        <label>
                            <input type="checkbox" name="secretary_chatbot_options[enable_chatbot]" value="1" <?php echo $checked; ?> />
                            Enable the chatbot on your website
                        </label>
                        <p class="description">When enabled, the chatbot will appear according to your display mode setting.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Display Mode</th>
                    <td>
                        <?php
                        $mode = isset($options['display_mode']) ? $options['display_mode'] : 'floating';
                        ?>
                        <select name="secretary_chatbot_options[display_mode]">
                            <option value="floating" <?php selected($mode, 'floating'); ?>>Floating Widget</option>
                            <option value="shortcode" <?php selected($mode, 'shortcode'); ?>>Shortcode Only</option>
                        </select>
                        <p class="description">
                            <strong>Floating Widget:</strong> Shows a floating icon in the bottom-right corner<br>
                            <strong>Shortcode Only:</strong> Only displays when using the [secretary_chatbot] shortcode
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="secretary-settings-section">
            <h2>Usage Instructions</h2>
            <h3>Shortcode Usage</h3>
            <p>Use the following shortcode to embed the chatbot anywhere on your site:</p>
            <code>[secretary_chatbot]</code>
            
            <h4>Shortcode Parameters:</h4>
            <ul>
                <li><code>width</code> - Width in pixels (default: 400)</li>
                <li><code>height</code> - Height in pixels (default: 600)</li>
            </ul>
            
            <h4>Examples:</h4>
            <code>[secretary_chatbot width="500" height="700"]</code><br>
            <code>[secretary_chatbot width="300" height="500"]</code>
            
            <h3>Genially Integration</h3>
            <p>To embed in Genially presentations:</p>
            <ol>
                <li>Create a page with the shortcode: <code>[secretary_chatbot width="400" height="600"]</code></li>
                <li>Copy the page URL</li>
                <li>In Genially, add an iframe element</li>
                <li>Set the iframe source to your page URL</li>
                <li>Set iframe dimensions to match your shortcode settings</li>
            </ol>
            
            <h3>Features</h3>
            <ul>
                <li>✅ American Government focused responses</li>
                <li>✅ Text-to-speech functionality</li>
                <li>✅ Mobile responsive design</li>
                <li>✅ WordPress REST API authentication</li>
                <li>✅ Rate limiting protection</li>
                <li>✅ Security event logging</li>
                <li>✅ BEEP plugin integration</li>
                <li>✅ Professional educational styling</li>
            </ul>

            <h3>🔒 Security Features</h3>
            <ul>
                <li><strong>Authentication:</strong> WordPress user login required</li>
                <li><strong>Rate Limiting:</strong> 30 messages per hour per user</li>
                <li><strong>Input Validation:</strong> Message length and content filtering</li>
                <li><strong>Security Logging:</strong> All access attempts logged</li>
                <li><strong>BEEP Integration:</strong> Enhanced AI security policies</li>
                <li><strong>REST API:</strong> Modern authentication with nonces</li>
            </ul>
        </div>
        
        <?php submit_button('Save Settings'); ?>
    </form>
    
    <div class="secretary-settings-section">
        <h2>🧪 Test Your Chatbot</h2>
        <p>Try these sample questions to test the chatbot:</p>
        <ul>
            <li>"What is the Constitution?"</li>
            <li>"Tell me about the Bill of Rights"</li>
            <li>"How does Congress work?"</li>
            <li>"What is federalism?"</li>
            <li>"Explain civil rights"</li>
        </ul>
        
        <?php if (isset($options['enable_chatbot']) && $options['enable_chatbot']): ?>
            <p><strong>✅ Chatbot is currently enabled on your site!</strong></p>
        <?php else: ?>
            <p><strong>⚠️ Chatbot is currently disabled. Enable it above to start using it.</strong></p>
        <?php endif; ?>
    </div>
</div>

<style>
.secretary-admin-notice {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.secretary-admin-notice h3 {
    color: white;
    margin-top: 0;
}

.secretary-settings-section {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.secretary-settings-section h2 {
    margin-top: 0;
    color: #1e3a8a;
    border-bottom: 2px solid #1e3a8a;
    padding-bottom: 10px;
}

.secretary-settings-section code {
    background: #f1f1f1;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}

.secretary-settings-section ul li {
    margin: 8px 0;
}
</style>
