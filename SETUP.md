# Quick Setup Guide for Registration Portal

## Option 1: XAMPP (Windows - Recommended for Beginners)

### Step 1: Download and Install XAMPP
1. Go to [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Download XAMPP for Windows
3. Install XAMPP with default settings
4. Start **Apache** and **PHP** modules from XAMPP Control Panel

### Step 2: Deploy the Application
1. Copy the entire `web2` folder to `C:\xampp\htdocs\`
2. Rename the folder from `web2` to `registration-portal`
3. The final path should be: `C:\xampp\htdocs\registration-portal\`

### Step 3: Set Permissions
1. Right-click on the `registration-portal` folder
2. Select "Properties" → "Security" tab
3. Click "Edit" and add "Full Control" for "Users"
4. Apply to all subfolders and files

### Step 4: Create Required Directories
Create these folders inside your application directory:
- `data/` (for storing submissions)
- `uploads/` (for file uploads)
- `logs/` (for application logs)
- `tmp/` (for temporary files)

### Step 5: Test the Application
1. Open your web browser
2. Navigate to: `http://localhost/registration-portal/`
3. Fill out and submit the registration form
4. Check that submissions are saved in the `data/` folder

### Step 6: Configure Email (Optional)
1. Edit `process.php` file
2. Update the email configuration section:
```php
'email' => [
    'admin_email' => 'your-email@gmail.com',
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Your Organization'
]
```

---

## Option 2: Live Hosting (Production Deployment)

### Popular Hosting Providers:

#### Budget-Friendly Options:
- **Hostinger** (hostinger.com) - $1-3/month
- **Namecheap** (namecheap.com) - $2-4/month  
- **Bluehost** (bluehost.com) - $3-5/month

#### Premium Options:
- **SiteGround** (siteground.com) - $4-8/month
- **A2 Hosting** (a2hosting.com) - $3-7/month

### Deployment Steps:

1. **Purchase Hosting Plan**
   - Choose a plan with PHP 7.4+ support
   - Ensure it includes MySQL database (optional)
   - Get SSL certificate (usually included)

2. **Upload Files**
   - Use File Manager in hosting control panel, or
   - Use FTP client like FileZilla
   - Upload all files to public_html folder

3. **Set Permissions**
   - Set folder permissions to 755
   - Set file permissions to 644
   - Set data/, uploads/, logs/, tmp/ to 777

4. **Configure Email**
   - Update email settings in `process.php`
   - Use your domain email addresses

5. **Test Everything**
   - Test form submission
   - Check email notifications
   - Verify file uploads work

---

## Option 3: Cloud Platforms (Advanced)

### Heroku (Free Tier Available)
```bash
# Install Heroku CLI, then:
heroku create your-app-name
git init
git add .
git commit -m "Initial commit"
git push heroku main
```

### Vercel (Frontend with Serverless Functions)
```bash
# Install Vercel CLI, then:
vercel --prod
```

### Netlify (Static Site with Forms)
- Drag and drop your folder to netlify.com
- Enable form handling in settings

---

## Troubleshooting

### Common Issues:

**❌ "File not found" error**
- Check file paths are correct
- Ensure index.html is in the right directory

**❌ "Permission denied" error**
- Set proper folder permissions (777 for data folders)
- Check web server user has access

**❌ "Form not submitting"**
- Check PHP is enabled on your server
- Verify process.php file exists and is readable
- Check browser console for JavaScript errors

**❌ "Emails not sending"**
- Verify PHP mail() function is enabled
- Check spam/junk folders
- Consider using SMTP instead of PHP mail

**❌ "File uploads not working"**
- Check PHP upload_max_filesize setting
- Verify uploads/ directory exists and has write permissions
- Check disk space availability

### Getting Help:
1. Check the browser console (F12) for error messages
2. Check server error logs
3. Ensure all files uploaded correctly
4. Verify PHP version is 7.4 or higher

---

## Next Steps:

1. **Customize the Design**
   - Edit `styles.css` for colors and fonts
   - Update logo and branding in `index.html`

2. **Add Your Content**
   - Modify form fields as needed
   - Update email templates
   - Add your contact information

3. **Security (Production)**
   - Enable HTTPS/SSL
   - Regular backups
   - Keep software updated
   - Monitor logs

4. **Analytics (Optional)**
   - Add Google Analytics
   - Set up form conversion tracking
   - Monitor performance

**🎉 Your professional registration portal is ready!**