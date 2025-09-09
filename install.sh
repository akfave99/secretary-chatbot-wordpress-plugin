#!/bin/bash

# Secretary of DeCourse WordPress Plugin Installation Script
echo "🎓 Installing Secretary of DeCourse Chatbot Plugin..."

# Check if we're in the right directory
if [ ! -f "secretary-chatbot.php" ]; then
    echo "❌ Error: secretary-chatbot.php not found. Please run this script from the plugin directory."
    exit 1
fi

# Set proper file permissions
echo "🔧 Setting file permissions..."

# Set directory permissions (755)
find . -type d -exec chmod 755 {} \;

# Set file permissions (644)
find . -type f -exec chmod 644 {} \;

# Make install script executable
chmod +x install.sh

echo "✅ File permissions set successfully!"

# Check for required files
echo "📋 Checking required files..."

required_files=(
    "secretary-chatbot.php"
    "includes/chatbot-logic.php"
    "includes/class-secretary-rest-auth.php"
    "assets/style.css"
    "assets/chatbot.js"
    "templates/floating-widget.php"
    "templates/embedded-chatbot.php"
    "admin/settings-page.php"
)

missing_files=()

for file in "${required_files[@]}"; do
    if [ ! -f "$file" ]; then
        missing_files+=("$file")
    else
        echo "✅ $file"
    fi
done

if [ ${#missing_files[@]} -gt 0 ]; then
    echo "❌ Missing required files:"
    for file in "${missing_files[@]}"; do
        echo "   - $file"
    done
    exit 1
fi

# Check for image files
echo "🖼️  Checking image files..."

if [ ! -f "assets/badger.png" ]; then
    echo "⚠️  Warning: assets/badger.png not found"
    echo "   Please add your badger avatar image to assets/badger.png"
else
    echo "✅ badger.png found"
fi

if [ ! -f "assets/user.png" ]; then
    echo "⚠️  Warning: assets/user.png not found"
    echo "   Please add a user avatar image to assets/user.png"
else
    echo "✅ user.png found"
fi

echo ""
echo "🚀 Installation complete!"
echo ""
echo "📋 Next steps:"
echo "1. Upload this entire folder to /wp-content/plugins/"
echo "2. Add badger.png to the assets/ folder if not already present"
echo "3. Activate the plugin in WordPress Admin → Plugins"
echo "4. Configure settings in WordPress Admin → Settings → Secretary Chatbot"
echo ""
echo "📖 For detailed instructions, see README.md"
echo ""
echo "🎯 Plugin ready for WordPress deployment!"
