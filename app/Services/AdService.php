<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\City;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AdService
{
    /**
     * Get advertisements for a specific placement and city
     */
    public function getAdsForPlacement(string $placement, ?int $cityId = null, int $limit = 3): Collection
    {
        $cacheKey = "ads_{$placement}_{$cityId}_{$limit}";
        
        return Cache::remember($cacheKey, 300, function () use ($placement, $cityId, $limit) {
            return Advertisement::getActiveAdsForPlacement($placement, $cityId)
                ->take($limit);
        });
    }

    /**
     * Get hero ads for city landing page
     */
    public function getHeroAds(?int $cityId = null): Collection
    {
        return $this->getAdsForPlacement('city_landing', $cityId, 1);
    }

    /**
     * Get banner ads
     */
    public function getBannerAds(string $page = 'homepage', ?int $cityId = null): Collection
    {
        return $this->getAdsForPlacement($page, $cityId, 2);
    }

    /**
     * Get sidebar ads
     */
    public function getSidebarAds(string $page = 'homepage', ?int $cityId = null): Collection
    {
        return $this->getAdsForPlacement($page, $cityId, 3);
    }

    /**
     * Get sponsored listings
     */
    public function getSponsoredListings(?int $cityId = null, ?array $categoryIds = null): Collection
    {
        $cacheKey = "sponsored_listings_{$cityId}_" . md5(json_encode($categoryIds));
        
        return Cache::remember($cacheKey, 300, function () use ($cityId, $categoryIds) {
            $query = Advertisement::active()
                ->where('type', 'sponsored_listing')
                ->forCity($cityId)
                ->withinBudget();

            if ($categoryIds) {
                $query->where(function ($q) use ($categoryIds) {
                    $q->whereNull('target_categories')
                      ->orWhere(function ($sq) use ($categoryIds) {
                          foreach ($categoryIds as $categoryId) {
                              $sq->orWhereJsonContains('target_categories', $categoryId);
                          }
                      });
                });
            }

            return $query->orderBy('price_amount', 'desc')
                        ->limit(5)
                        ->get()
                        ->filter(function ($ad) {
                            return $ad->hasValidSchedule() && $ad->withinDailyBudget();
                        });
        });
    }

    /**
     * Record ad impression
     */
    public function recordImpression(int $adId): void
    {
        $ad = Advertisement::find($adId);
        if ($ad && $ad->isActive()) {
            $ad->recordImpression();
            
            // Add cost for CPM ads
            if ($ad->pricing_model === 'cpm') {
                $cost = $ad->price_amount / 1000; // Cost per 1000 impressions
                $ad->increment('spent_amount', $cost);
            }
            
            // Clear cache
            $this->clearAdCaches($ad);
        }
    }

    /**
     * Record ad click
     */
    public function recordClick(int $adId): string
    {
        $ad = Advertisement::find($adId);
        if ($ad && $ad->isActive()) {
            $ad->recordClick();
            $this->clearAdCaches($ad);
            return $ad->click_url;
        }
        
        return '/';
    }

    /**
     * Record ad conversion (when someone completes desired action)
     */
    public function recordConversion(int $adId): void
    {
        $ad = Advertisement::find($adId);
        if ($ad && $ad->isActive()) {
            $ad->recordConversion();
            $this->clearAdCaches($ad);
        }
    }

    /**
     * Get ad statistics for admin dashboard
     */
    public function getAdStatistics(?int $cityId = null): array
    {
        $query = Advertisement::query();
        
        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        $stats = [
            'total_ads' => $query->count(),
            'active_ads' => $query->where('status', 'active')->count(),
            'pending_ads' => $query->where('status', 'pending_review')->count(),
            'total_impressions' => $query->sum('impressions'),
            'total_clicks' => $query->sum('clicks'),
            'total_revenue' => $query->sum('spent_amount'),
            'avg_ctr' => $query->avg('ctr') ?? 0,
        ];

        // Calculate overall CTR
        if ($stats['total_impressions'] > 0) {
            $stats['overall_ctr'] = ($stats['total_clicks'] / $stats['total_impressions']) * 100;
        } else {
            $stats['overall_ctr'] = 0;
        }

        return $stats;
    }

    /**
     * Get pricing information
     */
    public function getPricingInfo(): array
    {
        return Advertisement::getPricingTiers();
    }

    /**
     * Validate ad budget and scheduling
     */
    public function validateAdForDisplay(Advertisement $ad): bool
    {
        return $ad->isActive() && 
               $ad->hasValidSchedule() && 
               $ad->withinBudget() && 
               $ad->withinDailyBudget();
    }

    /**
     * Get revenue report for date range
     */
    public function getRevenueReport(string $startDate, string $endDate, ?int $cityId = null): array
    {
        $query = Advertisement::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        $ads = $query->get();

        return [
            'total_revenue' => $ads->sum('spent_amount'),
            'total_ads' => $ads->count(),
            'active_ads' => $ads->where('status', 'active')->count(),
            'completed_ads' => $ads->where('status', 'completed')->count(),
            'avg_revenue_per_ad' => $ads->count() > 0 ? $ads->sum('spent_amount') / $ads->count() : 0,
            'revenue_by_type' => $ads->groupBy('type')->map(function ($group) {
                return $group->sum('spent_amount');
            }),
            'revenue_by_pricing_model' => $ads->groupBy('pricing_model')->map(function ($group) {
                return $group->sum('spent_amount');
            })
        ];
    }

    /**
     * Clear ad caches
     */
    protected function clearAdCaches(Advertisement $ad): void
    {
        // Clear placement caches
        $placements = ['homepage', 'city_landing', 'shop_page', 'category_page', 'search_results'];
        
        foreach ($placements as $placement) {
            Cache::forget("ads_{$placement}_{$ad->city_id}_1");
            Cache::forget("ads_{$placement}_{$ad->city_id}_2");
            Cache::forget("ads_{$placement}_{$ad->city_id}_3");
            Cache::forget("ads_{$placement}_null_1");
            Cache::forget("ads_{$placement}_null_2");
            Cache::forget("ads_{$placement}_null_3");
        }
        
        // Clear sponsored listings cache
        Cache::forget("sponsored_listings_{$ad->city_id}_" . md5('[]'));
    }
}