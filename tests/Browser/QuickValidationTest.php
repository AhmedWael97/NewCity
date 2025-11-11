<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class QuickValidationTest extends DuskTestCase
{
    /**
     * Quick validation test for critical new views
     */
    public function test_quick_validation_of_key_views()
    {
        $this->browse(function (Browser $browser) {
            echo "\n⚡ Quick Validation Test\n";
            echo "=======================\n";

            // Login
            $browser->visit('http://127.0.0.1:8000/admin/login')
                    ->waitFor('input[name="email"]', 10)
                    ->type('email', 'admin@city.com')
                    ->type('password', 'superadminpassword')
                    ->press('تسجيل الدخول')
                    ->waitForLocation('http://127.0.0.1:8000/admin', 15);

            echo "✅ Login: Success\n";

            // Test key views that should definitely work now
            $tests = [
                [
                    'url' => 'http://127.0.0.1:8000/admin/cities/create',
                    'selector' => 'input[name="name"]',
                    'title' => 'إضافة مدينة جديدة',
                    'name' => 'Cities Create'
                ],
                [
                    'url' => 'http://127.0.0.1:8000/admin/categories/create',
                    'selector' => 'input[name="name"]',
                    'title' => 'إضافة فئة جديدة',
                    'name' => 'Categories Create'
                ],
                [
                    'url' => 'http://127.0.0.1:8000/admin/subscription-plans/create',
                    'selector' => 'input[name="name"]',
                    'title' => 'إضافة خطة اشتراك جديدة',
                    'name' => 'Subscription Plans Create'
                ]
            ];

            foreach ($tests as $test) {
                try {
                    $browser->visit($test['url'])
                            ->waitFor($test['selector'], 10)
                            ->assertSee($test['title']);
                    echo "✅ {$test['name']}: WORKING\n";
                } catch (\Exception $e) {
                    echo "❌ {$test['name']}: FAILED - " . $e->getMessage() . "\n";
                }
            }

            echo "\n🎯 Quick Validation Complete!\n";
        });
    }
}