<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Shop;
use App\Models\City;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class AdminShopManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Setup admin user for tests
     */
    private function createAdminUser()
    {
        return User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);
    }

    /**
     * Login as admin
     */
    private function loginAsAdmin(Browser $browser)
    {
        $browser->visit('/admin/login')
                ->type('email', 'admin@test.com')
                ->type('password', 'password123')
                ->press('Login')
                ->assertPathIs('/admin');
    }

    /**
     * Setup test data
     */
    private function setupTestData()
    {
        $city = City::factory()->create(['name' => 'Test City']);
        $category = Category::factory()->create(['name' => 'Test Category']);
        
        return ['city' => $city, 'category' => $category];
    }

    /**
     * Test viewing shops list
     */
    public function testAdminCanViewShopsList()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        Shop::factory()->count(3)->create([
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->clickLink('Shops')
                    ->assertPathIs('/admin/shops')
                    ->assertSee('Shops Management')
                    ->assertSee('Add New Shop');
        });
    }

    /**
     * Test creating a new shop
     */
    public function testAdminCanCreateShop()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();

        $this->browse(function (Browser $browser) use ($data) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops')
                    ->clickLink('Add New Shop')
                    ->assertSee('Create Shop')
                    ->type('name', 'Test Shop')
                    ->type('slug', 'test-shop')
                    ->type('description', 'This is a test shop description')
                    ->type('address', '123 Test Street')
                    ->type('phone', '+1234567890')
                    ->type('email', 'testshop@example.com')
                    ->select('city_id', $data['city']->id)
                    ->select('category_id', $data['category']->id)
                    ->press('Create Shop')
                    ->assertSee('Shop created successfully')
                    ->assertSee('Test Shop');
        });
    }

    /**
     * Test editing a shop
     */
    public function testAdminCanEditShop()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        $shop = Shop::factory()->create([
            'name' => 'Original Shop Name',
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id
        ]);

        $this->browse(function (Browser $browser) use ($shop) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops')
                    ->click("[data-action='edit'][data-id='{$shop->id}']")
                    ->assertSee('Edit Shop')
                    ->clear('name')
                    ->type('name', 'Updated Shop Name')
                    ->clear('description')
                    ->type('description', 'Updated shop description')
                    ->press('Update Shop')
                    ->assertSee('Shop updated successfully')
                    ->assertSee('Updated Shop Name');
        });
    }

    /**
     * Test deleting a shop
     */
    public function testAdminCanDeleteShop()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        $shop = Shop::factory()->create([
            'name' => 'Shop To Delete',
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id
        ]);

        $this->browse(function (Browser $browser) use ($shop) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops')
                    ->assertSee('Shop To Delete')
                    ->click("[data-action='delete'][data-id='{$shop->id}']")
                    ->acceptDialog()
                    ->assertSee('Shop deleted successfully')
                    ->assertDontSee('Shop To Delete');
        });
    }

    /**
     * Test toggling shop status
     */
    public function testAdminCanToggleShopStatus()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        $shop = Shop::factory()->create([
            'name' => 'Status Test Shop',
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id,
            'is_active' => true
        ]);

        $this->browse(function (Browser $browser) use ($shop) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops')
                    ->assertSee('Status Test Shop')
                    ->click("[data-action='toggle-status'][data-id='{$shop->id}']")
                    ->assertSee('Shop status updated successfully');
        });
    }

    /**
     * Test featuring/unfeaturing a shop
     */
    public function testAdminCanToggleShopFeature()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        $shop = Shop::factory()->create([
            'name' => 'Feature Test Shop',
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id,
            'is_featured' => false
        ]);

        $this->browse(function (Browser $browser) use ($shop) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops')
                    ->assertSee('Feature Test Shop')
                    ->click("[data-action='feature'][data-id='{$shop->id}']")
                    ->assertSee('Shop feature status updated successfully');
        });
    }

    /**
     * Test verifying a shop
     */
    public function testAdminCanVerifyShop()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        $shop = Shop::factory()->create([
            'name' => 'Verification Test Shop',
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id,
            'is_verified' => false
        ]);

        $this->browse(function (Browser $browser) use ($shop) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops')
                    ->assertSee('Verification Test Shop')
                    ->click("[data-action='verify'][data-id='{$shop->id}']")
                    ->assertSee('Shop verification status updated successfully');
        });
    }

    /**
     * Test viewing pending shops
     */
    public function testAdminCanViewPendingShops()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        Shop::factory()->create([
            'name' => 'Pending Shop',
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id,
            'is_verified' => false,
            'status' => 'pending'
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops/pending/review')
                    ->assertSee('Pending Shops')
                    ->assertSee('Pending Shop');
        });
    }

    /**
     * Test shop search functionality
     */
    public function testAdminCanSearchShops()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        Shop::factory()->create([
            'name' => 'Pizza Palace',
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id
        ]);

        Shop::factory()->create([
            'name' => 'Burger King',
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops')
                    ->type('search', 'Pizza')
                    ->press('Search')
                    ->assertSee('Pizza Palace')
                    ->assertDontSee('Burger King');
        });
    }

    /**
     * Test bulk actions on shops
     */
    public function testAdminCanPerformBulkActionsOnShops()
    {
        $this->createAdminUser();
        $data = $this->setupTestData();
        
        $shops = Shop::factory()->count(3)->create([
            'city_id' => $data['city']->id,
            'category_id' => $data['category']->id
        ]);

        $this->browse(function (Browser $browser) use ($shops) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/shops')
                    ->check("shop-checkbox-{$shops[0]->id}")
                    ->check("shop-checkbox-{$shops[1]->id}")
                    ->select('bulk_action', 'activate')
                    ->press('Apply')
                    ->assertSee('Bulk action completed successfully');
        });
    }
}