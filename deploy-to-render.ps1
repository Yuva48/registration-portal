# Auto-deployment script for Render.com
Write-Host "🚀 Registration Portal - Auto Deployment to Render.com" -ForegroundColor Green
Write-Host "=================================================" -ForegroundColor Yellow

# Check if GitHub CLI is available
try {
    $ghVersion = gh --version 2>$null
    Write-Host "✅ GitHub CLI is available" -ForegroundColor Green
}
catch {
    Write-Host "❌ GitHub CLI not found. Installing..." -ForegroundColor Red
    winget install GitHub.cli
    Write-Host "Please restart PowerShell and run this script again." -ForegroundColor Yellow
    exit
}

# Login to GitHub if not already logged in
Write-Host "📝 Checking GitHub authentication..." -ForegroundColor Cyan
try {
    gh auth status 2>$null
    if ($LASTEXITCODE -ne 0) {
        Write-Host "🔐 Please log in to GitHub..." -ForegroundColor Yellow
        gh auth login --web
    }
}
catch {
    Write-Host "🔐 Setting up GitHub authentication..." -ForegroundColor Yellow
    gh auth login --web
}

# Create repository
Write-Host "📁 Creating GitHub repository..." -ForegroundColor Cyan
$repoName = "registration-portal"
try {
    gh repo create $repoName --public --description "Professional Registration Portal with PHP backend and modern design" 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Repository created successfully!" -ForegroundColor Green
    }
    else {
        Write-Host "ℹ️ Repository might already exist, continuing..." -ForegroundColor Yellow
    }
}
catch {
    Write-Host "ℹ️ Repository creation skipped, continuing..." -ForegroundColor Yellow
}

# Add remote and push
Write-Host "📤 Pushing code to GitHub..." -ForegroundColor Cyan
try {
    $username = gh api user --jq '.login'
    git remote remove origin 2>$null
    git remote add origin "https://github.com/$username/$repoName.git"
    git branch -M main
    git push -u origin main --force
    
    Write-Host "✅ Code pushed to GitHub successfully!" -ForegroundColor Green
    Write-Host "📍 Repository URL: https://github.com/$username/$repoName" -ForegroundColor Cyan
}
catch {
    Write-Host "❌ Error pushing to GitHub. Please check authentication." -ForegroundColor Red
}

# Display Render deployment instructions
Write-Host ""
Write-Host "🌐 RENDER.COM DEPLOYMENT INSTRUCTIONS" -ForegroundColor Magenta
Write-Host "=====================================" -ForegroundColor Yellow
Write-Host "1. Go to: https://render.com" -ForegroundColor White
Write-Host "2. Sign up/Login with GitHub" -ForegroundColor White
Write-Host "3. Click 'New +' → 'Web Service'" -ForegroundColor White
Write-Host "4. Connect repository: $username/$repoName" -ForegroundColor Green
Write-Host "5. Configure:" -ForegroundColor White
Write-Host "   - Environment: PHP" -ForegroundColor Gray
Write-Host "   - Build Command: chmod +x build.sh && ./build.sh" -ForegroundColor Gray
Write-Host "   - Start Command: php -S 0.0.0.0:10000 index.php" -ForegroundColor Gray
Write-Host "6. Click 'Create Web Service'" -ForegroundColor White
Write-Host ""
Write-Host "🎉 Your app will be live at: https://$repoName-XXXX.onrender.com" -ForegroundColor Green

# Open Render.com
Write-Host ""
Write-Host "🌐 Opening Render.com..." -ForegroundColor Cyan
Start-Process "https://render.com"

Write-Host ""
Write-Host "✨ Deployment setup complete! Follow the instructions above to go live." -ForegroundColor Green