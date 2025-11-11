# Admin Panel Automation System

This automation system provides comprehensive testing for your Laravel admin panel using browser automation. It can automatically test login, user management, shop management, city/category management, and more.

## 🚀 Quick Start

### Option 1: Run with Batch File (Easiest)
```bash
# Double-click or run in Command Prompt
run-automation.bat
```

### Option 2: Run with PowerShell
```powershell
# Run complete automation
.\run-admin-automation.ps1 -Test complete

# Run specific tests
.\run-admin-automation.ps1 -Test login
.\run-admin-automation.ps1 -Test users -Headless $false

# Setup environment first
.\run-admin-automation.ps1 -SetupEnvironment $true
```

### Option 3: Run with Artisan Command
```bash
# Run complete automation
php artisan admin:automate --test=complete

# Run specific tests
php artisan admin:automate --test=login
php artisan admin:automate --test=users --headless=false
php artisan admin:automate --test=shops --slow=1000
```

### Option 4: Run with Dusk Directly
```bash
# Run all tests
php artisan dusk

# Run specific test files
php artisan dusk tests/Browser/AdminLoginTest.php
php artisan dusk tests/Browser/CompleteAdminAutomationTest.php
```

## 📋 Available Test Suites

| Test Suite | Description | Command |
|------------|-------------|---------|
| `login` | Admin login/logout functionality | `--test=login` |
| `users` | User CRUD operations, search, bulk actions | `--test=users` |
| `shops` | Shop management, verification, features | `--test=shops` |
| `cities` | City management and operations | `--test=cities` |
| `categories` | Category management and hierarchy | `--test=categories` |
| `dashboard` | Dashboard overview and navigation | `--test=dashboard` |
| `complete` | Complete automation workflow | `--test=complete` |
| `all` | All individual test suites | `--test=all` |

## ⚙️ Configuration Options

### Headless Mode
- `--headless=true` (default): Runs browser in background
- `--headless=false`: Shows browser window (good for debugging)

### Slow Mode
- `--slow=0` (default): Normal speed
- `--slow=1000`: 1 second delay between actions
- `--slow=2000`: 2 second delay (useful for watching)

## 🔧 Setup Requirements

### Prerequisites
- PHP 8.2+
- Composer
- Laravel 11+
- Chrome browser
- PowerShell (for Windows automation scripts)

### Initial Setup
1. Install Laravel Dusk:
   ```bash
   composer require --dev laravel/dusk
   php artisan dusk:install
   ```

2. Create test environment file:
   ```bash
   cp .env.example .env.dusk.local
   ```

3. Configure test database in `.env.dusk.local`:
   ```env
   DB_DATABASE=your_test_database
   APP_URL=http://localhost:8000
   ```

4. Run migrations:
   ```bash
   php artisan migrate --env=dusk.local
   ```

## 🎯 What Gets Tested

### Admin Login Tests
- ✅ Valid admin login
- ✅ Invalid credentials handling
- ✅ Login redirects
- ✅ Logout functionality

### User Management Tests
- ✅ View users list
- ✅ Create new user
- ✅ Edit existing user
- ✅ Delete user
- ✅ Search users
- ✅ Bulk actions
- ✅ Toggle user status

### Shop Management Tests
- ✅ View shops list
- ✅ Create new shop
- ✅ Edit shop details
- ✅ Delete shop
- ✅ Verify/unverify shops
- ✅ Feature/unfeature shops
- ✅ Search shops
- ✅ Bulk operations
- ✅ Pending shops review

### City & Category Management Tests
- ✅ CRUD operations for cities
- ✅ CRUD operations for categories
- ✅ Toggle active status
- ✅ Search functionality
- ✅ Category hierarchy

### Dashboard Tests
- ✅ Dashboard loading
- ✅ Navigation links
- ✅ Statistics display
- ✅ System health check
- ✅ Responsive design

### Complete Automation Workflow
- ✅ Full end-to-end testing
- ✅ Multi-step workflows
- ✅ Real user scenarios
- ✅ Error handling

## 📊 Sample Output

```
🚀 Starting Admin Panel Automation...

Configuration:
  Test Suite: complete
  Headless Mode: Yes
  Slow Mode: 0ms

🔑 Step 1: Admin Login...
✅ Admin login successful

📊 Step 2: Dashboard Exploration...
✅ Dashboard explored

👥 Step 3: User Management...
✅ User management completed

🏙️ Step 4: City Management...
✅ City management completed

📂 Step 5: Category Management...
✅ Category management completed

🏪 Step 6: Shop Management...
✅ Shop management completed

⚙️ Step 7: Settings Exploration...
✅ Settings explored

📈 Step 8: Reports and Analytics...
✅ Reports and analytics explored

🔧 Step 9: System Administration...
✅ System administration completed

🚪 Step 10: Logout...
✅ Logout successful

✅ Complete Admin Panel Automation Finished Successfully!
```

## 🐛 Troubleshooting

### Common Issues

1. **ChromeDriver not found**
   ```bash
   php artisan dusk:chrome-driver
   ```

2. **Port 8000 already in use**
   ```bash
   php artisan serve --port=8001
   # Update APP_URL in .env.dusk.local
   ```

3. **Database connection issues**
   - Check `.env.dusk.local` database settings
   - Ensure test database exists

4. **Permission issues on Windows**
   ```powershell
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   ```

### Debug Mode
Run with browser visible to see what's happening:
```bash
php artisan admin:automate --test=complete --headless=false --slow=2000
```

## 📁 File Structure

```
tests/Browser/
├── AdminLoginTest.php              # Login functionality
├── AdminUserManagementTest.php     # User CRUD operations
├── AdminShopManagementTest.php     # Shop management
├── AdminCityAndCategoryManagementTest.php # Cities & categories
├── AdminDashboardTest.php          # Dashboard tests
└── CompleteAdminAutomationTest.php # End-to-end automation

app/Console/Commands/
└── RunAdminAutomation.php          # Custom artisan command

Scripts/
├── run-automation.bat              # Windows batch file
└── run-admin-automation.ps1        # PowerShell script
```

## 🔄 Continuous Integration

You can integrate this into your CI/CD pipeline:

```yaml
# GitHub Actions example
- name: Run Admin Automation
  run: |
    php artisan admin:automate --test=all --headless=true
```

## 📝 Customization

### Adding New Tests
1. Create new test file in `tests/Browser/`
2. Extend `DuskTestCase`
3. Add to automation command options

### Modifying Existing Tests
- Edit test files in `tests/Browser/`
- Update selectors to match your admin panel
- Adjust assertions for your specific UI

## 📞 Support

If you encounter issues:
1. Check the troubleshooting section
2. Review Laravel Dusk documentation
3. Ensure your admin panel routes and views match the test expectations

---

**Happy Testing! 🎉**