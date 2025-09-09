# WordPress Auto-Deployment Setup Guide

This guide will help you set up automatic deployment from GitHub to your WordPress site.

## 🔧 Prerequisites

You'll need:
1. **WordPress site** (self-hosted or managed hosting)
2. **FTP/SFTP access** to your WordPress site
3. **GitHub repository** (created in Step 1)

## 📋 Step-by-Step Setup

### Step 1: Get Your WordPress Site Details

You'll need these details from your hosting provider:

#### For FTP Deployment:
- **FTP Server**: (e.g., `ftp.yoursite.com`)
- **FTP Username**: Your FTP username
- **FTP Password**: Your FTP password

#### For SFTP Deployment (More Secure):
- **SFTP Host**: (e.g., `yoursite.com`)
- **SFTP Username**: Your SSH/SFTP username  
- **SFTP Password**: Your SSH/SFTP password
- **SFTP Port**: Usually 22

### Step 2: Configure GitHub Secrets

1. Go to your GitHub repository
2. Click **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. Add these secrets:

#### For FTP:
- `FTP_SERVER`: Your FTP server address
- `FTP_USERNAME`: Your FTP username
- `FTP_PASSWORD`: Your FTP password

#### For SFTP (Alternative):
- `SFTP_HOST`: Your SFTP host
- `SFTP_USERNAME`: Your SFTP username
- `SFTP_PASSWORD`: Your SFTP password
- `SFTP_PORT`: Your SFTP port (usually 22)

### Step 3: Test the Deployment

1. **Push code to main branch**:
   ```bash
   git add .
   git commit -m "Initial plugin deployment"
   git push origin main
   ```

2. **Check GitHub Actions**:
   - Go to your repository → **Actions** tab
   - Watch the deployment workflow run
   - Check for any errors

3. **Verify on WordPress**:
   - Log into your WordPress admin
   - Go to **Plugins** → **Installed Plugins**
   - Look for "Secretary of DeCourse Chatbot"
   - **Activate** the plugin

## 🔍 Troubleshooting

### Deployment Fails
1. **Check GitHub Actions logs** for specific errors
2. **Verify FTP/SFTP credentials** in repository secrets
3. **Test FTP connection** manually with an FTP client
4. **Check file permissions** on your WordPress server

### Plugin Not Appearing
1. **Verify deployment path**: Should be `/wp-content/plugins/secretary-chatbot-plugin/`
2. **Check file permissions**: Files should be readable by web server
3. **Look for PHP errors** in WordPress debug log

### Connection Errors
1. **Enable WordPress debugging** (see README.md)
2. **Check browser console** for JavaScript errors
3. **Test AJAX endpoint** directly
4. **Review plugin activation** in WordPress admin

## 🎯 Next Steps After Setup

Once deployment is working:

1. **Test the chatbot** on your live site
2. **Check for connection errors**
3. **Monitor debug logs**
4. **Share site URL** for collaborative debugging

## 🤝 Sharing Access for Debugging

To help with debugging, you can:

1. **Share WordPress admin access** (temporary user)
2. **Provide site URL** for testing
3. **Share debug logs** when issues occur
4. **Grant repository collaborator access**

### Create Temporary WordPress User:
1. WordPress Admin → **Users** → **Add New**
2. **Username**: `github-debug-helper`
3. **Email**: `debug@yoursite.com`
4. **Role**: Administrator (temporary)
5. **Send login details** for debugging access

## 🔒 Security Notes

- **Use strong passwords** for all accounts
- **Remove temporary access** after debugging
- **Use SFTP instead of FTP** when possible
- **Keep repository secrets secure**
- **Monitor deployment logs** for sensitive data

## 📞 Support

If you need help:
1. **Check GitHub Actions logs** first
2. **Review WordPress debug logs**
3. **Test manually** with FTP client
4. **Create GitHub issue** with error details
