# Railway.app Deployment Guide

## Quick Deploy to Railway (No Credit Card Required!)

### Step 1: Create Railway Account
1. Go to: https://railway.app
2. Click "Start a New Project" 
3. Sign up with GitHub (recommended) or email
4. **No payment method required!**

### Step 2: Deploy from GitHub

#### Option A: Deploy via GitHub (Recommended)
1. **Push to GitHub first:**
   ```bash
   # If you don't have a GitHub repo yet
   gh repo create registration-portal --public
   git remote add origin https://github.com/yourusername/registration-portal.git
   git branch -M main
   git push -u origin main
   ```

2. **Deploy on Railway:**
   - Go to Railway dashboard
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Choose your repository
   - Railway will auto-deploy!

#### Option B: Deploy with Railway CLI
1. **Install Railway CLI:**
   ```bash
   npm install -g @railway/cli
   ```

2. **Login and Deploy:**
   ```bash
   railway login
   railway init
   railway up
   ```

### Step 3: Your App Will Be Live!
- Railway provides a free `.railway.app` domain
- Example: `https://your-project-name.railway.app`
- SSL certificate included automatically

## Features on Railway:
- ✅ **Free $5 monthly credit** (enough for small apps)
- ✅ **No credit card required**
- ✅ **Automatic HTTPS**
- ✅ **Custom domains** (free)
- ✅ **Environment variables**
- ✅ **Automatic deployments** from GitHub
- ✅ **Build logs and monitoring**

## Alternative: Render.com
If Railway doesn't work, try Render.com:
1. Go to https://render.com
2. Sign up with GitHub
3. Create new "Web Service"
4. Connect your repository
5. Free for 90 days!

## Need Help?
If you encounter any issues:
1. Check Railway docs: https://docs.railway.app
2. Railway Discord: https://discord.gg/railway
3. Or let me know and I'll help troubleshoot!