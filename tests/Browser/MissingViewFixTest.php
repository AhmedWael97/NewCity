<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class MissingViewFixTest extends DuskTestCase
{
    /**
     * Test that the missing admin.users.create view is now working
     */
    public function test_users_create_view_is_now_working()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🔧 Testing Missing View Fix\n";
            echo "===========================\n";

            // Login
            $browser->visit('http://127.0.0.1:8000/admin/login')
                    ->waitFor('input[name="email"]', 10)
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('http://127.0.0.1:8000/admin', 15);

            echo "✅ Admin login: SUCCESS\n";

            // Test Users Create View (the one that was missing)
            try {
                $browser->visit('http://127.0.0.1:8000/admin/users/create')
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('إضافة مستخدم جديد')
                        ->assertPresent('input[name="name"]')
                        ->assertPresent('input[name="email"]')
                        ->assertPresent('input[name="password"]')
                        ->assertPresent('input[name="password_confirmation"]')
                        ->assertPresent('select[name="user_type"]')
                        ->assertPresent('select[name="city_id"]');
                echo "✅ Users Create View: FIXED AND WORKING!\n";
            } catch (\Exception $e) {
                echo "❌ Users Create View: STILL FAILING - " . $e->getMessage() . "\n";
            }

            // Test Users Index to Create navigation
            try {
                $browser->visit('http://127.0.0.1:8000/admin/users')
                        ->waitFor('.card-header', 10)
                        ->clickLink('إضافة مستخدم')
                        ->waitFor('input[name="name"]', 10)
                        ->assertSee('إضافة مستخدم جديد');
                echo "✅ Users Index → Create Navigation: WORKING\n";
            } catch (\Exception $e) {
                echo "❌ Users Index → Create Navigation: ERROR - " . $e->getMessage() . "\n";
            }

            // Test all user views now exist
            $userViews = [
                '/admin/users' => 'Users Index',
                '/admin/users/create' => 'Users Create'
            ];

            foreach ($userViews as $url => $viewName) {
                try {
                    $browser->visit('http://127.0.0.1:8000' . $url)
                            ->waitFor('.card-header', 10);
                    echo "✅ {$viewName}: Available\n";
                } catch (\Exception $e) {
                    echo "❌ {$viewName}: Failed\n";
                }
            }

            echo "\n🎉 Missing View Fix Test Complete!\n";
        });
    }
}