<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ContactInfoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share cached contact info with all views
        View::composer('*', function ($view) {
            $contactInfo = Cache::remember('contact_info', 60 * 60 * 24, function () {
                return Config::get('contact');
            });
            
            $view->with('contactInfo', $contactInfo);
        });
    }
}
