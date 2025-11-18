# Simple Registration Portal Deployment Script
Write-Host "🚀 Registration Portal Deployment" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green

$source = "C:\Users\yuvan\OneDrive\Desktop\web2"

Write-Host "Choose deployment option:" -ForegroundColor Yellow
Write-Host "1. Deploy to XAMPP (Local Testing)" -ForegroundColor White
Write-Host "2. Create ZIP for hosting" -ForegroundColor White

$choice = Read-Host "Enter choice (1 or 2)"

if ($choice -eq "1") {
    Write-Host "Deploying to XAMPP..." -ForegroundColor Yellow
    
    if (Test-Path "C:\xampp") {
        $destination = "C:\xampp\htdocs\registration-portal"
        
        if (Test-Path $destination) {
            Remove-Item -Path $destination -Recurse -Force
        }
        
        Copy-Item -Path $source -Destination $destination -Recurse -Force
        
        # Create required directories
        $dirs = @("data", "uploads", "logs", "tmp")
        foreach ($dir in $dirs) {
            $dirPath = Join-Path $destination $dir
            if (!(Test-Path $dirPath)) {
                New-Item -ItemType Directory -Path $dirPath -Force | Out-Null
            }
        }
        
        Write-Host "✅ Deployed successfully!" -ForegroundColor Green
        Write-Host "🌐 Visit: http://localhost/registration-portal/" -ForegroundColor Cyan
        
        Start-Process "http://localhost/registration-portal/"
        
    } else {
        Write-Host "❌ XAMPP not found. Please install XAMPP first." -ForegroundColor Red
        Write-Host "📥 Download from: https://www.apachefriends.org/" -ForegroundColor Yellow
        Start-Process "https://www.apachefriends.org/"
    }
}
elseif ($choice -eq "2") {
    Write-Host "Creating ZIP for hosting..." -ForegroundColor Yellow
    
    $zipPath = "$env:USERPROFILE\Desktop\registration-portal.zip"
    
    if (Test-Path $zipPath) {
        Remove-Item $zipPath -Force
    }
    
    Compress-Archive -Path "$source\*" -DestinationPath $zipPath -Force
    
    Write-Host "✅ ZIP created: $zipPath" -ForegroundColor Green
    Write-Host "📤 Upload this to your hosting provider" -ForegroundColor Yellow
    
    Start-Process "explorer.exe" "/select,$zipPath"
}
else {
    Write-Host "❌ Invalid choice" -ForegroundColor Red
}

Read-Host "Press Enter to exit"