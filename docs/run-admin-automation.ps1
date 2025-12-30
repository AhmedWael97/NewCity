# Admin Panel Automation Script for Windows
# This script automates the admin panel testing using Laravel Dusk

param(
    [string]$Test = "all",          # Test to run: all, login, users, shops, cities, categories, dashboard, complete
    [bool]$Headless = $true,        # Run in headless mode
    [int]$Slow = 0,                 # Slow down automation (milliseconds)
    [bool]$SetupEnvironment = $false # Setup test environment before running
)

Write-Host "🚀 Admin Panel Automation Script" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green

# Function to check if a command exists
function Test-Command($cmdname) {
    return [bool](Get-Command -Name $cmdname -ErrorAction SilentlyContinue)
}

# Check prerequisites
Write-Host "🔍 Checking prerequisites..." -ForegroundColor Yellow

if (-not (Test-Command "php")) {
    Write-Host "❌ PHP is not installed or not in PATH" -ForegroundColor Red
    exit 1
}

if (-not (Test-Command "composer")) {
    Write-Host "❌ Composer is not installed or not in PATH" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path "artisan")) {
    Write-Host "❌ Laravel project not found. Please run this script from the Laravel project root." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Prerequisites check passed" -ForegroundColor Green

# Setup environment if requested
if ($SetupEnvironment) {
    Write-Host "🔧 Setting up test environment..." -ForegroundColor Yellow
    
    # Copy .env.example to .env.dusk.local if it doesn't exist
    if (-not (Test-Path ".env.dusk.local")) {
        if (Test-Path ".env.example") {
            Copy-Item ".env.example" ".env.dusk.local"
            Write-Host "📝 Created .env.dusk.local from .env.example" -ForegroundColor Green
        } else {
            Write-Host "⚠️ .env.example not found, please create .env.dusk.local manually" -ForegroundColor Yellow
        }
    }
    
    # Install dependencies
    Write-Host "📦 Installing dependencies..." -ForegroundColor Yellow
    & composer install --dev
    
    # Generate application key
    Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
    & php artisan key:generate --env=dusk.local
    
    # Run migrations
    Write-Host "🗄️ Running database migrations..." -ForegroundColor Yellow
    & php artisan migrate --env=dusk.local --force
    
    Write-Host "✅ Environment setup completed" -ForegroundColor Green
}

# Start the application server in background
Write-Host "🌐 Starting Laravel development server..." -ForegroundColor Yellow
$serverJob = Start-Job -ScriptBlock {
    Set-Location $using:PWD
    & php artisan serve --env=dusk.local --port=8000
}

# Wait a bit for server to start
Start-Sleep -Seconds 3

# Check if server is running
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "✅ Laravel server is running on http://localhost:8000" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to start Laravel server" -ForegroundColor Red
    Stop-Job $serverJob -ErrorAction SilentlyContinue
    Remove-Job $serverJob -ErrorAction SilentlyContinue
    exit 1
}

try {
    # Display configuration
    Write-Host "`n📋 Automation Configuration:" -ForegroundColor Cyan
    Write-Host "   Test Suite: $Test" -ForegroundColor White
    Write-Host "   Headless Mode: $Headless" -ForegroundColor White
    Write-Host "   Slow Mode: ${Slow}ms" -ForegroundColor White
    Write-Host ""

    # Prepare arguments for artisan command
    $arguments = @("admin:automate")
    $arguments += "--test=$Test"
    $arguments += "--headless=$($Headless.ToString().ToLower())"
    $arguments += "--slow=$Slow"

    # Run the automation
    Write-Host "🎯 Starting admin panel automation..." -ForegroundColor Yellow
    & php artisan @arguments

    if ($LASTEXITCODE -eq 0) {
        Write-Host "`n✅ Automation completed successfully!" -ForegroundColor Green
        
        # Generate report
        $reportPath = "storage/logs/admin-automation-report-$(Get-Date -Format 'yyyy-MM-dd-HH-mm-ss').txt"
        Write-Host "📄 Generating report at: $reportPath" -ForegroundColor Yellow
        
        @"
Admin Panel Automation Report
Generated: $(Get-Date)
Test Suite: $Test
Headless Mode: $Headless
Slow Mode: ${Slow}ms

Status: SUCCESS
Server: http://localhost:8000
Environment: dusk.local

All tests completed successfully.
"@ | Out-File -FilePath $reportPath -Encoding UTF8
        
        Write-Host "✅ Report saved to: $reportPath" -ForegroundColor Green
    } else {
        Write-Host "`n❌ Automation failed with exit code: $LASTEXITCODE" -ForegroundColor Red
    }

} finally {
    # Clean up: Stop the server
    Write-Host "`n🧹 Cleaning up..." -ForegroundColor Yellow
    Stop-Job $serverJob -ErrorAction SilentlyContinue
    Remove-Job $serverJob -ErrorAction SilentlyContinue
    Write-Host "✅ Laravel server stopped" -ForegroundColor Green
}

Write-Host "`n🎉 Admin Panel Automation Script Completed" -ForegroundColor Green

# Usage examples
Write-Host "`n💡 Usage Examples:" -ForegroundColor Cyan
Write-Host "   .\run-admin-automation.ps1 -Test all -Headless `$false" -ForegroundColor White
Write-Host "   .\run-admin-automation.ps1 -Test login -Slow 1000" -ForegroundColor White
Write-Host "   .\run-admin-automation.ps1 -Test users -SetupEnvironment `$true" -ForegroundColor White
Write-Host "   .\run-admin-automation.ps1 -Test complete" -ForegroundColor White