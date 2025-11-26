<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\City;

class RefreshCityCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:refresh-city {city? : City ID or slug} {--all : Refresh all cities}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh cached data for a specific city or all cities';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->info('Refreshing cache for all cities...');
            $this->refreshAllCities();
        } elseif ($cityIdentifier = $this->argument('city')) {
            $this->refreshCityCache($cityIdentifier);
        } else {
            $this->info('Refreshing global caches...');
            $this->refreshGlobalCache();
        }

        $this->info('✓ Cache refresh completed!');
    }

    /**
     * Refresh cache for all cities
     */
    private function refreshAllCities(): void
    {
        $cities = City::where('is_active', true)->get();
        
        $bar = $this->output->createProgressBar($cities->count());
        $bar->start();

        foreach ($cities as $city) {
            $this->clearCityCache($city);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        $this->refreshGlobalCache();
        $this->info("Refreshed cache for {$cities->count()} cities");
    }

    /**
     * Refresh cache for a specific city
     */
    private function refreshCityCache(string $identifier): void
    {
        $city = is_numeric($identifier) 
            ? City::find($identifier) 
            : City::where('slug', $identifier)->first();

        if (!$city) {
            $this->error("City not found: {$identifier}");
            return;
        }

        $this->clearCityCache($city);
        $this->info("Refreshed cache for: {$city->name}");
    }

    /**
     * Clear all cache keys for a specific city
     */
    private function clearCityCache(City $city): void
    {
        $keys = [
            "city_all_shops_{$city->slug}",
            "city_all_shops_{$city->slug}_last_check",
            "city_categories_shops_{$city->slug}",
            "city_categories_shops_{$city->slug}_last_check",
            "city_service_categories_{$city->slug}",
            "city_service_categories_{$city->slug}_last_check",
            "city_stats_{$city->id}",
            "city_stats_{$city->id}_checked_at",
            "landing_stats_{$city->id}",
            "sample_shop_{$city->slug}",
            "featured_shops_{$city->id}_8",
            "featured_shops_{$city->id}_8_checked_at",
            "popular_categories_{$city->id}_8",
            "popular_categories_{$city->id}_12",
            "nearby_cities_{$city->id}_5",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Refresh global caches
     */
    private function refreshGlobalCache(): void
    {
        $globalKeys = [
            'cities_for_selection',
            'cities_for_selection_checked_at',
            'city_stats_all',
            'city_stats_all_checked_at',
            'featured_shops_all_8',
            'featured_shops_all_8_checked_at',
            'popular_categories_all_8',
            'popular_categories_all_12',
            'landing_cities_basic',
            'landing_stats_all',
            'sample_shop_all',
        ];

        foreach ($globalKeys as $key) {
            Cache::forget($key);
        }

        $this->info('Global caches cleared');
    }
}
