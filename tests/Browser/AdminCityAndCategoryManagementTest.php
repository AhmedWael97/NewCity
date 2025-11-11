<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\City;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class AdminCityAndCategoryManagementTest extends DuskTestCase
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
     * Test viewing cities list
     */
    public function testAdminCanViewCitiesList()
    {
        $this->createAdminUser();
        City::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->clickLink('Cities')
                    ->assertPathIs('/admin/cities')
                    ->assertSee('Cities Management')
                    ->assertSee('Add New City');
        });
    }

    /**
     * Test creating a new city
     */
    public function testAdminCanCreateCity()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/cities')
                    ->clickLink('Add New City')
                    ->assertSee('Create City')
                    ->type('name', 'Test City')
                    ->type('slug', 'test-city')
                    ->type('description', 'This is a test city')
                    ->check('is_active')
                    ->press('Create City')
                    ->assertSee('City created successfully')
                    ->assertSee('Test City');
        });
    }

    /**
     * Test editing a city
     */
    public function testAdminCanEditCity()
    {
        $this->createAdminUser();
        
        $city = City::factory()->create([
            'name' => 'Original City Name',
            'slug' => 'original-city'
        ]);

        $this->browse(function (Browser $browser) use ($city) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/cities')
                    ->click("[data-action='edit'][data-id='{$city->id}']")
                    ->assertSee('Edit City')
                    ->clear('name')
                    ->type('name', 'Updated City Name')
                    ->clear('slug')
                    ->type('slug', 'updated-city')
                    ->press('Update City')
                    ->assertSee('City updated successfully')
                    ->assertSee('Updated City Name');
        });
    }

    /**
     * Test deleting a city
     */
    public function testAdminCanDeleteCity()
    {
        $this->createAdminUser();
        
        $city = City::factory()->create(['name' => 'City To Delete']);

        $this->browse(function (Browser $browser) use ($city) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/cities')
                    ->assertSee('City To Delete')
                    ->click("[data-action='delete'][data-id='{$city->id}']")
                    ->acceptDialog()
                    ->assertSee('City deleted successfully')
                    ->assertDontSee('City To Delete');
        });
    }

    /**
     * Test toggling city active status
     */
    public function testAdminCanToggleCityActiveStatus()
    {
        $this->createAdminUser();
        
        $city = City::factory()->create([
            'name' => 'Active Test City',
            'is_active' => true
        ]);

        $this->browse(function (Browser $browser) use ($city) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/cities')
                    ->assertSee('Active Test City')
                    ->click("[data-action='toggle-active'][data-id='{$city->id}']")
                    ->assertSee('City status updated successfully');
        });
    }

    /**
     * Test viewing categories list
     */
    public function testAdminCanViewCategoriesList()
    {
        $this->createAdminUser();
        Category::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->clickLink('Categories')
                    ->assertPathIs('/admin/categories')
                    ->assertSee('Categories Management')
                    ->assertSee('Add New Category');
        });
    }

    /**
     * Test creating a new category
     */
    public function testAdminCanCreateCategory()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/categories')
                    ->clickLink('Add New Category')
                    ->assertSee('Create Category')
                    ->type('name', 'Test Category')
                    ->type('slug', 'test-category')
                    ->type('description', 'This is a test category')
                    ->check('is_active')
                    ->press('Create Category')
                    ->assertSee('Category created successfully')
                    ->assertSee('Test Category');
        });
    }

    /**
     * Test editing a category
     */
    public function testAdminCanEditCategory()
    {
        $this->createAdminUser();
        
        $category = Category::factory()->create([
            'name' => 'Original Category Name',
            'slug' => 'original-category'
        ]);

        $this->browse(function (Browser $browser) use ($category) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/categories')
                    ->click("[data-action='edit'][data-id='{$category->id}']")
                    ->assertSee('Edit Category')
                    ->clear('name')
                    ->type('name', 'Updated Category Name')
                    ->clear('slug')
                    ->type('slug', 'updated-category')
                    ->press('Update Category')
                    ->assertSee('Category updated successfully')
                    ->assertSee('Updated Category Name');
        });
    }

    /**
     * Test deleting a category
     */
    public function testAdminCanDeleteCategory()
    {
        $this->createAdminUser();
        
        $category = Category::factory()->create(['name' => 'Category To Delete']);

        $this->browse(function (Browser $browser) use ($category) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/categories')
                    ->assertSee('Category To Delete')
                    ->click("[data-action='delete'][data-id='{$category->id}']")
                    ->acceptDialog()
                    ->assertSee('Category deleted successfully')
                    ->assertDontSee('Category To Delete');
        });
    }

    /**
     * Test toggling category active status
     */
    public function testAdminCanToggleCategoryActiveStatus()
    {
        $this->createAdminUser();
        
        $category = Category::factory()->create([
            'name' => 'Active Test Category',
            'is_active' => true
        ]);

        $this->browse(function (Browser $browser) use ($category) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/categories')
                    ->assertSee('Active Test Category')
                    ->click("[data-action='toggle-active'][data-id='{$category->id}']")
                    ->assertSee('Category status updated successfully');
        });
    }

    /**
     * Test viewing category hierarchy
     */
    public function testAdminCanViewCategoryHierarchy()
    {
        $this->createAdminUser();
        
        $parent = Category::factory()->create(['name' => 'Parent Category']);
        Category::factory()->create([
            'name' => 'Child Category',
            'parent_id' => $parent->id
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/categories/hierarchy')
                    ->assertSee('Category Hierarchy')
                    ->assertSee('Parent Category')
                    ->assertSee('Child Category');
        });
    }

    /**
     * Test searching cities
     */
    public function testAdminCanSearchCities()
    {
        $this->createAdminUser();
        
        City::factory()->create(['name' => 'Cairo', 'slug' => 'cairo']);
        City::factory()->create(['name' => 'Alexandria', 'slug' => 'alexandria']);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/cities')
                    ->type('search', 'Cairo')
                    ->press('Search')
                    ->assertSee('Cairo')
                    ->assertDontSee('Alexandria');
        });
    }

    /**
     * Test searching categories
     */
    public function testAdminCanSearchCategories()
    {
        $this->createAdminUser();
        
        Category::factory()->create(['name' => 'Restaurant', 'slug' => 'restaurant']);
        Category::factory()->create(['name' => 'Shopping', 'slug' => 'shopping']);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/categories')
                    ->type('search', 'Restaurant')
                    ->press('Search')
                    ->assertSee('Restaurant')
                    ->assertDontSee('Shopping');
        });
    }
}