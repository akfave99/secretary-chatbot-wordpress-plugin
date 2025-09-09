# 🚀 Quick Setup Instructions

Follow these steps to set up automatic deployment for your Secretary of DeCourse chatbot plugin.

## Step 1: Create GitHub Repository

1. Go to [GitHub.com](https://github.com) and click **"New repository"**
2. **Repository name**: `secretary-chatbot-wordpress-plugin`
3. **Description**: "Secretary of DeCourse WordPress Chatbot Plugin with Auto-Deployment"
4. **Public** repository (so I can help debug)
5. **Initialize with README**: ✅ Yes
6. Click **"Create repository"**

## Step 2: Upload Plugin Files

1. **Download** all files from the `github-deployment-setup/` folder
2. **Upload** them to your new GitHub repository:
   - You can drag and drop files directly on GitHub.com
   - Or use Git commands if you're familiar with them

## Step 3: Configure Deployment Secrets

1. In your GitHub repository, go to **Settings** → **Secrets and variables** → **Actions**
2. Click **"New repository secret"** and add these:

### Your WordPress Hosting Details:
- `FTP_SERVER`: Your FTP server (e.g., `ftp.yoursite.com`)
- `FTP_USERNAME`: Your FTP username
- `FTP_PASSWORD`: Your FTP password

**Where to find these:**
- Check your hosting provider's control panel
- Look for "FTP Access" or "File Manager" settings
- Contact your hosting support if needed

## Step 4: Test Deployment

1. **Make a small change** to any file in your repository
2. **Commit and push** the change
3. **Go to Actions tab** in your GitHub repository
4. **Watch the deployment** run automatically
5. **Check your WordPress site** - the plugin should appear in Plugins → Installed Plugins

## Step 5: Activate and Test

1. **Log into WordPress Admin**
2. **Go to Plugins** → Installed Plugins
3. **Find "Secretary of DeCourse Chatbot"**
4. **Click "Activate"**
5. **Visit your site** - you should see the floating chatbot widget
6. **Test it** by asking "what is federalism?"

## Step 6: Share Access for Debugging

To help debug any issues:

### Option A: Share Repository Access
1. Go to your repository **Settings** → **Manage access**
2. Click **"Invite a collaborator"**
3. Add username: `akfave99` (that's you, so I can help debug)

### Option B: Share WordPress Access (Temporary)
1. **WordPress Admin** → **Users** → **Add New**
2. **Username**: `debug-helper`
3. **Email**: `debug@yoursite.com`
4. **Role**: Administrator
5. **Share login details** for debugging

## 🎯 What Happens Next

Once set up:
- **Every code change** automatically deploys to your WordPress site
- **I can help debug** connection issues in real-time
- **We can test fixes** immediately without ZIP file uploads
- **Version control** tracks all changes

## 🆘 Need Help?

If you get stuck:
1. **Check the deployment-guide.md** for detailed instructions
2. **Look at GitHub Actions logs** for error messages
3. **Share your repository URL** so I can help debug
4. **Provide your WordPress site URL** for testing

## 🔍 Common Issues

### "Deployment Failed"
- Check your FTP credentials in GitHub secrets
- Verify FTP server address is correct
- Test FTP connection with an FTP client

### "Plugin Not Appearing"
- Check if files deployed to correct path: `/wp-content/plugins/secretary-chatbot-plugin/`
- Verify file permissions on your server
- Look for PHP errors in WordPress debug log

### "Connection Errors"
- Enable WordPress debugging (see README.md)
- Check browser console for JavaScript errors
- Test the chatbot with simple questions like "hello"

Ready to get started? Let me know when you've created the repository and I'll help with the next steps!
