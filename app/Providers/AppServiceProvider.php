<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Category;
use App\Models\City;
use App\Models\Shop;
use App\Models\SupportTicket;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the CityDataService
        $this->app->singleton(\App\Services\CityDataService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set custom pagination view
        Paginator::defaultView('custom.pagination');
        Paginator::defaultSimpleView('custom.pagination');

        // Share navigation categories with all views
        View::composer('partials.navbar', function ($view) {
            $navCategories = Category::active()
                ->roots()
                ->withChildren()
                ->ordered()
                ->limit(8) // Top 8 categories for navigation
                ->get();
            
            $navCities = City::active()
                ->orderBy('name')
                ->limit(10) // Top 10 cities for quick access
                ->get();
                
            $view->with([
                'navCategories' => $navCategories,
                'navCities' => $navCities
            ]);
        });
        
        // Share cities with city modal
        View::composer('partials.city-modal', function ($view) {
            $cities = City::active()
                ->withCount('shops')
                ->orderBy('shops_count', 'desc')
                ->orderBy('name')
                ->get();
                
            $view->with([
                'cities' => $cities
            ]);
        });
        
        // Share cities with floating city selector
        View::composer('partials.floating-city-selector', function ($view) {
            $cities = City::active()
                ->withCount('shops')
                ->orderBy('name')
                ->get();
                
            $view->with([
                'cities' => $cities
            ]);
        });
        
        // Share admin stats with admin layout
        View::composer('layouts.admin', function ($view) {
            $stats = [
                'pending_shops' => Shop::where('status', 'pending')->count(),
                'pending_tickets' => SupportTicket::where('status', 'open')->count(),
            ];
                
            $view->with([
                'stats' => $stats
            ]);
        });
    }
}
