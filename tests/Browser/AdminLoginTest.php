<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminLoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test admin login functionality
     */
    public function testAdminCanLogin()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                    ->assertSee('Login')
                    ->type('email', 'admin@test.com')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/admin')
                    ->assertSee('Dashboard');
        });
    }

    /**
     * Test admin login with invalid credentials
     */
    public function testAdminCannotLoginWithInvalidCredentials()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                    ->type('email', 'wrong@test.com')
                    ->type('password', 'wrongpassword')
                    ->press('Login')
                    ->assertPathIs('/admin/login')
                    ->assertSee('These credentials do not match our records');
        });
    }

    /**
     * Test admin logout
     */
    public function testAdminCanLogout()
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                    ->type('email', 'admin@test.com')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/admin')
                    ->clickLink('Logout')
                    ->assertPathIs('/admin/login');
        });
    }
}