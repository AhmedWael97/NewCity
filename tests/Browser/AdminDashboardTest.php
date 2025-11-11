<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminDashboardTest extends DuskTestCase
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
     * Test admin dashboard loads properly
     */
    public function testAdminDashboardLoads()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->assertSee('Dashboard')
                    ->assertSee('Total Users')
                    ->assertSee('Total Shops')
                    ->assertSee('Total Cities')
                    ->assertSee('Recent Activity');
        });
    }

    /**
     * Test navigation links work
     */
    public function testAdminNavigationLinks()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            // Test Users link
            $browser->clickLink('Users')
                    ->assertPathIs('/admin/users')
                    ->back();
            
            // Test Shops link
            $browser->clickLink('Shops')
                    ->assertPathIs('/admin/shops')
                    ->back();
            
            // Test Cities link
            $browser->clickLink('Cities')
                    ->assertPathIs('/admin/cities')
                    ->back();
            
            // Test Categories link
            $browser->clickLink('Categories')
                    ->assertPathIs('/admin/categories')
                    ->back();
        });
    }

    /**
     * Test system health check
     */
    public function testAdminCanViewSystemHealth()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/system-health')
                    ->assertSee('System Health')
                    ->assertSee('Database Status')
                    ->assertSee('Cache Status')
                    ->assertSee('Queue Status');
        });
    }

    /**
     * Test analytics page
     */
    public function testAdminCanViewAnalytics()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->clickLink('Analytics')
                    ->assertPathIs('/admin/analytics')
                    ->assertSee('Analytics Dashboard')
                    ->assertSee('User Statistics')
                    ->assertSee('Shop Performance');
        });
    }

    /**
     * Test settings page
     */
    public function testAdminCanViewSettings()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->clickLink('Settings')
                    ->assertPathIs('/admin/settings')
                    ->assertSee('System Settings')
                    ->assertSee('Application Name')
                    ->assertSee('Email Settings');
        });
    }

    /**
     * Test reports generation
     */
    public function testAdminCanGenerateReports()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/reports')
                    ->assertSee('Reports')
                    ->select('report_type', 'users')
                    ->press('Generate Report')
                    ->assertSee('Report generated successfully');
        });
    }

    /**
     * Test clear cache functionality
     */
    public function testAdminCanClearCache()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/settings')
                    ->press('Clear Cache')
                    ->assertSee('Cache cleared successfully');
        });
    }

    /**
     * Test logs viewer
     */
    public function testAdminCanViewLogs()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->visit('/admin/logs')
                    ->assertSee('System Logs')
                    ->assertSee('Download Logs')
                    ->assertSee('Clear Logs');
        });
    }

    /**
     * Test admin profile update
     */
    public function testAdminCanUpdateProfile()
    {
        $admin = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($admin) {
            $this->loginAsAdmin($browser);
            
            $browser->clickLink('Profile')
                    ->assertSee('Profile Settings')
                    ->clear('name')
                    ->type('name', 'Updated Admin Name')
                    ->press('Update Profile')
                    ->assertSee('Profile updated successfully')
                    ->assertSee('Updated Admin Name');
        });
    }

    /**
     * Test password change
     */
    public function testAdminCanChangePassword()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            $browser->clickLink('Profile')
                    ->type('current_password', 'password123')
                    ->type('password', 'newpassword123')
                    ->type('password_confirmation', 'newpassword123')
                    ->press('Change Password')
                    ->assertSee('Password changed successfully');
        });
    }

    /**
     * Test responsive design works
     */
    public function testAdminDashboardResponsive()
    {
        $this->createAdminUser();

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            
            // Test mobile view
            $browser->resize(375, 667)
                    ->assertSee('Dashboard')
                    ->click('.mobile-menu-toggle')
                    ->assertSee('Users')
                    ->assertSee('Shops');
            
            // Test tablet view
            $browser->resize(768, 1024)
                    ->assertSee('Dashboard');
            
            // Test desktop view
            $browser->resize(1920, 1080)
                    ->assertSee('Dashboard');
        });
    }
}