# Secretary of DeCourse WordPress Chatbot Plugin

A professional WordPress plugin that integrates the Secretary of DeCourse American Government teaching assistant directly into your WordPress site.

## 🚀 Features

- **WordPress Native Integration** - Fully integrated with WordPress security and permissions
- **AJAX-Powered Chat** - Real-time messaging without page reloads
- **Multiple Display Modes** - Floating widget or shortcode embedding
- **Educational Focus** - Specialized in American Government topics
- **Text-to-Speech** - Built-in speech synthesis for accessibility
- **Mobile Responsive** - Works perfectly on all devices
- **Genially Compatible** - Perfect for embedding in presentations
- **Admin Dashboard** - Easy configuration through WordPress admin

## 📦 Installation

### Method 1: Direct Upload (Recommended)

1. **Download the plugin folder** (`secretary-chatbot-plugin`)
2. **Upload to WordPress:**
   ```
   /wp-content/plugins/secretary-chatbot-plugin/
   ```
3. **Add the badger image:**
   - Save your badger.png image to: `/wp-content/plugins/secretary-chatbot-plugin/assets/badger.png`
4. **Activate the plugin** in WordPress Admin → Plugins
5. **Configure settings** in WordPress Admin → Settings → Secretary Chatbot

### Method 2: ZIP Installation

1. **Create a ZIP file** of the `secretary-chatbot-plugin` folder
2. **Upload via WordPress Admin:**
   - Go to Plugins → Add New → Upload Plugin
   - Choose your ZIP file and install
3. **Add the badger image** as described above
4. **Activate and configure**

## ⚙️ Configuration

### WordPress Admin Settings

1. Go to **Settings → Secretary Chatbot**
2. **Enable the chatbot** by checking the checkbox
3. **Choose display mode:**
   - **Floating Widget:** Shows a floating icon on all pages
   - **Shortcode Only:** Only appears when you use the shortcode

### Display Options

#### Floating Widget
- Automatically appears on all pages when enabled
- Shows as a floating badger icon in bottom-right corner
- Clicking opens the chat window

#### Shortcode Usage
```
[secretary_chatbot]
[secretary_chatbot width="500" height="700"]
[secretary_chatbot width="300" height="500"]
```

## 🎓 Genially Integration

### For Genially Presentations:

1. **Create a dedicated page** in WordPress with the shortcode:
   ```
   [secretary_chatbot width="400" height="600"]
   ```

2. **Get the page URL** (e.g., `https://yoursite.com/chatbot-page/`)

3. **In Genially:**
   - Add an iframe element
   - Set source to your page URL
   - Set dimensions to match your shortcode (400x600px)

### Alternative: Direct Embed
If your hosting allows, you can also embed the chatbot directly in Genially using the page URL.

## 🔧 Customization

### Styling
- All styles are prefixed with `secretary-` to avoid conflicts
- Modify `/assets/style.css` for custom styling
- Colors use the theme: Dark blue (#1e3a8a), white background, black text

### Content
- Edit `/includes/chatbot-logic.php` to modify responses
- Add new prompts and replies in the arrays
- Customize government-related content

### Images
- Replace `/assets/badger.png` with your custom avatar
- Replace `/assets/user.png` for user avatars
- Images should be square (recommended: 100x100px minimum)

## 🛡️ Security Features

### **WordPress REST API Authentication**
- **Modern REST API** - Uses WordPress REST API with proper authentication
- **Rate Limiting** - 30 messages per hour per user to prevent abuse
- **Input Validation** - Message length and content filtering
- **Security Logging** - All access attempts and security events logged

### **BEEP Plugin Integration**
- **Enhanced Security** - Integrates with BEEP Assessment plugin security
- **AI Access Control** - Uses BEEP's AI feature access controls
- **Unified Logging** - Security events logged to BEEP's system
- **Permission Inheritance** - Follows BEEP's user permission patterns

### **WordPress Security Standards**
- **Nonce Protection** - CSRF protection for all requests
- **Input Sanitization** - All user inputs properly sanitized
- **Capability Checks** - WordPress user capability validation
- **Direct Access Prevention** - All PHP files protected from direct access

## 📱 Mobile Support

- Fully responsive design
- Touch-friendly interface
- Optimized for mobile screens
- Maintains functionality across all devices

## 🔊 Accessibility

- **Text-to-Speech** - Automatic speech synthesis for bot responses
- **Keyboard Navigation** - Full keyboard support
- **Screen Reader Friendly** - Proper ARIA labels and semantic HTML
- **High Contrast** - Clear visual design for readability

## 🐛 Troubleshooting

### Common Issues

1. **Chatbot not appearing:**
   - Check if plugin is activated
   - Verify settings are enabled
   - Clear any caching plugins

2. **Images not loading:**
   - Ensure badger.png is in `/assets/` folder
   - Check file permissions (644 for files, 755 for folders)
   - Verify image file format (PNG recommended)

3. **AJAX errors:**
   - Check WordPress debug logs
   - Verify nonce security is working
   - Test with default WordPress theme

### Debug Mode
Add this to your `wp-config.php` for debugging:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📊 Performance

- **Lightweight** - Minimal resource usage
- **Cached Assets** - CSS/JS files are properly cached
- **Optimized AJAX** - Efficient server communication
- **No Database Bloat** - Minimal database storage

## 🔄 Updates

To update the plugin:
1. Backup your current installation
2. Replace plugin files with new version
3. Keep your custom `badger.png` image
4. Reactivate if necessary

## 📞 Support

- Check WordPress debug logs for errors
- Verify all file permissions are correct
- Test with a default WordPress theme
- Ensure no plugin conflicts exist

## 📄 License

GPL v2 or later - Compatible with WordPress licensing

## 🎯 Educational Use

Perfect for:
- Educational institutions
- Government courses
- Civic education websites
- Online learning platforms
- Student engagement tools

---

**Ready to enhance your WordPress site with an intelligent American Government teaching assistant!** 🇺🇸
