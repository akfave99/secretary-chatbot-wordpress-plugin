<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$width = isset($atts['width']) ? $atts['width'] : '400';
$height = isset($atts['height']) ? $atts['height'] : '600';
?>

<!-- Secretary of DeCourse Embedded Chatbot -->
<div class="secretary-embedded-chatbot" style="width: <?php echo esc_attr($width); ?>px; height: <?php echo esc_attr($height); ?>px;">
    <div class="secretary-chat-header">
        <div class="secretary-header-content">
            <img src="<?php echo SECRETARY_CHATBOT_PLUGIN_URL; ?>assets/badger.png" alt="Secretary of DeCourse" class="secretary-header-avatar">
            <div class="secretary-header-text">
                <h3>Secretary of DeCourse</h3>
                <p>American Government Assistant</p>
            </div>
        </div>
    </div>
    <div class="secretary-messages"></div>
    <div class="secretary-input-container">
        <input type="text" placeholder="Ask me about American Government..." autocomplete="off" />
        <button type="button" class="secretary-send-btn">Send</button>
    </div>
</div>
