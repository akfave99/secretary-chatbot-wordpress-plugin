<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Secretary of DeCourse Floating Chatbot Widget -->
<div class="secretary-chat-widget">
    <!-- Floating Icon -->
    <div class="secretary-chat-icon">
        <?php
        $options = get_option('secretary_chatbot_options');
        $badger_url = isset($options['badger_image_url']) && !empty($options['badger_image_url'])
            ? $options['badger_image_url']
            : SECRETARY_CHATBOT_PLUGIN_URL . 'assets/badger.png';
        ?>
        <img src="<?php echo esc_url($badger_url); ?>" alt="Secretary of DeCourse" class="secretary-icon-image">
        <div class="secretary-pulse-ring"></div>
    </div>

    <!-- Mr. Secretary of DeCourse Title -->
    <div class="secretary-icon-title">Mr. Secretary of DeCourse</div>
    
    <!-- Chat Window -->
    <div class="secretary-chat-window hidden">
        <div class="secretary-chat-header">
            <div class="secretary-header-content">
                <img src="<?php echo esc_url($badger_url); ?>" alt="Secretary of DeCourse" class="secretary-header-avatar">
                <div class="secretary-header-text">
                    <h3>🦡 Secretary of DeCourse</h3>
                    <p class="secretary-course-subtitle">Government Assistant</p>
                </div>
            </div>
            <button type="button" class="secretary-close-btn">&times;</button>
        </div>
        <div class="secretary-messages"></div>
        <div class="secretary-input-container">
            <input type="text" placeholder="Ask me about Government..." autocomplete="off" />
            <button type="button" class="secretary-send-btn">Send</button>
        </div>
    </div>
</div>
