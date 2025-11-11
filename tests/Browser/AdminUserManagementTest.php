<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserManagementTest extends DuskTestCase
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
     * Test viewing users list
     */
    public function testAdminCanViewUsersList()
    {
        $this->createAdminUser();
        
        // Create some regular users
        User::factory()->count(3)->create(['user_type' => 'user']);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->clickLink('Users')
                    ->assertPathIs('/admin/users')
                    ->assertSee('Users Management')
                    ->assertSee('Add New User');
        });
    }

    /**
     * Test creating a new user
     */
    public function testAdminCanCreateUser()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/users')
                    ->clickLink('Add New User')
                    ->assertSee('Create User')
                    ->type('name', 'Test User')
                    ->type('email', 'testuser@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->select('user_type', 'user')
                    ->press('Create User')
                    ->assertSee('User created successfully')
                    ->assertSee('Test User');
        });
    }

    /**
     * Test editing a user
     */
    public function testAdminCanEditUser()
    {
        $this->createAdminUser();
        
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'user_type' => 'user'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/users')
                    ->click("[data-action='edit'][data-id='{$user->id}']")
                    ->assertSee('Edit User')
                    ->clear('name')
                    ->type('name', 'Updated Name')
                    ->clear('email')
                    ->type('email', 'updated@example.com')
                    ->press('Update User')
                    ->assertSee('User updated successfully')
                    ->assertSee('Updated Name');
        });
    }

    /**
     * Test deleting a user
     */
    public function testAdminCanDeleteUser()
    {
        $this->createAdminUser();
        
        $user = User::factory()->create([
            'name' => 'User To Delete',
            'user_type' => 'user'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/users')
                    ->assertSee('User To Delete')
                    ->click("[data-action='delete'][data-id='{$user->id}']")
                    ->acceptDialog()
                    ->assertSee('User deleted successfully')
                    ->assertDontSee('User To Delete');
        });
    }

    /**
     * Test toggling user status
     */
    public function testAdminCanToggleUserStatus()
    {
        $this->createAdminUser();
        
        $user = User::factory()->create([
            'name' => 'Status Test User',
            'user_type' => 'user',
            'is_active' => true
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/users')
                    ->assertSee('Status Test User')
                    ->click("[data-action='toggle-status'][data-id='{$user->id}']")
                    ->assertSee('User status updated successfully');
        });
    }

    /**
     * Test bulk actions on users
     */
    public function testAdminCanPerformBulkActions()
    {
        $this->createAdminUser();
        
        // Create multiple users
        $users = User::factory()->count(3)->create(['user_type' => 'user']);

        $this->browse(function (Browser $browser) use ($users) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/users')
                    ->check("user-checkbox-{$users[0]->id}")
                    ->check("user-checkbox-{$users[1]->id}")
                    ->select('bulk_action', 'activate')
                    ->press('Apply')
                    ->assertSee('Bulk action completed successfully');
        });
    }

    /**
     * Test user search functionality
     */
    public function testAdminCanSearchUsers()
    {
        $this->createAdminUser();
        
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_type' => 'user'
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'user_type' => 'user'
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/users')
                    ->type('search', 'John')
                    ->press('Search')
                    ->assertSee('John Doe')
                    ->assertDontSee('Jane Smith');
        });
    }
}