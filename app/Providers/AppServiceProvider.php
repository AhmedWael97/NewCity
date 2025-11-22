<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
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
        // Register event listeners
        Event::listen(
            \App\Events\UserActivityTracked::class,
            \App\Listeners\StoreUserActivity::class
        );

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
        
        // Share default SEO data with all views using layouts.app
        View::composer('layouts.app', function ($view) {
            if (!$view->offsetExists('seoData')) {
                $defaultSeoData = [
                    'title' => 'اكتشف المدن - منصة استكشاف المتاجر المحلية في مصر',
                    'description' => 'اكتشف أفضل المتاجر والخدمات المحلية في مدينتك',
                    'keywords' => 'متاجر, مصر, تسوق, دليل المتاجر',
                    'canonical' => url()->current(),
                    'og_image' => asset('images/og-default.jpg'),
                ];
                
                $view->with('seoData', $defaultSeoData);
            }
        });
    }
}
