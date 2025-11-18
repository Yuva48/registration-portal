# 🚀 Registration Portal Deployment Helper
# Quick deployment script for Windows PowerShell

Write-Host "=" * 60 -ForegroundColor Cyan
Write-Host "🎯 REGISTRATION PORTAL DEPLOYMENT HELPER" -ForegroundColor Green
Write-Host "=" * 60 -ForegroundColor Cyan

$source = "C:\Users\yuvan\OneDrive\Desktop\web2"

Write-Host ""
Write-Host "📋 Choose your deployment option:" -ForegroundColor Yellow
Write-Host "1. 🏠 Local Testing (XAMPP)" -ForegroundColor White
Write-Host "2. 📦 Create ZIP for Hosting Upload" -ForegroundColor White
Write-Host "3. 🌐 FTP Preparation Checklist" -ForegroundColor White
Write-Host "4. 🔧 Fix Permissions (if needed)" -ForegroundColor White
Write-Host "5. ✅ Test Local Installation" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Enter your choice (1-5)"

switch ($choice) {
    1 {
        Write-Host "🏠 Deploying to XAMPP..." -ForegroundColor Yellow
        
        if (Test-Path "C:\xampp") {
            $xamppDest = "C:\xampp\htdocs\registration-portal"
            
            # Remove existing if present
            if (Test-Path $xamppDest) {
                Remove-Item -Path $xamppDest -Recurse -Force
                Write-Host "🗑️ Removed existing installation" -ForegroundColor Gray
            }
            
            # Copy files
            Copy-Item -Path $source -Destination $xamppDest -Recurse -Force
            
            # Create required directories
            $dirs = @("data", "uploads", "logs", "tmp")
            foreach ($dir in $dirs) {
                $dirPath = Join-Path $xamppDest $dir
                if (!(Test-Path $dirPath)) {
                    New-Item -ItemType Directory -Path $dirPath -Force | Out-Null
                    Write-Host "📁 Created directory: $dir" -ForegroundColor Gray
                }
            }
            
            Write-Host "✅ Successfully deployed to XAMPP!" -ForegroundColor Green
            Write-Host "🌐 Visit: http://localhost/registration-portal/" -ForegroundColor Cyan
            Write-Host "📂 Files location: $xamppDest" -ForegroundColor Gray
            
            # Ask if user wants to open browser
            $openBrowser = Read-Host "Open in browser now? (y/n)"
            if ($openBrowser -eq 'y' -or $openBrowser -eq 'Y') {
                Start-Process "http://localhost/registration-portal/"
            }
            
        } else {
            Write-Host "❌ XAMPP not found at C:\xampp" -ForegroundColor Red
            Write-Host "📥 Please install XAMPP from: https://www.apachefriends.org/" -ForegroundColor Yellow
            
            $downloadXampp = Read-Host "Open XAMPP download page? (y/n)"
            if ($downloadXampp -eq 'y' -or $downloadXampp -eq 'Y') {
                Start-Process "https://www.apachefriends.org/"
            }
        }
    }
    
    2 {
        Write-Host "📦 Creating ZIP for hosting upload..." -ForegroundColor Yellow
        
        $zipPath = "$env:USERPROFILE\Desktop\registration-portal-deployment.zip"
        
        # Remove existing ZIP if present
        if (Test-Path $zipPath) {
            Remove-Item $zipPath -Force
        }
        
        # Create ZIP excluding unnecessary files
        $excludePatterns = @("*.log", "*.tmp", ".git*", "node_modules", ".env")
        
        # Get all files except excluded ones
        $files = Get-ChildItem -Path $source -Recurse | Where-Object {
            $exclude = $false
            foreach ($pattern in $excludePatterns) {
                if ($_.Name -like $pattern) {
                    $exclude = $true
                    break
                }
            }
            return -not $exclude
        }
        
        # Create ZIP
        Compress-Archive -Path "$source\*" -DestinationPath $zipPath -Force
        
        $zipSize = (Get-Item $zipPath).Length / 1MB
        
        Write-Host "✅ ZIP file created successfully!" -ForegroundColor Green
        Write-Host "📁 Location: $zipPath" -ForegroundColor Cyan
        Write-Host "📊 Size: $([math]::Round($zipSize, 2)) MB" -ForegroundColor Gray
        Write-Host ""
        Write-Host "📤 Next steps:" -ForegroundColor Yellow
        Write-Host "1. Login to your hosting control panel" -ForegroundColor White
        Write-Host "2. Go to File Manager" -ForegroundColor White
        Write-Host "3. Upload this ZIP to public_html/" -ForegroundColor White
        Write-Host "4. Extract the ZIP file" -ForegroundColor White
        Write-Host "5. Set permissions (see option 3)" -ForegroundColor White
        
        $openFolder = Read-Host "Open folder with ZIP file? (y/n)"
        if ($openFolder -eq 'y' -or $openFolder -eq 'Y') {
            Start-Process "explorer.exe" "/select,$zipPath"
        }
    }
    
    3 {
        Write-Host "🌐 FTP/Hosting Deployment Checklist" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "📋 Pre-Upload Steps:" -ForegroundColor Cyan
        Write-Host "✓ Purchase hosting with PHP 7.4+ support" -ForegroundColor White
        Write-Host "✓ Note down FTP credentials from hosting panel" -ForegroundColor White
        Write-Host "✓ Download FileZilla FTP client (if needed)" -ForegroundColor White
        Write-Host ""
        Write-Host "📤 Upload Instructions:" -ForegroundColor Cyan
        Write-Host "1. Connect to your hosting via FTP or File Manager" -ForegroundColor White
        Write-Host "2. Upload ALL files to public_html/ directory" -ForegroundColor White
        Write-Host "3. Ensure index.html is in the root (public_html/index.html)" -ForegroundColor White
        Write-Host ""
        Write-Host "🔒 Set These Permissions:" -ForegroundColor Cyan
        Write-Host "📁 Folders: 755 (rwxr-xr-x)" -ForegroundColor White
        Write-Host "📄 Files: 644 (rw-r--r--)" -ForegroundColor White
        Write-Host "📁 data/: 777 (rwxrwxrwx)" -ForegroundColor White
        Write-Host "📁 uploads/: 777 (rwxrwxrwx)" -ForegroundColor White
        Write-Host "📁 logs/: 777 (rwxrwxrwx)" -ForegroundColor White
        Write-Host "📁 tmp/: 777 (rwxrwxrwx)" -ForegroundColor White
        Write-Host ""
        Write-Host "⚙️ Configuration:" -ForegroundColor Cyan
        Write-Host "1. Edit process.php file" -ForegroundColor White
        Write-Host "2. Update email settings with your domain email" -ForegroundColor White
        Write-Host "3. Test form submission" -ForegroundColor White
        Write-Host "4. Check data/ folder for submissions" -ForegroundColor White
        Write-Host ""
        Write-Host "🎯 Popular Hosting Recommendations:" -ForegroundColor Yellow
        Write-Host "• Hostinger.com (`$1.99/month) - Best value" -ForegroundColor White
        Write-Host "• SiteGround.com (`$3.99/month) - Best performance" -ForegroundColor White
        Write-Host "• InfinityFree.net (Free) - For testing" -ForegroundColor White
        
        $showHosting = Read-Host "Open hosting recommendations? (y/n)"
        if ($showHosting -eq 'y' -or $showHosting -eq 'Y') {
            Start-Process "https://www.hostinger.com/"
        }
    }
    
    4 {
        Write-Host "🔧 Fixing file permissions..." -ForegroundColor Yellow
        
        if (Test-Path "C:\xampp\htdocs\registration-portal") {
            $appPath = "C:\xampp\htdocs\registration-portal"
            
            # Create directories if they don't exist
            $dirs = @("data", "uploads", "logs", "tmp")
            foreach ($dir in $dirs) {
                $dirPath = Join-Path $appPath $dir
                if (!(Test-Path $dirPath)) {
                    New-Item -ItemType Directory -Path $dirPath -Force | Out-Null
                    Write-Host "📁 Created directory: $dir" -ForegroundColor Green
                }
                
                # Set permissions (Windows equivalent)
                try {
                    $acl = Get-Acl $dirPath
                    $accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Users", "FullControl", "Allow")
                    $acl.SetAccessRule($accessRule)
                    Set-Acl $dirPath $acl
                    Write-Host "🔒 Set permissions for: $dir" -ForegroundColor Green
                } catch {
                    Write-Host "⚠️ Could not set permissions for: $dir" -ForegroundColor Yellow
                }
            }
            
            Write-Host "✅ Permissions updated!" -ForegroundColor Green
        } else {
            Write-Host "❌ Local installation not found" -ForegroundColor Red
            Write-Host "Run option 1 first to deploy locally" -ForegroundColor Yellow
        }
    }
    
    5 {
        Write-Host "🧪 Testing local installation..." -ForegroundColor Yellow
        
        if (Test-Path "C:\xampp\htdocs\registration-portal\index.html") {
            Write-Host "✅ Files found!" -ForegroundColor Green
            
            # Check if XAMPP is running
            $xamppRunning = Get-Process "httpd" -ErrorAction SilentlyContinue
            if ($xamppRunning) {
                Write-Host "✅ Apache is running!" -ForegroundColor Green
                
                # Test if localhost responds
                try {
                    $response = Invoke-WebRequest -Uri "http://localhost/registration-portal/" -TimeoutSec 5 -ErrorAction Stop
                    Write-Host "✅ Application is accessible!" -ForegroundColor Green
                    Write-Host "🌐 URL: http://localhost/registration-portal/" -ForegroundColor Cyan
                    
                    $openApp = Read-Host "Open application in browser? (y/n)"
                    if ($openApp -eq 'y' -or $openApp -eq 'Y') {
                        Start-Process "http://localhost/registration-portal/"
                    }
                } catch {
                    Write-Host "❌ Cannot access application" -ForegroundColor Red
                    Write-Host "Check XAMPP Apache service" -ForegroundColor Yellow
                }
            } else {
                Write-Host "❌ Apache is not running!" -ForegroundColor Red
                Write-Host "Start Apache in XAMPP Control Panel" -ForegroundColor Yellow
                
                $openXampp = Read-Host "Open XAMPP Control Panel? (y/n)"
                if ($openXampp -eq 'y' -or $openXampp -eq 'Y') {
                    Start-Process "C:\xampp\xampp-control.exe" -ErrorAction SilentlyContinue
                }
            }
        } else {
            Write-Host "❌ Local installation not found!" -ForegroundColor Red
            Write-Host "Run option 1 first to deploy locally" -ForegroundColor Yellow
        }
    }
    
    default {
        Write-Host "❌ Invalid choice. Please run the script again." -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=" * 60 -ForegroundColor Cyan
Write-Host "🎉 Registration Portal Deployment Helper Complete!" -ForegroundColor Green
Write-Host "=" * 60 -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to exit"