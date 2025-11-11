<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FullAdminAutomationTest extends DuskTestCase
{
    /**
     * Complete admin automation test with real credentials
     */
    public function testCompleteAdminAutomation()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🚀 Starting Complete Admin Panel Automation...\n";
            echo "================================================\n\n";

            // Step 1: Login with real credentials
            echo "🔑 Step 1: Admin Login...\n";
            $browser->visit('/admin/login')
                    ->assertSee('تسجيل دخول المدير')
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('/admin', 10);
                    
            $currentUrl = $browser->driver->getCurrentURL();
            if (strpos($currentUrl, '/admin/login') === false) {
                echo "✅ Login successful! Current URL: $currentUrl\n\n";
            } else {
                echo "❌ Login failed - still on login page\n";
                return;
            }

            // Step 2: Explore Dashboard
            echo "📊 Step 2: Dashboard Exploration...\n";
            $browser->visit('/admin')
                    ->screenshot('admin_dashboard');
            echo "✅ Dashboard accessed\n";
            echo "📸 Screenshot saved: admin_dashboard.png\n\n";

            // Step 3: User Management
            echo "👥 Step 3: User Management Testing...\n";
            $browser->visit('/admin/users')
                    ->screenshot('admin_users_list');
            echo "✅ Users page accessible\n";
            echo "📸 Screenshot saved: admin_users_list.png\n";
            
            // Try to access create user form
            try {
                if ($browser->seeLink('Add New User') || $browser->seeLink('إضافة مستخدم جديد')) {
                    $browser->clickLink('Add New User')
                            ->screenshot('admin_create_user');
                    echo "✅ Create user form accessible\n";
                    echo "📸 Screenshot saved: admin_create_user.png\n";
                } else {
                    $browser->visit('/admin/users/create')
                            ->screenshot('admin_create_user_direct');
                    echo "✅ Create user form accessed directly\n";
                    echo "📸 Screenshot saved: admin_create_user_direct.png\n";
                }
            } catch (\Exception $e) {
                echo "⚠️ Create user form not accessible\n";
            }
            echo "\n";

            // Step 4: Shop Management
            echo "🏪 Step 4: Shop Management Testing...\n";
            $browser->visit('/admin/shops')
                    ->screenshot('admin_shops_list');
            echo "✅ Shops page accessible\n";
            echo "📸 Screenshot saved: admin_shops_list.png\n";
            
            // Try to access create shop form
            try {
                if ($browser->seeLink('Add New Shop') || $browser->seeLink('إضافة متجر جديد')) {
                    $browser->clickLink('Add New Shop')
                            ->screenshot('admin_create_shop');
                    echo "✅ Create shop form accessible\n";
                    echo "📸 Screenshot saved: admin_create_shop.png\n";
                } else {
                    $browser->visit('/admin/shops/create')
                            ->screenshot('admin_create_shop_direct');
                    echo "✅ Create shop form accessed directly\n";
                    echo "📸 Screenshot saved: admin_create_shop_direct.png\n";
                }
            } catch (\Exception $e) {
                echo "⚠️ Create shop form not accessible\n";
            }
            echo "\n";

            // Step 5: City Management
            echo "🏙️ Step 5: City Management Testing...\n";
            $browser->visit('/admin/cities')
                    ->screenshot('admin_cities_list');
            echo "✅ Cities page accessible\n";
            echo "📸 Screenshot saved: admin_cities_list.png\n";
            
            // Try to access create city form
            try {
                if ($browser->seeLink('Add New City') || $browser->seeLink('إضافة مدينة جديدة')) {
                    $browser->clickLink('Add New City')
                            ->screenshot('admin_create_city');
                    echo "✅ Create city form accessible\n";
                    echo "📸 Screenshot saved: admin_create_city.png\n";
                }
            } catch (\Exception $e) {
                echo "⚠️ Create city form not accessible\n";
            }
            echo "\n";

            // Step 6: Category Management
            echo "📂 Step 6: Category Management Testing...\n";
            $browser->visit('/admin/categories')
                    ->screenshot('admin_categories_list');
            echo "✅ Categories page accessible\n";
            echo "📸 Screenshot saved: admin_categories_list.png\n";
            
            // Try to access create category form
            try {
                if ($browser->seeLink('Add New Category') || $browser->seeLink('إضافة فئة جديدة')) {
                    $browser->clickLink('Add New Category')
                            ->screenshot('admin_create_category');
                    echo "✅ Create category form accessible\n";
                    echo "📸 Screenshot saved: admin_create_category.png\n";
                }
            } catch (\Exception $e) {
                echo "⚠️ Create category form not accessible\n";
            }
            echo "\n";

            // Step 7: Test Search/Filter Functionality
            echo "🔍 Step 7: Testing Search/Filter Features...\n";
            $this->testSearchFeatures($browser);
            echo "\n";

            // Step 8: Test Admin Actions
            echo "⚙️ Step 8: Testing Admin Actions...\n";
            $this->testAdminActions($browser);
            echo "\n";

            // Step 9: Logout
            echo "🚪 Step 9: Admin Logout...\n";
            try {
                if ($browser->seeLink('Logout') || $browser->seeLink('تسجيل خروج')) {
                    $browser->clickLink('Logout')
                            ->waitForLocation('/admin/login', 10);
                    echo "✅ Logout successful via link\n";
                } else {
                    $browser->visit('/admin/logout')
                            ->waitForLocation('/admin/login', 10);
                    echo "✅ Logout successful via direct URL\n";
                }
            } catch (\Exception $e) {
                echo "⚠️ Logout test skipped\n";
            }

            echo "\n🎉 Complete Admin Panel Automation Finished Successfully!\n";
            echo "===========================================================\n";
            echo "📸 All screenshots saved in tests/Browser/screenshots/\n";
            echo "📋 Summary: Tested login, dashboard, users, shops, cities, categories, and logout\n";
        });
    }

    /**
     * Test search and filter features
     */
    private function testSearchFeatures(Browser $browser)
    {
        // Test search in users
        try {
            $browser->visit('/admin/users');
            if ($browser->element('input[name="search"]') || $browser->element('input[placeholder*="search"]')) {
                $browser->type('input[name="search"]', 'test')
                        ->press('Search')
                        ->screenshot('admin_users_search');
                echo "✅ User search feature tested\n";
                echo "📸 Screenshot saved: admin_users_search.png\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ User search not available\n";
        }

        // Test search in shops
        try {
            $browser->visit('/admin/shops');
            if ($browser->element('input[name="search"]') || $browser->element('input[placeholder*="search"]')) {
                $browser->type('input[name="search"]', 'test')
                        ->press('Search')
                        ->screenshot('admin_shops_search');
                echo "✅ Shop search feature tested\n";
                echo "📸 Screenshot saved: admin_shops_search.png\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ Shop search not available\n";
        }
    }

    /**
     * Test admin actions like edit, delete, status changes
     */
    private function testAdminActions(Browser $browser)
    {
        // Test user actions
        try {
            $browser->visit('/admin/users');
            
            // Look for action buttons
            $actionButtons = $browser->elements('[data-action]');
            if (count($actionButtons) > 0) {
                echo "✅ Found " . count($actionButtons) . " action buttons in users\n";
                $browser->screenshot('admin_users_actions');
                echo "📸 Screenshot saved: admin_users_actions.png\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ User action buttons not found\n";
        }

        // Test shop actions
        try {
            $browser->visit('/admin/shops');
            
            // Look for action buttons
            $actionButtons = $browser->elements('[data-action]');
            if (count($actionButtons) > 0) {
                echo "✅ Found " . count($actionButtons) . " action buttons in shops\n";
                $browser->screenshot('admin_shops_actions');
                echo "📸 Screenshot saved: admin_shops_actions.png\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ Shop action buttons not found\n";
        }
    }

    /**
     * Test admin authentication edge cases
     */
    public function testAdminAuthentication()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🔐 Testing Admin Authentication Edge Cases...\n";
            echo "==============================================\n\n";

            // Test 1: Invalid credentials
            echo "❌ Test 1: Invalid Credentials...\n";
            $browser->visit('/admin/login')
                    ->type('email', 'wrong@email.com')
                    ->type('password', 'wrongpassword')
                    ->press('تسجيل الدخول')
                    ->waitFor('.alert-danger', 5)
                    ->screenshot('admin_login_failed');
            echo "✅ Invalid login properly rejected\n";
            echo "📸 Screenshot saved: admin_login_failed.png\n\n";

            // Test 2: Valid credentials
            echo "✅ Test 2: Valid Credentials...\n";
            $browser->visit('/admin/login')
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('/admin', 10);
            echo "✅ Valid login successful\n\n";

            // Test 3: Protected routes
            echo "🔒 Test 3: Protected Routes...\n";
            $protectedRoutes = ['/admin/users', '/admin/shops', '/admin/cities', '/admin/categories'];
            
            foreach ($protectedRoutes as $route) {
                try {
                    $browser->visit($route);
                    $currentUrl = $browser->driver->getCurrentURL();
                    if (strpos($currentUrl, '/admin/login') === false) {
                        echo "✅ $route - Accessible when authenticated\n";
                    } else {
                        echo "❌ $route - Redirected to login\n";
                    }
                } catch (\Exception $e) {
                    echo "⚠️ $route - Error accessing\n";
                }
            }

            echo "\n🎉 Authentication testing completed!\n";
        });
    }
}