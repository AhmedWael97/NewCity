<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Shop;
use App\Models\City;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

class CompleteAdminAutomationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Complete automated test suite for admin panel
     * This test will run through all major admin functionalities
     */
    public function testCompleteAdminWorkflow()
    {
        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@automation.test',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {
            $this->runCompleteAdminAutomation($browser);
        });
    }

    /**
     * Complete admin automation workflow
     */
    private function runCompleteAdminAutomation(Browser $browser)
    {
        echo "\n🚀 Starting Complete Admin Panel Automation...\n";

        // Step 1: Login
        $this->performAdminLogin($browser);
        
        // Step 2: Dashboard Overview
        $this->exploreDashboard($browser);
        
        // Step 3: User Management
        $this->performUserManagement($browser);
        
        // Step 4: City Management
        $this->performCityManagement($browser);
        
        // Step 5: Category Management  
        $this->performCategoryManagement($browser);
        
        // Step 6: Shop Management
        $this->performShopManagement($browser);
        
        // Step 7: Settings and Configuration
        $this->exploreSettings($browser);
        
        // Step 8: Reports and Analytics
        $this->exploreReportsAndAnalytics($browser);
        
        // Step 9: System Administration
        $this->performSystemAdministration($browser);
        
        // Step 10: Logout
        $this->performLogout($browser);

        echo "\n✅ Complete Admin Panel Automation Finished Successfully!\n";
    }

    /**
     * Step 1: Admin Login
     */
    private function performAdminLogin(Browser $browser)
    {
        echo "🔑 Step 1: Admin Login...\n";
        
        $browser->visit('/admin/login')
                ->assertSee('Login')
                ->type('email', 'admin@automation.test')
                ->type('password', 'password123')
                ->press('Login')
                ->assertPathIs('/admin')
                ->assertSee('Dashboard');
                
        echo "✅ Admin login successful\n";
    }

    /**
     * Step 2: Dashboard Exploration
     */
    private function exploreDashboard(Browser $browser)
    {
        echo "📊 Step 2: Dashboard Exploration...\n";
        
        $browser->assertSee('Total Users')
                ->assertSee('Total Shops')
                ->assertSee('Total Cities')
                ->assertSee('Recent Activity');
                
        // Check system health if available
        if ($browser->seeLink('System Health')) {
            $browser->clickLink('System Health')
                    ->assertSee('System Status')
                    ->visit('/admin'); // Go back to dashboard
        }
        
        echo "✅ Dashboard explored\n";
    }

    /**
     * Step 3: User Management
     */
    private function performUserManagement(Browser $browser)
    {
        echo "👥 Step 3: User Management...\n";
        
        // Navigate to users
        $browser->clickLink('Users')
                ->assertPathIs('/admin/users')
                ->assertSee('Users Management');
        
        // Create a new user
        $browser->clickLink('Add New User')
                ->type('name', 'Test User Auto')
                ->type('email', 'testuser@automation.test')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->select('user_type', 'user')
                ->press('Create User')
                ->assertSee('User created successfully');
        
        // Search for the user
        $browser->visit('/admin/users')
                ->type('search', 'Test User Auto')
                ->press('Search')
                ->assertSee('Test User Auto');
        
        // Edit the user (if edit button exists)
        $editSelector = "[data-action='edit']";
        if ($browser->element($editSelector)) {
            $browser->click($editSelector)
                    ->clear('name')
                    ->type('name', 'Updated Test User')
                    ->press('Update User')
                    ->assertSee('User updated successfully');
        }
        
        echo "✅ User management completed\n";
    }

    /**
     * Step 4: City Management
     */
    private function performCityManagement(Browser $browser)
    {
        echo "🏙️ Step 4: City Management...\n";
        
        // Navigate to cities
        $browser->clickLink('Cities')
                ->assertPathIs('/admin/cities')
                ->assertSee('Cities Management');
        
        // Create a new city
        $browser->clickLink('Add New City')
                ->type('name', 'Test City Auto')
                ->type('slug', 'test-city-auto')
                ->type('description', 'Automated test city')
                ->check('is_active')
                ->press('Create City')
                ->assertSee('City created successfully');
        
        // Search for the city
        $browser->visit('/admin/cities')
                ->type('search', 'Test City Auto')
                ->press('Search')
                ->assertSee('Test City Auto');
        
        echo "✅ City management completed\n";
    }

    /**
     * Step 5: Category Management
     */
    private function performCategoryManagement(Browser $browser)
    {
        echo "📂 Step 5: Category Management...\n";
        
        // Navigate to categories
        $browser->clickLink('Categories')
                ->assertPathIs('/admin/categories')
                ->assertSee('Categories Management');
        
        // Create a new category
        $browser->clickLink('Add New Category')
                ->type('name', 'Test Category Auto')
                ->type('slug', 'test-category-auto')
                ->type('description', 'Automated test category')
                ->check('is_active')
                ->press('Create Category')
                ->assertSee('Category created successfully');
        
        // View hierarchy if available
        if ($browser->seeLink('View Hierarchy')) {
            $browser->clickLink('View Hierarchy')
                    ->assertSee('Category Hierarchy');
        }
        
        echo "✅ Category management completed\n";
    }

    /**
     * Step 6: Shop Management
     */
    private function performShopManagement(Browser $browser)
    {
        echo "🏪 Step 6: Shop Management...\n";
        
        // Navigate to shops
        $browser->clickLink('Shops')
                ->assertPathIs('/admin/shops')
                ->assertSee('Shops Management');
        
        // Create a new shop (simplified - may need city and category IDs)
        $browser->clickLink('Add New Shop')
                ->type('name', 'Test Shop Auto')
                ->type('slug', 'test-shop-auto')
                ->type('description', 'Automated test shop')
                ->type('address', '123 Auto Test Street')
                ->type('phone', '+1234567890')
                ->type('email', 'testshop@automation.test');
        
        // Select city and category if dropdowns exist
        $cityOptions = $browser->elements('select[name="city_id"] option');
        if (count($cityOptions) > 1) {
            $browser->select('city_id', $cityOptions[1]->getAttribute('value'));
        }
        
        $categoryOptions = $browser->elements('select[name="category_id"] option');
        if (count($categoryOptions) > 1) {
            $browser->select('category_id', $categoryOptions[1]->getAttribute('value'));
        }
        
        $browser->press('Create Shop')
                ->assertSee('Shop created successfully');
        
        // View pending shops if available
        if ($browser->seeLink('Pending Review')) {
            $browser->clickLink('Pending Review')
                    ->assertSee('Pending Shops');
        }
        
        echo "✅ Shop management completed\n";
    }

    /**
     * Step 7: Settings Exploration
     */
    private function exploreSettings(Browser $browser)
    {
        echo "⚙️ Step 7: Settings Exploration...\n";
        
        if ($browser->seeLink('Settings')) {
            $browser->clickLink('Settings')
                    ->assertSee('Settings');
            
            // Try to clear cache if button exists
            if ($browser->seeInElement('button', 'Clear Cache')) {
                $browser->press('Clear Cache')
                        ->assertSee('Cache cleared');
            }
        }
        
        echo "✅ Settings explored\n";
    }

    /**
     * Step 8: Reports and Analytics
     */
    private function exploreReportsAndAnalytics(Browser $browser)
    {
        echo "📈 Step 8: Reports and Analytics...\n";
        
        // Analytics
        if ($browser->seeLink('Analytics')) {
            $browser->clickLink('Analytics')
                    ->assertSee('Analytics');
        }
        
        // Reports
        if ($browser->seeLink('Reports')) {
            $browser->clickLink('Reports')
                    ->assertSee('Reports');
        }
        
        echo "✅ Reports and analytics explored\n";
    }

    /**
     * Step 9: System Administration
     */
    private function performSystemAdministration(Browser $browser)
    {
        echo "🔧 Step 9: System Administration...\n";
        
        // Logs
        if ($browser->seeLink('Logs')) {
            $browser->clickLink('Logs')
                    ->assertSee('Logs');
        }
        
        // Support tickets if available
        if ($browser->seeLink('Support')) {
            $browser->clickLink('Support')
                    ->assertSee('Support');
        }
        
        echo "✅ System administration completed\n";
    }

    /**
     * Step 10: Logout
     */
    private function performLogout(Browser $browser)
    {
        echo "🚪 Step 10: Logout...\n";
        
        $browser->clickLink('Logout')
                ->assertPathIs('/admin/login')
                ->assertSee('Login');
                
        echo "✅ Logout successful\n";
    }

    /**
     * Test that runs multiple scenarios in parallel
     */
    public function testMultipleAdminScenarios()
    {
        // Create multiple admin users for concurrent testing
        $admin1 = User::factory()->create([
            'email' => 'admin1@automation.test',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        $admin2 = User::factory()->create([
            'email' => 'admin2@automation.test',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser1, Browser $browser2) {
            // Scenario 1: Admin 1 manages users
            $browser1->visit('/admin/login')
                     ->type('email', 'admin1@automation.test')
                     ->type('password', 'password123')
                     ->press('Login')
                     ->clickLink('Users')
                     ->assertSee('Users Management');

            // Scenario 2: Admin 2 manages shops
            $browser2->visit('/admin/login')
                     ->type('email', 'admin2@automation.test')
                     ->type('password', 'password123')
                     ->press('Login')
                     ->clickLink('Shops')
                     ->assertSee('Shops Management');
        });
    }
}