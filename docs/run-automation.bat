@echo off
echo.
echo ========================================
echo    Admin Panel Automation Runner
echo ========================================
echo.

REM Check if PowerShell is available
powershell -Command "Write-Host 'PowerShell is available'" >nul 2>&1
if errorlevel 1 (
    echo Error: PowerShell is not available
    echo Please install PowerShell or run the automation manually
    pause
    exit /b 1
)

echo Choose automation type:
echo.
echo 1. Complete Admin Automation (Recommended)
echo 2. Login Tests Only
echo 3. User Management Tests
echo 4. Shop Management Tests  
echo 5. City & Category Tests
echo 6. Dashboard Tests
echo 7. All Tests
echo 8. Setup Environment First
echo.

set /p choice="Enter your choice (1-8): "

if "%choice%"=="1" (
    set test=complete
) else if "%choice%"=="2" (
    set test=login
) else if "%choice%"=="3" (
    set test=users
) else if "%choice%"=="4" (
    set test=shops
) else if "%choice%"=="5" (
    set test=cities
) else if "%choice%"=="6" (
    set test=dashboard
) else if "%choice%"=="7" (
    set test=all
) else if "%choice%"=="8" (
    echo Setting up environment first...
    powershell -ExecutionPolicy Bypass -File "run-admin-automation.ps1" -Test "complete" -SetupEnvironment $true
    goto end
) else (
    echo Invalid choice. Using complete automation.
    set test=complete
)

echo.
echo Choose browser mode:
echo 1. Headless (Faster, no browser window)
echo 2. With Browser Window (Slower, you can watch)
echo.
set /p browserChoice="Enter your choice (1-2): "

if "%browserChoice%"=="2" (
    set headless=false
) else (
    set headless=true
)

echo.
echo Starting automation with:
echo - Test: %test%
echo - Headless: %headless%
echo.

powershell -ExecutionPolicy Bypass -File "run-admin-automation.ps1" -Test "%test%" -Headless $%headless%

:end
echo.
echo Automation completed!
pause