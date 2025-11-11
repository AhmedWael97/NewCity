<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class CacheLandingPage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:landing-page';

    /**
     * The console command description.
     */
    protected $description = 'Cache landing page data for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Caching landing page data...');

        // Clear existing cache
        Cache::forget('landing_cities');
        Cache::forget('landing_stats');

        // Pre-warm the cache by calling the landing page
        $response = app(\App\Http\Controllers\LandingController::class)->index();
        
        $this->info('Landing page cache warmed successfully!');
        
        // Optionally cache the view as well
        Artisan::call('view:cache');
        $this->info('Views cached successfully!');

        return 0;
    }
}