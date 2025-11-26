<?php

namespace App\Observers;

use App\Models\UserService;
use Illuminate\Support\Facades\Cache;

class UserServiceObserver
{
    /**
     * Handle the UserService "created" event.
     */
    public function created(UserService $service): void
    {
        $this->clearServiceCaches($service);
    }

    /**
     * Handle the UserService "updated" event.
     */
    public function updated(UserService $service): void
    {
        $this->clearServiceCaches($service);
    }

    /**
     * Handle the UserService "deleted" event.
     */
    public function deleted(UserService $service): void
    {
        $this->clearServiceCaches($service);
    }

    /**
     * Clear all related service caches
     */
    private function clearServiceCaches(UserService $service): void
    {
        // Clear city-specific service caches
        if ($service->city_id) {
            $city = \App\Models\City::find($service->city_id);
            if ($city) {
                Cache::forget("city_service_categories_{$city->slug}");
                Cache::forget("city_service_categories_{$city->slug}_last_check");
            }
        }
        
        // Clear city stats (includes service counts)
        Cache::forget("city_stats_{$service->city_id}");
        Cache::forget("city_stats_{$service->city_id}_checked_at");
        Cache::forget('city_stats_all');
        Cache::forget('city_stats_all_checked_at');
    }
}
