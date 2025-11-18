# Render.com Deployment Configuration

## Quick Deploy to Render (90 Days FREE!)

### Step 1: Create Render Account
1. Go to: https://render.com
2. Click "Get Started" 
3. Sign up with GitHub (recommended)
4. **No credit card required for 90 days!**

### Step 2: Create Web Service

1. **In Render Dashboard:**
   - Click **"New +"**
   - Select **"Web Service"**
   - Choose **"Build and deploy from a Git repository"**

2. **Connect Repository:**
   - If using GitHub: Connect your GitHub account
   - Select your repository: `registration-portal`
   - Or use public repo if you created one

3. **Configure Service:**
   - **Name**: `registration-portal`
   - **Environment**: `PHP`
   - **Build Command**: `chmod +x build.sh && ./build.sh`
   - **Start Command**: `php -S 0.0.0.0:10000 index.php`
   - **Advanced** → **Add Environment Variable**:
     - `APP_ENV` = `production`
     - `APP_DEBUG` = `false`

### Step 3: Deploy!
- Click **"Create Web Service"**
- Render will automatically build and deploy
- Your app will be live at: `https://your-app-name.onrender.com`

## Features on Render:
- ✅ **90 days completely free**
- ✅ **No credit card required initially**
- ✅ **Automatic HTTPS**
- ✅ **Custom domains**
- ✅ **PostgreSQL database** (if needed later)
- ✅ **Automatic deployments** from GitHub
- ✅ **Persistent storage** available

## Repository Setup (If Needed):

### Option A: Create GitHub Repository
```bash
# Install GitHub CLI if you haven't
winget install GitHub.cli

# Create repo and push
gh repo create registration-portal --public
git remote add origin https://github.com/yourusername/registration-portal.git
git branch -M main
git push -u origin main
```

### Option B: Manual Upload
1. Create repository on GitHub.com
2. Upload your files via web interface
3. Use that repository URL in Render

## Environment Variables (Optional):
- `APP_ENV`: production
- `APP_DEBUG`: false
- `MAX_FILE_SIZE`: 2048000
- `ALLOWED_FILE_TYPES`: jpg,jpeg,png,gif,pdf,doc,docx

## Troubleshooting:
- **Build fails**: Check build logs in Render dashboard
- **App won't start**: Verify start command is correct
- **Files not persisting**: Consider upgrading to paid plan for persistent disk

## Support:
- Render Docs: https://render.com/docs
- Community: https://community.render.com/