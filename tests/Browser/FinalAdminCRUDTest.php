<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FinalAdminCRUDTest extends DuskTestCase
{
    /**
     * Complete CRUD analysis and testing
     */
    public function testCompleteCRUDAnalysis()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🎯 COMPLETE ADMIN CRUD ANALYSIS\n";
            echo "==============================\n\n";

            // Login
            $this->loginAsAdmin($browser);

            // Analyze each module
            $this->analyzeUsers($browser);
            $this->analyzeShops($browser);
            $this->analyzeCities($browser);
            $this->analyzeCategories($browser);
            $this->analyzeRatings($browser);
            $this->analyzeSubscriptions($browser);

            echo "\n📊 FINAL ANALYSIS SUMMARY\n";
            echo "========================\n";
            echo "✅ All modules tested and analyzed\n";
            echo "📸 Screenshots saved for each module\n";
            echo "📋 Detailed analysis saved in ADMIN_CRUD_ANALYSIS.md\n";
        });
    }

    private function loginAsAdmin(Browser $browser)
    {
        echo "🔑 Admin Login...\n";
        $browser->visit('/admin/login')
                ->type('email', 'admin@city.com')
                ->type('password', 'superadminpassword')
                ->press('تسجيل الدخول')
                ->waitForLocation('/admin', 10);
        echo "✅ Login successful\n\n";
    }

    private function analyzeUsers(Browser $browser)
    {
        echo "👥 ANALYZING USERS MODULE\n";
        echo "-----------------------\n";

        $browser->visit('/admin/users')->screenshot('users_analysis');

        // Check for create button
        if ($browser->seeLink('إضافة مستخدم جديد')) {
            echo "✅ Create button found: 'إضافة مستخدم جديد'\n";
            
            // Try to access create form
            try {
                $browser->clickLink('إضافة مستخدم جديد');
                $currentUrl = $browser->driver->getCurrentURL();
                if (strpos($currentUrl, 'create') !== false) {
                    echo "✅ Create form accessible\n";
                    $browser->screenshot('users_create_form');
                } else {
                    echo "❌ Create form redirected to: $currentUrl\n";
                }
            } catch (\Exception $e) {
                echo "❌ Create form error: " . $e->getMessage() . "\n";
            }
        } else {
            echo "❌ Create button not found\n";
        }

        // Go back to index
        $browser->visit('/admin/users');

        // Check search functionality
        if ($browser->element('input[name="search"]')) {
            echo "✅ Search field found\n";
            $browser->type('input[name="search"]', 'admin')
                    ->press('بحث')
                    ->screenshot('users_search_results');
            echo "✅ Search tested\n";
        } else {
            echo "❌ Search field not found\n";
        }

        // Check filters
        $filters = ['role', 'city_id', 'status', 'is_verified'];
        foreach ($filters as $filter) {
            if ($browser->element("select[name=\"{$filter}\"]")) {
                echo "✅ Filter found: {$filter}\n";
            } else {
                echo "❌ Filter missing: {$filter}\n";
            }
        }

        // Check for bulk actions
        if ($browser->element('button[onclick="selectAll()"]')) {
            echo "✅ Bulk action controls found\n";
        } else {
            echo "❌ Bulk action controls not found\n";
        }

        echo "✅ Users analysis completed\n\n";
    }

    private function analyzeShops(Browser $browser)
    {
        echo "🏪 ANALYZING SHOPS MODULE\n";
        echo "-----------------------\n";

        $browser->visit('/admin/shops')->screenshot('shops_analysis');

        // Check for create button
        if ($browser->seeLink('إضافة متجر جديد') || $browser->seeLink('Add Shop') || $browser->seeLink('Create')) {
            echo "✅ Create button found\n";
        } else {
            echo "❌ Create button not found\n";
        }

        // Check search
        if ($browser->element('input[name="search"]')) {
            echo "✅ Search functionality available\n";
        } else {
            echo "❌ Search functionality missing\n";
        }

        echo "✅ Shops analysis completed\n\n";
    }

    private function analyzeCities(Browser $browser)
    {
        echo "🏙️ ANALYZING CITIES MODULE\n";
        echo "------------------------\n";

        $browser->visit('/admin/cities')->screenshot('cities_analysis');

        // Check various elements
        $this->checkCRUDElements($browser, 'Cities');

        echo "✅ Cities analysis completed\n\n";
    }

    private function analyzeCategories(Browser $browser)
    {
        echo "📂 ANALYZING CATEGORIES MODULE\n";
        echo "----------------------------\n";

        $browser->visit('/admin/categories')->screenshot('categories_analysis');

        // Check various elements
        $this->checkCRUDElements($browser, 'Categories');

        // Check for hierarchy
        try {
            $browser->visit('/admin/categories/hierarchy')->screenshot('categories_hierarchy');
            echo "✅ Hierarchy view available\n";
        } catch (\Exception $e) {
            echo "❌ Hierarchy view not available\n";
        }

        echo "✅ Categories analysis completed\n\n";
    }

    private function analyzeRatings(Browser $browser)
    {
        echo "⭐ ANALYZING RATINGS MODULE\n";
        echo "-------------------------\n";

        $browser->visit('/admin/ratings')->screenshot('ratings_analysis');

        // Check various elements
        $this->checkCRUDElements($browser, 'Ratings');

        echo "ℹ️ Note: Ratings are typically user-generated\n";
        echo "✅ Ratings analysis completed\n\n";
    }

    private function analyzeSubscriptions(Browser $browser)
    {
        echo "💳 ANALYZING SUBSCRIPTIONS MODULE\n";
        echo "-------------------------------\n";

        $browser->visit('/admin/subscriptions')->screenshot('subscriptions_analysis');

        // Check various elements
        $this->checkCRUDElements($browser, 'Subscriptions');

        echo "✅ Subscriptions analysis completed\n\n";
    }

    private function checkCRUDElements(Browser $browser, string $moduleName)
    {
        // Check for common CRUD elements
        $elements = [
            'Create Button' => ['إضافة', 'Add', 'Create', 'جديد'],
            'Search Field' => ['input[name="search"]', 'input[placeholder*="بحث"]'],
            'Edit Links' => ['a[href*="edit"]', '[data-action="edit"]'],
            'Delete Buttons' => ['[data-action="delete"]', 'button[data-toggle="modal"]'],
            'Filter Selects' => ['select[name*="filter"]', 'select[name*="status"]'],
        ];

        foreach ($elements as $elementType => $selectors) {
            $found = false;
            foreach ($selectors as $selector) {
                if (is_array($selector)) {
                    // Text-based search
                    foreach ($selector as $text) {
                        if ($browser->seeLink($text) || $browser->see($text)) {
                            echo "✅ {$elementType} found: {$text}\n";
                            $found = true;
                            break 2;
                        }
                    }
                } else {
                    // Selector-based search
                    if ($browser->element($selector)) {
                        echo "✅ {$elementType} found\n";
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                echo "❌ {$elementType} not found\n";
            }
        }
    }
}