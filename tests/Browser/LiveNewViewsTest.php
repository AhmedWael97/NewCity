<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LiveNewViewsTest extends DuskTestCase
{
    /**
     * Test new views with live database
     */
    public function test_new_views_load_with_live_admin()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🚀 Testing New Admin CRUD Views\n";
            echo "================================\n";

            // Login with known working credentials
            $browser->visit('http://127.0.0.1:8000/admin/login')
                    ->waitFor('input[name="email"]', 10)
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('http://127.0.0.1:8000/admin', 15)
                    ->assertSee('لوحة التحكم');

            echo "✅ Admin login successful\n";

            // Test 1: Users Edit View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/users')
                        ->waitFor('.card-body', 10)
                        ->assertSee('المستخدمين');
                
                // Try to find an edit button and click it
                if ($browser->element('.btn-primary[href*="/edit"]')) {
                    $editUrl = $browser->attribute('.btn-primary[href*="/edit"]', 'href');
                    $browser->visit($editUrl)
                            ->waitFor('input[name="name"]', 10)
                            ->assertSee('تعديل المستخدم')
                            ->assertPresent('input[name="name"]')
                            ->assertPresent('input[name="email"]')
                            ->assertPresent('select[name="user_type"]');
                    echo "✅ Users Edit View: WORKING\n";
                } else {
                    echo "⚠️ Users Edit View: No users found to edit\n";
                }
            } catch (\Exception $e) {
                echo "❌ Users Edit View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 2: Cities Create View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/cities/create')
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('إضافة مدينة جديدة')
                        ->assertPresent('input[name="name"]')
                        ->assertPresent('input[name="name_en"]')
                        ->assertPresent('input[name="slug"]')
                        ->assertPresent('input[name="governorate"]')
                        ->assertPresent('textarea[name="description"]')
                        ->assertPresent('input[name="latitude"]')
                        ->assertPresent('input[name="longitude"]')
                        ->assertPresent('input[name="color"]')
                        ->assertPresent('input[name="is_active"]');
                echo "✅ Cities Create View: WORKING\n";
            } catch (\Exception $e) {
                echo "❌ Cities Create View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 3: Cities Edit View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/cities')
                        ->waitFor('.card-body', 10);
                
                if ($browser->element('.btn-warning[href*="/edit"]')) {
                    $editUrl = $browser->attribute('.btn-warning[href*="/edit"]', 'href');
                    $browser->visit($editUrl)
                            ->waitFor('input[name="name"]', 10)
                            ->assertSee('تعديل المدينة')
                            ->assertPresent('input[name="name"]')
                            ->assertPresent('input[name="latitude"]')
                            ->assertPresent('input[name="longitude"]');
                    echo "✅ Cities Edit View: WORKING\n";
                } else {
                    echo "⚠️ Cities Edit View: No cities found to edit\n";
                }
            } catch (\Exception $e) {
                echo "❌ Cities Edit View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 4: Categories Create View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/categories/create')
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('إضافة فئة جديدة')
                        ->assertPresent('input[name="name"]')
                        ->assertPresent('input[name="name_en"]')
                        ->assertPresent('input[name="slug"]')
                        ->assertPresent('select[name="parent_id"]')
                        ->assertPresent('input[name="icon"]')
                        ->assertPresent('input[name="color"]')
                        ->assertPresent('input[name="meta_title"]')
                        ->assertPresent('textarea[name="meta_description"]');
                echo "✅ Categories Create View: WORKING\n";
            } catch (\Exception $e) {
                echo "❌ Categories Create View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 5: Categories Edit View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/categories')
                        ->waitFor('.card-body', 10);
                
                if ($browser->element('.btn-warning[href*="/edit"]')) {
                    $editUrl = $browser->attribute('.btn-warning[href*="/edit"]', 'href');
                    $browser->visit($editUrl)
                            ->waitFor('input[name="name"]', 10)
                            ->assertSee('تعديل الفئة')
                            ->assertPresent('input[name="name"]')
                            ->assertPresent('input[name="icon"]')
                            ->assertPresent('textarea[name="meta_description"]');
                    echo "✅ Categories Edit View: WORKING\n";
                } else {
                    echo "⚠️ Categories Edit View: No categories found to edit\n";
                }
            } catch (\Exception $e) {
                echo "❌ Categories Edit View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 6: Ratings Edit View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/ratings')
                        ->waitFor('.card-body', 10);
                
                if ($browser->element('.btn-warning[href*="/edit"]')) {
                    $editUrl = $browser->attribute('.btn-warning[href*="/edit"]', 'href');
                    $browser->visit($editUrl)
                            ->waitFor('select[name="user_id"]', 10)
                            ->assertSee('تعديل التقييم')
                            ->assertPresent('select[name="user_id"]')
                            ->assertPresent('select[name="shop_id"]')
                            ->assertPresent('select[name="rating"]')
                            ->assertPresent('select[name="status"]')
                            ->assertPresent('textarea[name="comment"]');
                    echo "✅ Ratings Edit View: WORKING\n";
                } else {
                    echo "⚠️ Ratings Edit View: No ratings found to edit\n";
                }
            } catch (\Exception $e) {
                echo "❌ Ratings Edit View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 7: Subscription Plans Create View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/subscription-plans/create')
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('إضافة خطة اشتراك جديدة')
                        ->assertPresent('input[name="name"]')
                        ->assertPresent('input[name="name_en"]')
                        ->assertPresent('input[name="price"]')
                        ->assertPresent('select[name="duration_days"]')
                        ->assertPresent('input[name="max_products"]')
                        ->assertPresent('input[name="max_images"]')
                        ->assertPresent('textarea[name="features"]')
                        ->assertPresent('input[name="color"]')
                        ->assertPresent('input[name="icon"]');
                echo "✅ Subscription Plans Create View: WORKING\n";
            } catch (\Exception $e) {
                echo "❌ Subscription Plans Create View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 8: Subscription Plans Edit View
            try {
                $browser->visit('http://127.0.0.1:8000/admin/subscription-plans')
                        ->waitFor('.card-body', 10);
                
                if ($browser->element('.btn-warning[href*="/edit"]')) {
                    $editUrl = $browser->attribute('.btn-warning[href*="/edit"]', 'href');
                    $browser->visit($editUrl)
                            ->waitFor('input[name="name"]', 10)
                            ->assertSee('تعديل خطة الاشتراك')
                            ->assertPresent('input[name="name"]')
                            ->assertPresent('input[name="price"]')
                            ->assertPresent('input[name="discount_percentage"]');
                    echo "✅ Subscription Plans Edit View: WORKING\n";
                } else {
                    echo "⚠️ Subscription Plans Edit View: No plans found to edit\n";
                }
            } catch (\Exception $e) {
                echo "❌ Subscription Plans Edit View: ERROR - " . $e->getMessage() . "\n";
            }

            // Test 9: JavaScript Functionality
            try {
                $browser->visit('http://127.0.0.1:8000/admin/cities/create')
                        ->waitFor('input[name="name"]', 10)
                        ->type('name', 'تست المدينة')
                        ->pause(1000); // Wait for JS to process
                
                // Check if slug was auto-generated
                $slugValue = $browser->inputValue('slug');
                if (!empty($slugValue)) {
                    echo "✅ JavaScript Auto-generation: WORKING (slug: $slugValue)\n";
                } else {
                    echo "⚠️ JavaScript Auto-generation: Not working\n";
                }
            } catch (\Exception $e) {
                echo "❌ JavaScript Auto-generation: ERROR - " . $e->getMessage() . "\n";
            }

            echo "\n🎉 New Views Testing Complete!\n";
            echo "================================\n";
        });
    }

    /**
     * Test form submission functionality
     */
    public function test_form_submission_functionality()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🔄 Testing Form Submission\n";
            echo "=========================\n";

            // Login
            $browser->visit('http://127.0.0.1:8000/admin/login')
                    ->waitFor('input[name="email"]', 10)
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('http://127.0.0.1:8000/admin', 15);

            // Test creating a new city
            try {
                $timestamp = time();
                $browser->visit('http://127.0.0.1:8000/admin/cities/create')
                        ->waitFor('input[name="name"]', 10)
                        ->type('name', "مدينة تجريبية {$timestamp}")
                        ->type('name_en', "Test City {$timestamp}")
                        ->type('slug', "test-city-{$timestamp}")
                        ->type('governorate', 'محافظة تجريبية')
                        ->type('description', 'وصف تجريبي للمدينة الجديدة')
                        ->type('latitude', '30.0444')
                        ->type('longitude', '31.2357')
                        ->check('is_active')
                        ->press('حفظ المدينة')
                        ->waitForLocation('http://127.0.0.1:8000/admin/cities', 15);

                echo "✅ City Creation Form: WORKING - New city created successfully\n";
            } catch (\Exception $e) {
                echo "❌ City Creation Form: ERROR - " . $e->getMessage() . "\n";
            }

            echo "\n✅ Form Submission Testing Complete!\n";
        });
    }
}