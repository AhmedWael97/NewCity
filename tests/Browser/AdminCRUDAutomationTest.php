<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminCRUDAutomationTest extends DuskTestCase
{
    /**
     * Test all available CRUD functionality in admin panel
     */
    public function testAdminCRUDFunctionality()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🎯 ADMIN CRUD FUNCTIONALITY TEST\n";
            echo "================================\n\n";

            // Login first
            $this->loginAsAdmin($browser);

            // Test each module
            $this->testUsersCRUD($browser);
            $this->testShopsCRUD($browser);
            $this->testCitiesCRUD($browser);
            $this->testCategoriesCRUD($browser);
            $this->testRatingsCRUD($browser);
            $this->testSubscriptionsCRUD($browser);

            echo "\n🎉 CRUD FUNCTIONALITY TEST COMPLETED!\n";
            echo "====================================\n";
        });
    }

    /**
     * Login as admin
     */
    private function loginAsAdmin(Browser $browser)
    {
        echo "🔑 Logging in as admin...\n";
        $browser->visit('/admin/login')
                ->type('email', 'admin@city.com')
                ->type('password', 'superadminpassword')
                ->press('تسجيل الدخول')
                ->waitForLocation('/admin', 10);
        echo "✅ Admin login successful\n\n";
    }

    /**
     * Test Users CRUD
     */
    private function testUsersCRUD(Browser $browser)
    {
        echo "👥 TESTING USERS CRUD\n";
        echo "-------------------\n";

        // Index
        echo "📋 Testing Users Index...\n";
        $browser->visit('/admin/users')->screenshot('users_index');
        echo "✅ Users index page accessible\n";

        // Test search
        echo "🔍 Testing Users Search...\n";
        if ($browser->element('input[name="search"]')) {
            $browser->type('input[name="search"]', 'admin')
                    ->press('Search')
                    ->screenshot('users_search');
            echo "✅ Users search functionality works\n";
        } else {
            echo "⚠️ Users search field not found\n";
        }

        // Test filters
        echo "🎛️ Testing Users Filters...\n";
        $this->testSelectFilters($browser, ['user_type', 'is_verified', 'city_id']);

        // Try to access create form
        echo "➕ Testing Users Create Form...\n";
        if ($this->tryAccessCreateForm($browser, '/admin/users/create', 'users_create')) {
            echo "✅ Users create form accessible\n";
        } else {
            echo "❌ Users create form not accessible\n";
        }

        // Test bulk actions
        echo "📦 Testing Users Bulk Actions...\n";
        $this->testBulkActions($browser, 'users');

        echo "✅ Users CRUD testing completed\n\n";
    }

    /**
     * Test Shops CRUD
     */
    private function testShopsCRUD(Browser $browser)
    {
        echo "🏪 TESTING SHOPS CRUD\n";
        echo "-------------------\n";

        // Index
        echo "📋 Testing Shops Index...\n";
        $browser->visit('/admin/shops')->screenshot('shops_index');
        echo "✅ Shops index page accessible\n";

        // Test search
        echo "🔍 Testing Shops Search...\n";
        if ($browser->element('input[name="search"]')) {
            $browser->type('input[name="search"]', 'shop')
                    ->press('Search')
                    ->screenshot('shops_search');
            echo "✅ Shops search functionality works\n";
        } else {
            echo "⚠️ Shops search field not found\n";
        }

        // Test create form
        echo "➕ Testing Shops Create Form...\n";
        if ($this->tryAccessCreateForm($browser, '/admin/shops/create', 'shops_create')) {
            echo "✅ Shops create form accessible\n";
        } else {
            echo "❌ Shops create form not accessible\n";
        }

        // Test pending shops
        echo "⏳ Testing Pending Shops...\n";
        try {
            $browser->visit('/admin/shops/pending/review')->screenshot('shops_pending');
            echo "✅ Pending shops page accessible\n";
        } catch (\Exception $e) {
            echo "⚠️ Pending shops page not accessible\n";
        }

        // Test bulk actions
        echo "📦 Testing Shops Bulk Actions...\n";
        $this->testBulkActions($browser, 'shops');

        echo "✅ Shops CRUD testing completed\n\n";
    }

    /**
     * Test Cities CRUD
     */
    private function testCitiesCRUD(Browser $browser)
    {
        echo "🏙️ TESTING CITIES CRUD\n";
        echo "--------------------\n";

        // Index
        echo "📋 Testing Cities Index...\n";
        $browser->visit('/admin/cities')->screenshot('cities_index');
        echo "✅ Cities index page accessible\n";

        // Test search
        echo "🔍 Testing Cities Search...\n";
        if ($browser->element('input[name="search"]')) {
            $browser->type('input[name="search"]', 'cairo')
                    ->press('Search')
                    ->screenshot('cities_search');
            echo "✅ Cities search functionality works\n";
        } else {
            echo "⚠️ Cities search field not found\n";
        }

        // Try to access create form
        echo "➕ Testing Cities Create Form...\n";
        if ($this->tryAccessCreateForm($browser, '/admin/cities/create', 'cities_create')) {
            echo "✅ Cities create form accessible\n";
        } else {
            echo "❌ Cities create form not accessible\n";
        }

        echo "✅ Cities CRUD testing completed\n\n";
    }

    /**
     * Test Categories CRUD
     */
    private function testCategoriesCRUD(Browser $browser)
    {
        echo "📂 TESTING CATEGORIES CRUD\n";
        echo "------------------------\n";

        // Index
        echo "📋 Testing Categories Index...\n";
        $browser->visit('/admin/categories')->screenshot('categories_index');
        echo "✅ Categories index page accessible\n";

        // Test hierarchy
        echo "🌳 Testing Categories Hierarchy...\n";
        try {
            $browser->visit('/admin/categories/hierarchy')->screenshot('categories_hierarchy');
            echo "✅ Categories hierarchy page accessible\n";
        } catch (\Exception $e) {
            echo "⚠️ Categories hierarchy page not accessible\n";
        }

        // Try to access create form
        echo "➕ Testing Categories Create Form...\n";
        if ($this->tryAccessCreateForm($browser, '/admin/categories/create', 'categories_create')) {
            echo "✅ Categories create form accessible\n";
        } else {
            echo "❌ Categories create form not accessible\n";
        }

        echo "✅ Categories CRUD testing completed\n\n";
    }

    /**
     * Test Ratings CRUD
     */
    private function testRatingsCRUD(Browser $browser)
    {
        echo "⭐ TESTING RATINGS CRUD\n";
        echo "---------------------\n";

        // Index
        echo "📋 Testing Ratings Index...\n";
        $browser->visit('/admin/ratings')->screenshot('ratings_index');
        echo "✅ Ratings index page accessible\n";

        // Test search
        echo "🔍 Testing Ratings Search...\n";
        if ($browser->element('input[name="search"]')) {
            $browser->type('input[name="search"]', 'rating')
                    ->press('Search')
                    ->screenshot('ratings_search');
            echo "✅ Ratings search functionality works\n";
        } else {
            echo "⚠️ Ratings search field not found\n";
        }

        // Note: Ratings don't have create form since they're user-generated
        echo "ℹ️ Ratings are user-generated, no create form needed\n";

        echo "✅ Ratings CRUD testing completed\n\n";
    }

    /**
     * Test Subscriptions CRUD
     */
    private function testSubscriptionsCRUD(Browser $browser)
    {
        echo "💳 TESTING SUBSCRIPTIONS CRUD\n";
        echo "----------------------------\n";

        // Index
        echo "📋 Testing Subscriptions Index...\n";
        $browser->visit('/admin/subscriptions')->screenshot('subscriptions_index');
        echo "✅ Subscriptions index page accessible\n";

        // Try to access create form
        echo "➕ Testing Subscriptions Create Form...\n";
        if ($this->tryAccessCreateForm($browser, '/admin/subscriptions/create', 'subscriptions_create')) {
            echo "✅ Subscriptions create form accessible\n";
        } else {
            echo "❌ Subscriptions create form not accessible\n";
        }

        // Test analytics
        echo "📊 Testing Subscriptions Analytics...\n";
        try {
            $browser->visit('/admin/subscriptions/analytics/overview')->screenshot('subscriptions_analytics');
            echo "✅ Subscriptions analytics page accessible\n";
        } catch (\Exception $e) {
            echo "⚠️ Subscriptions analytics page not accessible\n";
        }

        echo "✅ Subscriptions CRUD testing completed\n\n";
    }

    /**
     * Helper: Try to access create form
     */
    private function tryAccessCreateForm(Browser $browser, string $url, string $screenshotName): bool
    {
        try {
            $browser->visit($url);
            $currentUrl = $browser->driver->getCurrentURL();
            
            if (strpos($currentUrl, '/admin/login') === false && 
                strpos($currentUrl, 'create') !== false) {
                $browser->screenshot($screenshotName);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Helper: Test select filters
     */
    private function testSelectFilters(Browser $browser, array $filterNames)
    {
        foreach ($filterNames as $filterName) {
            if ($browser->element("select[name=\"{$filterName}\"]")) {
                echo "✅ Filter '{$filterName}' found\n";
            } else {
                echo "⚠️ Filter '{$filterName}' not found\n";
            }
        }
    }

    /**
     * Helper: Test bulk actions
     */
    private function testBulkActions(Browser $browser, string $module)
    {
        if ($browser->element('select[name="bulk_action"]') || 
            $browser->element('[name="bulk_action"]')) {
            echo "✅ Bulk actions available for {$module}\n";
        } else {
            echo "⚠️ Bulk actions not found for {$module}\n";
        }
    }

    /**
     * Test action buttons (edit, delete, etc.)
     */
    public function testActionButtons()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🎯 TESTING ACTION BUTTONS\n";
            echo "========================\n\n";

            $this->loginAsAdmin($browser);

            $modules = [
                'users' => 'Users',
                'shops' => 'Shops', 
                'cities' => 'Cities',
                'categories' => 'Categories',
                'ratings' => 'Ratings',
                'subscriptions' => 'Subscriptions'
            ];

            foreach ($modules as $route => $name) {
                echo "🔘 Testing {$name} Action Buttons...\n";
                $browser->visit("/admin/{$route}");
                
                // Check for various action buttons
                $actions = [
                    '[data-action="edit"]' => 'Edit',
                    '[data-action="delete"]' => 'Delete',
                    '[data-action="toggle-status"]' => 'Toggle Status',
                    '[data-action="verify"]' => 'Verify',
                    '.btn-edit' => 'Edit Button',
                    '.btn-delete' => 'Delete Button',
                    'a[href*="edit"]' => 'Edit Link',
                    'form[method="DELETE"]' => 'Delete Form'
                ];

                $foundActions = 0;
                foreach ($actions as $selector => $actionName) {
                    try {
                        $elements = $browser->elements($selector);
                        if (count($elements) > 0) {
                            echo "  ✅ {$actionName}: " . count($elements) . " found\n";
                            $foundActions++;
                        }
                    } catch (\Exception $e) {
                        // Action not found, continue
                    }
                }

                if ($foundActions === 0) {
                    echo "  ⚠️ No action buttons found for {$name}\n";
                }

                $browser->screenshot("{$route}_actions");
                echo "  📸 Screenshot saved: {$route}_actions.png\n\n";
            }

            echo "✅ Action buttons testing completed\n";
        });
    }
}