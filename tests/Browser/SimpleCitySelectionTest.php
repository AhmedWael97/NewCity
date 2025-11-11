<?php

namespace Tests\Browser;

use App\Models\City;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SimpleCitySelectionTest extends DuskTestCase
{
    /**
     * Test city selection modal basic functionality.
     */
    public function testBasicCitySelection()
    {
        $this->browse(function (Browser $browser) {
            echo "\n🚀 Testing City Selection Modal Functionality\n";
            echo "=".str_repeat("=", 50)."\n";

            // Visit homepage
            echo "📍 Step 1: Visiting homepage...\n";
            $browser->visit('/')
                   ->pause(2000);

            // Check if modal appears (depends on session state)
            if ($browser->element('#citySelectionModal:not(.d-none)')) {
                echo "✅ City selection modal is visible\n";
                
                // Try to select a city
                $browser->pause(1000);
                
                // Get first available city button
                $cityButtons = $browser->elements('button[data-city-slug]');
                
                if (count($cityButtons) > 0) {
                    $firstButton = $cityButtons[0];
                    $citySlug = $firstButton->getAttribute('data-city-slug');
                    $cityName = $firstButton->getAttribute('data-city-name');
                    
                    echo "🏙️ Selecting city: {$cityName} ({$citySlug})\n";
                    
                    // Click the city
                    $browser->click("button[data-city-slug='{$citySlug}']")
                           ->pause(4000) // Wait for AJAX and page reload
                           ->waitUntilMissing('#citySelectionModal', 10);
                    
                    echo "✅ City selected successfully!\n";
                    echo "✅ Modal disappeared\n";
                    
                    // Check if city context is displayed
                    $browser->assertSee($cityName);
                    echo "✅ City context is displayed: {$cityName}\n";
                    
                } else {
                    echo "⚠️ No city buttons found in modal\n";
                }
                
            } else {
                echo "ℹ️ City selection modal is not visible (city may already be selected)\n";
                
                // Check if change city button exists
                if ($browser->element('button:contains("تغيير المدينة")')) {
                    echo "✅ Change city button is available\n";
                    
                    // Test changing city
                    $browser->click('button:contains("تغيير المدينة")')
                           ->pause(2000)
                           ->assertVisible('#citySelectionModal');
                    
                    echo "✅ City selection modal opened via change button\n";
                } else {
                    echo "ℹ️ No change city button found\n";
                }
            }

            echo "\n🎉 City Selection Test Completed!\n";
            echo "=".str_repeat("=", 50)."\n";
        });
    }
}