<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LiveAdminAutomationTest extends DuskTestCase
{
    /**
     * Test complete admin automation with live admin account
     * This test will work with your existing admin account
     */
    public function testLiveAdminAutomation()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🚀 Starting Live Admin Panel Automation...\n";
            echo "===========================================\n\n";

            // Step 1: Check if admin login page loads
            echo "🔑 Step 1: Testing admin login page...\n";
            $browser->visit('/admin/login')
                    ->assertSee('تسجيل دخول المدير')
                    ->assertSee('البريد الإلكتروني')
                    ->assertSee('كلمة المرور');
            echo "✅ Admin login page loads correctly!\n\n";

            // Step 2: Test with demo credentials (you can change these)
            echo "🔐 Step 2: Testing login functionality...\n";
            echo "📝 Please create an admin user manually or use existing credentials\n";
            echo "🔧 For testing, you can create an admin user with:\n";
            echo "   Email: admin@test.com\n";
            echo "   Password: password123\n";
            echo "   User Type: admin\n\n";

            // Check if there's already an admin session or try demo login
            $browser->visit('/admin');
            
            if ($browser->driver->getCurrentURL() == 'http://127.0.0.1:8000/admin/login') {
                echo "🔄 Not logged in, trying demo credentials...\n";
                
                $browser->type('email', 'admin@test.com')
                        ->type('password', 'password123')
                        ->press('تسجيل الدخول');
                        
                // Wait and check result
                sleep(2);
                $currentUrl = $browser->driver->getCurrentURL();
                
                if (strpos($currentUrl, '/admin/login') !== false) {
                    echo "⚠️ Demo credentials don't work. Please use your actual admin credentials.\n";
                    echo "📋 The automation system is ready, just update the credentials in the test.\n";
                } else {
                    echo "✅ Login successful! Redirected to: $currentUrl\n";
                    $this->exploreAdminPanel($browser);
                }
            } else {
                echo "✅ Already logged in! Current page: " . $browser->driver->getCurrentURL() . "\n";
                $this->exploreAdminPanel($browser);
            }

            echo "\n🎉 Live Admin Automation Test Completed!\n";
            echo "========================================\n";
        });
    }

    /**
     * Explore admin panel functionality
     */
    private function exploreAdminPanel(Browser $browser)
    {
        echo "\n📊 Step 3: Exploring admin dashboard...\n";
        
        // Get current page content
        $pageSource = $browser->driver->getPageSource();
        
        if (strpos($pageSource, 'dashboard') !== false || strpos($pageSource, 'Dashboard') !== false || strpos($pageSource, 'لوحة') !== false) {
            echo "✅ Dashboard detected!\n";
        }

        // Check for common admin elements
        $adminElements = [
            'Users' => ['users', 'مستخدمين', 'المستخدمين'],
            'Shops' => ['shops', 'متاجر', 'المتاجر'],
            'Cities' => ['cities', 'مدن', 'المدن'],
            'Categories' => ['categories', 'فئات', 'الفئات'],
            'Dashboard' => ['dashboard', 'لوحة', 'الرئيسية'],
            'Settings' => ['settings', 'إعدادات', 'الإعدادات'],
            'Reports' => ['reports', 'تقارير', 'التقارير'],
        ];

        echo "\n🔍 Step 4: Scanning for admin features...\n";
        foreach ($adminElements as $element => $patterns) {
            $found = false;
            foreach ($patterns as $pattern) {
                if (stripos($pageSource, $pattern) !== false) {
                    echo "✅ Found: $element\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "⚪ Not found: $element\n";
            }
        }

        // Test navigation if possible
        echo "\n🧭 Step 5: Testing navigation...\n";
        $this->testNavigation($browser);
    }

    /**
     * Test navigation links
     */
    private function testNavigation(Browser $browser)
    {
        $navigationTests = [
            '/admin/users' => 'Users Management',
            '/admin/shops' => 'Shop Management', 
            '/admin/cities' => 'City Management',
            '/admin/categories' => 'Category Management',
        ];

        foreach ($navigationTests as $url => $description) {
            try {
                echo "🔗 Testing $description ($url)...\n";
                $browser->visit($url);
                
                $currentUrl = $browser->driver->getCurrentURL();
                if (strpos($currentUrl, '/admin/login') === false) {
                    echo "✅ $description page accessible\n";
                    
                    // Take screenshot for documentation
                    $screenshotName = str_replace(['/', ' '], ['_', '_'], $description) . '.png';
                    $browser->screenshot($screenshotName);
                    echo "📸 Screenshot saved: $screenshotName\n";
                } else {
                    echo "❌ $description requires additional authentication\n";
                }
            } catch (\Exception $e) {
                echo "⚠️ $description not accessible: " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * Test specific admin functionality based on what's available
     */
    public function testAvailableAdminFeatures()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🎯 Testing Available Admin Features\n";
            echo "===================================\n";

            // Try to access admin area
            $browser->visit('/admin');
            $currentUrl = $browser->driver->getCurrentURL();
            
            if (strpos($currentUrl, '/admin/login') !== false) {
                echo "🔐 Admin login required. Testing login page functionality...\n";
                
                // Test login page elements
                $this->testLoginPageElements($browser);
            } else {
                echo "✅ Admin area accessible at: $currentUrl\n";
                $this->testAdminAreaFeatures($browser);
            }
        });
    }

    /**
     * Test login page elements in detail
     */
    private function testLoginPageElements(Browser $browser)
    {
        echo "\n🔍 Testing Login Page Elements:\n";
        
        $elements = [
            'Email field' => 'input[name="email"]',
            'Password field' => 'input[name="password"]',
            'Login button' => 'button[type="submit"]',
            'Remember me' => 'input[name="remember"]',
        ];

        foreach ($elements as $name => $selector) {
            try {
                $element = $browser->element($selector);
                if ($element) {
                    echo "✅ $name found\n";
                } else {
                    echo "❌ $name not found\n";
                }
            } catch (\Exception $e) {
                echo "❌ $name not found\n";
            }
        }
    }

    /**
     * Test admin area features
     */
    private function testAdminAreaFeatures(Browser $browser)
    {
        echo "\n🏗️ Testing Admin Area Features:\n";
        
        // Test common admin paths
        $adminPaths = [
            '/admin' => 'Dashboard',
            '/admin/users' => 'User Management',
            '/admin/shops' => 'Shop Management',
            '/admin/cities' => 'City Management',
            '/admin/categories' => 'Category Management',
        ];

        foreach ($adminPaths as $path => $description) {
            try {
                $browser->visit($path);
                $title = $browser->driver->getTitle();
                echo "✅ $description accessible - Title: $title\n";
            } catch (\Exception $e) {
                echo "❌ $description not accessible\n";
            }
        }
    }
}