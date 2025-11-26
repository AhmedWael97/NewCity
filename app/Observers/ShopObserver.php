<?php

namespace App\Observers;

use App\Models\Shop;
use Illuminate\Support\Facades\Cache;

class ShopObserver
{
    /**
     * Handle the Shop "created" event.
     */
    public function created(Shop $shop): void
    {
        $this->clearShopCaches($shop);
    }

    /**
     * Handle the Shop "updated" event.
     */
    public function updated(Shop $shop): void
    {
        $this->clearShopCaches($shop);
    }

    /**
     * Handle the Shop "deleted" event.
     */
    public function deleted(Shop $shop): void
    {
        $this->clearShopCaches($shop);
    }

    /**
     * Clear all related shop caches
     */
    private function clearShopCaches(Shop $shop): void
    {
        // Clear city-specific caches
        if ($shop->city_id) {
            $city = \App\Models\City::find($shop->city_id);
            if ($city) {
                Cache::forget("city_all_shops_{$city->slug}");
                Cache::forget("city_all_shops_{$city->slug}_last_check");
                Cache::forget("city_categories_shops_{$city->slug}");
                Cache::forget("city_categories_shops_{$city->slug}_last_check");
                Cache::forget("city_stats_{$shop->city_id}");
                Cache::forget("city_stats_{$shop->city_id}_checked_at");
            }
        }
        
        // Clear global caches
        Cache::forget('city_stats_all');
        Cache::forget('city_stats_all_checked_at');
        Cache::forget('cities_for_selection');
        Cache::forget('cities_for_selection_checked_at');
        
        // Clear featured shops cache
        Cache::forget("featured_shops_all_8");
        Cache::forget("featured_shops_{$shop->city_id}_8");
        Cache::forget("featured_shops_all_8_checked_at");
        Cache::forget("featured_shops_{$shop->city_id}_8_checked_at");
        
        // Clear landing page caches
        Cache::forget("landing_stats_{$shop->city_id}");
        Cache::forget("landing_stats_all");
        
        // Clear category-related caches
        if ($shop->category_id) {
            Cache::forget("popular_categories_{$shop->city_id}_8");
            Cache::forget("popular_categories_{$shop->city_id}_12");
            Cache::forget("popular_categories_all_8");
            Cache::forget("popular_categories_all_12");
        }
    }
}
