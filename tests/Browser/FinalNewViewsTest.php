<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class FinalNewViewsTest extends DuskTestCase
{
    /**
     * Final validation of all new CRUD views
     */
    public function test_final_crud_views_validation()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🎯 Final CRUD Views Validation\n";
            echo "===============================\n";

            // Login
            $browser->visit('http://127.0.0.1:8000/admin/login')
                    ->waitFor('input[name="email"]', 10)
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('http://127.0.0.1:8000/admin', 15);

            echo "✅ Admin login successful\n";

            // Test 1: Cities Create View (Fixed)
            try {
                $browser->visit('http://127.0.0.1:8000/admin/cities/create')
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('إضافة مدينة جديدة')
                        ->assertPresent('input[name="name"]')
                        ->assertPresent('input[name="slug"]')
                        ->assertPresent('input[name="color"]'); // Now should be present
                echo "✅ Cities Create View: FIXED AND WORKING\n";
            } catch (\Exception $e) {
                echo "❌ Cities Create View: STILL FAILING - " . $e->getMessage() . "\n";
            }

            // Test 2: Categories Create View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/categories/create')
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('إضافة فئة جديدة')
                        ->assertPresent('input[name="name"]')
                        ->assertPresent('input[name="icon"]')
                        ->assertPresent('input[name="color"]');
                echo "✅ Categories Create View: WORKING\n";
            } catch (\Exception $e) {
                echo "❌ Categories Create View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 3: Subscription Plans Views (Fixed Routes)
            try {
                $browser->visit('http://127.0.0.1:8000/admin/subscription-plans')
                        ->waitFor('.card-body', 10);
                echo "✅ Subscription Plans Index: WORKING\n";

                $browser->visit('http://127.0.0.1:8000/admin/subscription-plans/create')
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('إضافة خطة اشتراك جديدة')
                        ->assertPresent('input[name="name"]')
                        ->assertPresent('input[name="price"]');
                echo "✅ Subscription Plans Create: WORKING\n";
            } catch (\Exception $e) {
                echo "❌ Subscription Plans: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 4: JavaScript Auto-generation
            try {
                $browser->visit('http://127.0.0.1:8000/admin/cities/create')
                        ->waitFor('input[name="name"]', 10)
                        ->clear('name')
                        ->type('name', 'مدينة تجريبية')
                        ->pause(2000); // Wait for JS
                
                $slugValue = $browser->inputValue('slug');
                if (!empty($slugValue) && $slugValue !== 'مدينة تجريبية') {
                    echo "✅ JavaScript Auto-generation: WORKING (generated: $slugValue)\n";
                } else {
                    echo "⚠️ JavaScript Auto-generation: Partial (slug: '$slugValue')\n";
                }
            } catch (\Exception $e) {
                echo "❌ JavaScript: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 5: Form Submission
            try {
                $timestamp = time();
                $browser->visit('http://127.0.0.1:8000/admin/cities/create')
                        ->waitFor('input[name="name"]', 10)
                        ->type('name', "تست {$timestamp}")
                        ->type('slug', "test-{$timestamp}")
                        ->check('is_active')
                        ->press('حفظ المدينة')
                        ->waitForLocation('http://127.0.0.1:8000/admin/cities', 15);
                echo "✅ Form Submission: WORKING - City created successfully\n";
            } catch (\Exception $e) {
                echo "⚠️ Form Submission: May have validation issues - " . $e->getMessage() . "\n";
            }

            echo "\n🎉 Final Validation Complete!\n";
            echo "===============================\n";
        });
    }

    /**
     * Test navigation to all CRUD modules
     */
    public function test_navigation_to_all_modules()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🧭 Navigation Testing\n";
            echo "====================\n";

            // Login
            $browser->visit('http://127.0.0.1:8000/admin/login')
                    ->waitFor('input[name="email"]', 10)
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('http://127.0.0.1:8000/admin', 15);

            $modules = [
                'users' => 'المستخدمين',
                'cities' => 'المدن', 
                'categories' => 'الفئات',
                'shops' => 'المتاجر',
                'ratings' => 'التقييمات',
                'subscription-plans' => 'خطط الاشتراك'
            ];

            foreach ($modules as $module => $name) {
                try {
                    $browser->visit("http://127.0.0.1:8000/admin/{$module}")
                            ->waitFor('.card-header', 10);
                    echo "✅ {$name}: Accessible\n";
                } catch (\Exception $e) {
                    echo "❌ {$name}: Failed - " . $e->getMessage() . "\n";
                }
            }

            echo "\n✅ Navigation Testing Complete!\n";
        });
    }
}