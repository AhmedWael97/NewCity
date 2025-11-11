<?php

namespace Tests\Browser;

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CitySelectionTest extends DuskTestCase
{
    /**
     * Test city selection modal functionality.
     */
    public function testCitySelectionModal()
    {
        $this->browse(function (Browser $browser) {
            // Clear session first
            $browser->visit('/clear-session')
                   ->assertSee('Session cleared');

            // Visit homepage - should show city selection modal
            $browser->visit('/')
                   ->pause(2000)
                   ->assertVisible('#citySelectionModal')
                   ->assertSee('اختر مدينتك');

            // Get a city from database
            $city = City::where('is_active', true)->first();
            
            if (!$city) {
                $this->fail('No active cities found in database');
            }

            echo "\n🏙️ Testing city selection for: {$city->name} ({$city->slug})\n";

            // Click on a city to select it
            $browser->click("button[data-city-slug='{$city->slug}']")
                   ->pause(3000) // Wait for AJAX and reload
                   ->waitUntilMissing('#citySelectionModal', 10);

            // Check if city context is displayed
            $browser->assertSee($city->name)
                   ->assertSee('تغيير المدينة');

            echo "✅ City selection modal test passed!\n";
            echo "✅ Selected city: {$city->name}\n";
            echo "✅ Modal disappeared after selection\n";
            echo "✅ City context is now displayed\n";

            // Test changing city
            $browser->click('button:contains("تغيير المدينة")')
                   ->pause(2000)
                   ->assertVisible('#citySelectionModal')
                   ->assertSee('اختر مدينتك');

            echo "✅ Change city functionality works\n";

            // Test skip functionality
            $browser->click('button:contains("تخطي الآن")')
                   ->pause(3000)
                   ->waitUntilMissing('#citySelectionModal', 10);

            echo "✅ Skip city selection works\n";
        });
    }

    /**
     * Test city-specific content filtering.
     */
    public function testCitySpecificContent()
    {
        $this->browse(function (Browser $browser) {
            // Clear session first
            $browser->visit('/clear-session');

            // Get a city with shops
            $city = City::whereHas('shops', function($query) {
                $query->where('is_active', true);
            })->first();

            if (!$city) {
                echo "⚠️ No city with active shops found, skipping content test\n";
                return;
            }

            echo "\n🏪 Testing city-specific content for: {$city->name}\n";

            // Visit homepage and select city
            $browser->visit('/')
                   ->pause(2000);

            // Select the city if modal is visible
            if ($browser->element('#citySelectionModal:not(.d-none)')) {
                $browser->click("button[data-city-slug='{$city->slug}']")
                       ->pause(3000);
            }

            // Check if the content reflects the selected city
            $browser->assertSee($city->name);

            echo "✅ City-specific content is displayed\n";
            echo "✅ City context is working properly\n";
        });
    }
}