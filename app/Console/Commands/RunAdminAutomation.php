<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunAdminAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:automate 
                            {--test=all : Specific test to run (all, login, users, shops, cities, categories, dashboard, complete)}
                            {--headless=true : Run browser in headless mode}
                            {--slow=0 : Slow down automation (milliseconds between actions)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run automated admin panel testing with browser automation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Admin Panel Automation...');
        $this->newLine();

        $test = $this->option('test');
        $headless = $this->option('headless');
        $slow = $this->option('slow');

        // Set Dusk environment variables
        if ($headless === 'false') {
            putenv('DUSK_HEADLESS_DISABLED=true');
        }

        if ($slow > 0) {
            putenv("DUSK_SLOW_MODE={$slow}");
        }

        // Display current configuration
        $this->displayConfiguration($test, $headless, $slow);

        try {
            switch ($test) {
                case 'login':
                    $this->runLoginTests();
                    break;
                case 'users':
                    $this->runUserTests();
                    break;
                case 'shops':
                    $this->runShopTests();
                    break;
                case 'cities':
                    $this->runCityTests();
                    break;
                case 'categories':
                    $this->runCategoryTests();
                    break;
                case 'dashboard':
                    $this->runDashboardTests();
                    break;
                case 'complete':
                    $this->runCompleteAutomation();
                    break;
                case 'all':
                default:
                    $this->runAllTests();
                    break;
            }

            $this->newLine();
            $this->info('✅ Admin Panel Automation Completed Successfully!');
            
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Automation failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Display current configuration
     */
    private function displayConfiguration($test, $headless, $slow)
    {
        $this->info('Configuration:');
        $this->line("  Test Suite: {$test}");
        $this->line("  Headless Mode: " . ($headless === 'true' ? 'Yes' : 'No'));
        $this->line("  Slow Mode: {$slow}ms");
        $this->newLine();
    }

    /**
     * Run login tests
     */
    private function runLoginTests()
    {
        $this->info('🔑 Running Admin Login Tests...');
        Artisan::call('dusk', ['--filter' => 'AdminLoginTest']);
        $this->line(Artisan::output());
    }

    /**
     * Run user management tests
     */
    private function runUserTests()
    {
        $this->info('👥 Running User Management Tests...');
        Artisan::call('dusk', ['--filter' => 'AdminUserManagementTest']);
        $this->line(Artisan::output());
    }

    /**
     * Run shop management tests
     */
    private function runShopTests()
    {
        $this->info('🏪 Running Shop Management Tests...');
        Artisan::call('dusk', ['--filter' => 'AdminShopManagementTest']);
        $this->line(Artisan::output());
    }

    /**
     * Run city management tests
     */
    private function runCityTests()
    {
        $this->info('🏙️ Running City Management Tests...');
        Artisan::call('dusk', ['--filter' => 'AdminCityAndCategoryManagementTest']);
        $this->line(Artisan::output());
    }

    /**
     * Run category management tests
     */
    private function runCategoryTests()
    {
        $this->info('📂 Running Category Management Tests...');
        Artisan::call('dusk', ['--filter' => 'AdminCityAndCategoryManagementTest']);
        $this->line(Artisan::output());
    }

    /**
     * Run dashboard tests
     */
    private function runDashboardTests()
    {
        $this->info('📊 Running Dashboard Tests...');
        Artisan::call('dusk', ['--filter' => 'AdminDashboardTest']);
        $this->line(Artisan::output());
    }

    /**
     * Run complete automation
     */
    private function runCompleteAutomation()
    {
        $this->info('🎯 Running Complete Admin Automation...');
        Artisan::call('dusk', ['--filter' => 'CompleteAdminAutomationTest']);
        $this->line(Artisan::output());
    }

    /**
     * Run all tests
     */
    private function runAllTests()
    {
        $this->info('🎯 Running All Admin Tests...');
        
        $tests = [
            'Login Tests' => 'AdminLoginTest',
            'User Management Tests' => 'AdminUserManagementTest',
            'Shop Management Tests' => 'AdminShopManagementTest',
            'City & Category Tests' => 'AdminCityAndCategoryManagementTest',
            'Dashboard Tests' => 'AdminDashboardTest',
            'Complete Automation' => 'CompleteAdminAutomationTest',
        ];

        foreach ($tests as $description => $testClass) {
            $this->info("Running {$description}...");
            Artisan::call('dusk', ['--filter' => $testClass]);
            $this->line(Artisan::output());
            $this->newLine();
        }
    }
}