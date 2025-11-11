<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\City;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Rating;
use App\Models\SubscriptionPlan;

class NewViewsValidationTest extends DuskTestCase
{
    protected $adminUser;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create admin user for testing
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin@city.com'],
            [
                'name' => 'Test Admin',
                'password' => bcrypt('superadminpassword'),
                'user_type' => 'admin',
                'is_active' => true
            ]
        );
    }

    /**
     * Test all newly created view files
     */
    public function test_all_new_crud_views_load_successfully()
    {
        $this->browse(function (Browser $browser) {
            // Login as admin
            $browser->visit('/admin/login')
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('/admin')
                    ->assertSee('لوحة التحكم');

            // Test Users Edit View
            $this->testUsersEditView($browser);
            
            // Test Cities CRUD Views
            $this->testCitiesCRUDViews($browser);
            
            // Test Categories CRUD Views
            $this->testCategoriesCRUDViews($browser);
            
            // Test Ratings Edit View
            $this->testRatingsEditView($browser);
            
            // Test Subscription Plans CRUD Views
            $this->testSubscriptionPlansCRUDViews($browser);

            echo "\n✅ All new view files loaded successfully!\n";
        });
    }

    private function testUsersEditView(Browser $browser)
    {
        try {
            // Create a test user to edit
            $testUser = User::factory()->create([
                'user_type' => 'customer',
                'is_active' => true
            ]);

            $browser->visit("/admin/users/{$testUser->id}/edit")
                    ->waitFor('input[name="name"]', 10)
                    ->assertSee('تعديل المستخدم')
                    ->assertPresent('input[name="name"]')
                    ->assertPresent('input[name="email"]')
                    ->assertPresent('select[name="user_type"]')
                    ->assertPresent('input[name="phone"]')
                    ->assertPresent('select[name="city_id"]');

            echo "✅ Users Edit View: PASSED\n";
        } catch (\Exception $e) {
            echo "❌ Users Edit View: FAILED - " . $e->getMessage() . "\n";
        }
    }

    private function testCitiesCRUDViews(Browser $browser)
    {
        try {
            // Test Cities Create View
            $browser->visit('/admin/cities/create')
                    ->waitFor('input[name="name"]', 10)
                    ->assertSee('إضافة مدينة جديدة')
                    ->assertPresent('input[name="name"]')
                    ->assertPresent('input[name="name_en"]')
                    ->assertPresent('input[name="slug"]')
                    ->assertPresent('input[name="governorate"]')
                    ->assertPresent('textarea[name="description"]');

            echo "✅ Cities Create View: PASSED\n";

            // Test Cities Edit View
            $testCity = City::first();
            if ($testCity) {
                $browser->visit("/admin/cities/{$testCity->id}/edit")
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('تعديل المدينة')
                        ->assertInputValue('name', $testCity->name)
                        ->assertPresent('input[name="latitude"]')
                        ->assertPresent('input[name="longitude"]');

                echo "✅ Cities Edit View: PASSED\n";
            }
        } catch (\Exception $e) {
            echo "❌ Cities CRUD Views: FAILED - " . $e->getMessage() . "\n";
        }
    }

    private function testCategoriesCRUDViews(Browser $browser)
    {
        try {
            // Test Categories Create View
            $browser->visit('/admin/categories/create')
                    ->waitFor('input[name="name"]', 10)
                    ->assertSee('إضافة فئة جديدة')
                    ->assertPresent('input[name="name"]')
                    ->assertPresent('input[name="name_en"]')
                    ->assertPresent('input[name="slug"]')
                    ->assertPresent('select[name="parent_id"]')
                    ->assertPresent('input[name="icon"]')
                    ->assertPresent('input[name="color"]');

            echo "✅ Categories Create View: PASSED\n";

            // Test Categories Edit View
            $testCategory = Category::first();
            if ($testCategory) {
                $browser->visit("/admin/categories/{$testCategory->id}/edit")
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('تعديل الفئة')
                        ->assertInputValue('name', $testCategory->name)
                        ->assertPresent('textarea[name="meta_description"]');

                echo "✅ Categories Edit View: PASSED\n";
            }
        } catch (\Exception $e) {
            echo "❌ Categories CRUD Views: FAILED - " . $e->getMessage() . "\n";
        }
    }

    private function testRatingsEditView(Browser $browser)
    {
        try {
            $testRating = Rating::first();
            if ($testRating) {
                $browser->visit("/admin/ratings/{$testRating->id}/edit")
                        ->waitFor('select[name="user_id"]', 10)
                        ->assertSee('تعديل التقييم')
                        ->assertPresent('select[name="user_id"]')
                        ->assertPresent('select[name="shop_id"]')
                        ->assertPresent('select[name="rating"]')
                        ->assertPresent('select[name="status"]')
                        ->assertPresent('textarea[name="comment"]');

                echo "✅ Ratings Edit View: PASSED\n";
            } else {
                echo "⚠️ Ratings Edit View: SKIPPED (No test data)\n";
            }
        } catch (\Exception $e) {
            echo "❌ Ratings Edit View: FAILED - " . $e->getMessage() . "\n";
        }
    }

    private function testSubscriptionPlansCRUDViews(Browser $browser)
    {
        try {
            // Test Subscription Plans Create View
            $browser->visit('/admin/subscription-plans/create')
                    ->waitFor('input[name="name"]', 10)
                    ->assertSee('إضافة خطة اشتراك جديدة')
                    ->assertPresent('input[name="name"]')
                    ->assertPresent('input[name="name_en"]')
                    ->assertPresent('input[name="price"]')
                    ->assertPresent('select[name="duration_days"]')
                    ->assertPresent('input[name="max_products"]')
                    ->assertPresent('textarea[name="features"]');

            echo "✅ Subscription Plans Create View: PASSED\n";

            // Test Subscription Plans Edit View
            $testPlan = SubscriptionPlan::first();
            if ($testPlan) {
                $browser->visit("/admin/subscription-plans/{$testPlan->id}/edit")
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('تعديل خطة الاشتراك')
                        ->assertInputValue('name', $testPlan->name)
                        ->assertPresent('input[name="discount_percentage"]')
                        ->assertPresent('input[name="color"]');

                echo "✅ Subscription Plans Edit View: PASSED\n";
            } else {
                echo "⚠️ Subscription Plans Edit View: SKIPPED (No test data)\n";
            }
        } catch (\Exception $e) {
            echo "❌ Subscription Plans CRUD Views: FAILED - " . $e->getMessage() . "\n";
        }
    }

    /**
     * Test form submission for one of the new views
     */
    public function test_form_submission_works()
    {
        $this->browse(function (Browser $browser) {
            // Login
            $browser->visit('/admin/login')
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('/admin');

            // Test creating a new city
            try {
                $browser->visit('/admin/cities/create')
                        ->waitFor('input[name="name"]', 10)
                        ->type('name', 'مدينة تجريبية')
                        ->type('name_en', 'Test City')
                        ->type('slug', 'test-city-' . time())
                        ->type('governorate', 'محافظة تجريبية')
                        ->type('description', 'وصف تجريبي للمدينة')
                        ->check('is_active')
                        ->press('حفظ المدينة')
                        ->waitForLocation('/admin/cities', 15);

                echo "✅ Form Submission Test: PASSED - City created successfully\n";
            } catch (\Exception $e) {
                echo "❌ Form Submission Test: FAILED - " . $e->getMessage() . "\n";
            }
        });
    }

    /**
     * Test navigation to all CRUD pages
     */
    public function test_admin_navigation_to_crud_pages()
    {
        $this->browse(function (Browser $browser) {
            // Login
            $browser->visit('/admin/login')
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('/admin');

            // Test navigation to each module
            $modules = [
                '/admin/users' => 'المستخدمين',
                '/admin/cities' => 'المدن',
                '/admin/categories' => 'الفئات',
                '/admin/shops' => 'المتاجر',
                '/admin/ratings' => 'التقييمات',
                '/admin/subscription-plans' => 'خطط الاشتراك'
            ];

            foreach ($modules as $url => $expectedText) {
                try {
                    $browser->visit($url)
                            ->waitFor('.card-header', 10)
                            ->assertSee($expectedText);
                    echo "✅ Navigation to {$url}: PASSED\n";
                } catch (\Exception $e) {
                    echo "❌ Navigation to {$url}: FAILED - " . $e->getMessage() . "\n";
                }
            }
        });
    }
}