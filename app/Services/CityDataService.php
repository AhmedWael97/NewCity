<?php

namespace App\Services;

use App\Models\City;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class CityDataService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const STATS_CACHE_TTL = 1800; // 30 minutes

    /**
     * Check if cache needs refresh by comparing last updated timestamp
     */
    private function shouldRefreshCache(string $cacheKey, string $model, ?int $cityId = null): bool
    {
        $lastChecked = Cache::get($cacheKey . '_checked_at');
        
        if (!$lastChecked) {
            return true;
        }

        // Get the latest update time from the model
        $query = $model::query();
        
        if ($cityId && method_exists($model, 'getTable')) {
            $table = (new $model)->getTable();
            if (in_array('city_id', (new $model)->getFillable())) {
                $query->where('city_id', $cityId);
            }
        }
        
        $latestUpdate = $query->max('updated_at');
        
        if (!$latestUpdate) {
            return false;
        }
        
        return $latestUpdate > $lastChecked;
    }

    /**
     * Smart cache remember with auto-refresh on updates
     */
    private function smartCache(string $cacheKey, int $ttl, callable $callback, string $model = null, ?int $cityId = null)
    {
        // If model is provided, check if we need to refresh
        if ($model && $this->shouldRefreshCache($cacheKey, $model, $cityId)) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, $ttl, $callback);
        
        // Store the check timestamp
        Cache::put($cacheKey . '_checked_at', now(), $ttl);
        
        return $data;
    }

    /**
     * Get optimized city data for selection modal - ULTRA FAST VERSION
     */
    public function getCitiesForSelection(int $limit = 50): \Illuminate\Support\Collection
    {
        return $this->smartCache('cities_for_selection', 1800, function () use ($limit) {
            return City::select([
                'id',
                'name', 
                'slug',
                'governorate'
            ])
            ->where('is_active', true)
            ->withCount(['shops as shops_count' => function ($query) {
                $query->where('is_active', true)->where('is_verified', true);
            }])
            ->orderByDesc('shops_count')
            ->orderBy('name', 'ASC')
            ->limit($limit)
            ->get();
        }, City::class);
    }

    /**
     * Get city-specific shops with optimized queries
     */
    public function getCityShops(?int $cityId = null, array $filters = []): Builder
    {
        $query = Shop::with(['category:id,name,slug,icon', 'city:id,name,slug'])
            ->select([
                'id', 'user_id', 'city_id', 'category_id', 'name', 'slug', 
                'description', 'address', 'latitude', 'longitude', 'phone',
                'website', 'images', 'rating', 'review_count', 'is_featured',
                'is_verified', 'created_at'
            ])
            ->where('is_active', true)
            ->where('is_verified', true);

        // Apply city filter
        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('address', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Apply rating filter
        if (!empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        // Apply location filter (nearby)
        if (!empty($filters['latitude']) && !empty($filters['longitude'])) {
            $lat = $filters['latitude'];
            $lng = $filters['longitude'];
            $radius = $filters['radius'] ?? 10; // 10km default

            $query->whereRaw("
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?
            ", [$lat, $lng, $lat, $radius]);
        }

        // Default ordering
        $query->orderByDesc('is_featured')
              ->orderByDesc('rating')
              ->orderByDesc('review_count');

        return $query;
    }

    /**
     * Get city-specific statistics with caching
     */
    public function getCityStats(?int $cityId = null): array
    {
        $cacheKey = 'city_stats_' . ($cityId ?? 'all');
        
        return $this->smartCache($cacheKey, self::STATS_CACHE_TTL, function () use ($cityId) {
            $baseQuery = function ($model, $activeColumn = 'is_active') use ($cityId) {
                $query = $model::where($activeColumn, true);
                
                if ($cityId && in_array('city_id', $model::make()->getFillable())) {
                    $query->where('city_id', $cityId);
                }
                
                return $query;
            };

            $stats = [
                'total_shops' => $baseQuery(Shop::class)->where('is_verified', true)->count(),
                'total_products' => 0,
                'total_services' => 0,
                'total_categories' => Category::where('is_active', true)->count(),
                'average_rating' => 0,
                'total_cities' => $cityId ? 1 : City::where('is_active', true)->count(),
            ];

            // Get products and services count
            if ($cityId) {
                $stats['total_products'] = Product::whereHas('shop', function ($query) use ($cityId) {
                    $query->where('city_id', $cityId)->where('is_active', true);
                })->where('is_available', true)->count();

                $stats['total_services'] = Service::whereHas('shop', function ($query) use ($cityId) {
                    $query->where('city_id', $cityId)->where('is_active', true);
                })->where('is_available', true)->count();

                // Average rating for city shops
                $stats['average_rating'] = Shop::where('city_id', $cityId)
                    ->where('is_active', true)
                    ->where('is_verified', true)
                    ->where('review_count', '>', 0)
                    ->avg('rating') ?: 0;
            } else {
                $stats['total_products'] = Product::whereHas('shop', function ($query) {
                    $query->where('is_active', true);
                })->where('is_available', true)->count();

                $stats['total_services'] = Service::whereHas('shop', function ($query) {
                    $query->where('is_active', true);
                })->where('is_available', true)->count();

                $stats['average_rating'] = Shop::where('is_active', true)
                    ->where('is_verified', true)
                    ->where('review_count', '>', 0)
                    ->avg('rating') ?: 0;
            }

            // Round average rating
            $stats['average_rating'] = round($stats['average_rating'], 2);

            return $stats;
        }, Shop::class, $cityId);
    }

    /**
     * Get popular categories for a city
     */
    public function getPopularCategories(?int $cityId = null, int $limit = 12): \Illuminate\Support\Collection
    {
        $cacheKey = 'popular_categories_' . ($cityId ?? 'all') . '_' . $limit;
        
        return $this->smartCache($cacheKey, self::CACHE_TTL, function () use ($cityId, $limit) {
            $query = Category::select(['id', 'name', 'slug', 'icon', 'color'])
                ->where('is_active', true)
                ->withCount(['shops as shops_count' => function ($q) use ($cityId) {
                    $q->where('is_active', true)->where('is_verified', true);
                    if ($cityId) {
                        $q->where('city_id', $cityId);
                    }
                }])
                ->having('shops_count', '>', 0)
                ->orderByDesc('shops_count')
                ->limit($limit);

            return $query->get();
        }, Category::class, $cityId);
    }

    /**
     * Get featured shops for a city
     */
    public function getFeaturedShops(?int $cityId = null, int $limit = 8): \Illuminate\Support\Collection
    {
        $cacheKey = 'featured_shops_' . ($cityId ?? 'all') . '_' . $limit;
        
        return $this->smartCache($cacheKey, self::CACHE_TTL, function () use ($cityId, $limit) {
            $query = Shop::with(['category:id,name,slug', 'city:id,name,slug'])
                ->select([
                    'id', 'city_id', 'category_id', 'name', 'slug', 'description',
                    'address', 'images', 'rating', 'review_count', 'is_featured'
                ])
                ->where('is_active', true)
                ->where('is_verified', true)
                ->where('is_featured', true);

            if ($cityId) {
                $query->where('city_id', $cityId);
            }

            return $query->orderByDesc('rating')
                         ->orderByDesc('review_count')
                         ->limit($limit)
                         ->get();
        }, Shop::class, $cityId);
    }

    /**
     * Search shops with city context
     */
    public function searchShops(string $query, ?int $cityId = null, array $options = []): Builder
    {
        $searchQuery = $this->getCityShops($cityId);

        // Full-text search
        $searchQuery->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%")
              ->orWhere('address', 'LIKE', "%{$query}%");
        });

        // Search in products and services if requested
        if (!empty($options['include_products'])) {
            $searchQuery->orWhereHas('products', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->where('is_available', true);
            });
        }

        if (!empty($options['include_services'])) {
            $searchQuery->orWhereHas('services', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->where('is_available', true);
            });
        }

        return $searchQuery;
    }

    /**
     * Clear city-specific caches
     */
    public function clearCityCache(?int $cityId = null): void
    {
        $patterns = [
            'cities_selection_data',
            'city_stats_' . ($cityId ?? 'all'),
            'popular_categories_' . ($cityId ?? 'all') . '_*',
            'featured_shops_' . ($cityId ?? 'all') . '_*',
            'landing_cities_' . ($cityId ?? 'all'),
            'landing_stats_' . ($cityId ?? 'all'),
            'sample_shop_' . ($cityId ?? 'all'),
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // For patterns with wildcards, we'd need to implement a more sophisticated cache clearing
                // For now, we'll clear specific known keys
                $sizes = [8, 12, 16, 20];
                foreach ($sizes as $size) {
                    Cache::forget(str_replace('*', $size, $pattern));
                }
            } else {
                Cache::forget($pattern);
            }
        }
    }

    /**
     * Get nearby cities for cross-promotion
     */
    public function getNearbyCity(int $cityId, int $limit = 5): \Illuminate\Support\Collection
    {
        $city = City::find($cityId);
        
        if (!$city || !$city->latitude || !$city->longitude) {
            return collect();
        }

        $cacheKey = "nearby_cities_{$cityId}_{$limit}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($city, $limit, $cityId) {
            return City::select(['id', 'name', 'slug', 'latitude', 'longitude'])
                ->where('id', '!=', $cityId)
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw("
                    (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
                ", [$city->latitude, $city->longitude, $city->latitude])
                ->orderBy('distance')
                ->limit($limit)
                ->get();
        });
    }
}