<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\City;
use App\Models\ShopAnalytics;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display main analytics dashboard
     */
    public function index()
    {
        // Top performing shops by views (last 30 days)
        $topShopsByViews = ShopAnalytics::select('shop_id', DB::raw('COUNT(*) as total_views'))
            ->where('event_type', 'shop_view')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('shop_id')
            ->orderBy('total_views', 'desc')
            ->with(['shop' => function($query) {
                $query->select('id', 'name', 'city_id', 'category_id')
                      ->with(['city:id,name', 'category:id,name']);
            }])
            ->limit(20)
            ->get();

        // Top cities by visitor count (last 30 days)
        $topCitiesByVisitors = ShopAnalytics::select('shops.city_id', DB::raw('COUNT(DISTINCT shop_analytics.user_ip) as unique_visitors'))
            ->join('shops', 'shop_analytics.shop_id', '=', 'shops.id')
            ->where('shop_analytics.created_at', '>=', Carbon::now()->subDays(30))
            ->where('shop_analytics.event_type', 'shop_view')
            ->whereNotNull('shops.city_id')
            ->groupBy('shops.city_id')
            ->orderBy('unique_visitors', 'desc')
            ->limit(15)
            ->get()
            ->map(function($item) {
                $cityData = new \stdClass();
                $cityData->unique_visitors = $item->unique_visitors;
                $cityData->city = \App\Models\City::select('id', 'name')->find($item->city_id);
                return $cityData;
            });

        // Most searched terms (last 30 days)
        $topSearchTerms = ShopAnalytics::select('metadata->search_term as search_term', DB::raw('COUNT(*) as search_count'))
            ->where('event_type', 'search')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('metadata->search_term')
            ->groupBy('metadata->search_term')
            ->orderBy('search_count', 'desc')
            ->limit(20)
            ->get();

        // Popular categories by shop views
        $popularCategories = ShopAnalytics::select('shops.category_id', 'categories.name', DB::raw('COUNT(*) as total_views'))
            ->join('shops', 'shop_analytics.shop_id', '=', 'shops.id')
            ->join('categories', 'shops.category_id', '=', 'categories.id')
            ->where('shop_analytics.event_type', 'shop_view')
            ->where('shop_analytics.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('shops.category_id', 'categories.name')
            ->orderBy('total_views', 'desc')
            ->limit(10)
            ->get();

        // Daily analytics for the last 30 days
        $dailyAnalytics = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $dailyAnalytics[] = [
                'date' => $dateStr,
                'views' => ShopAnalytics::whereDate('created_at', $date)
                    ->where('event_type', 'shop_view')
                    ->count(),
                'searches' => ShopAnalytics::whereDate('created_at', $date)
                    ->where('event_type', 'search')
                    ->count(),
                'unique_visitors' => ShopAnalytics::whereDate('created_at', $date)
                    ->distinct('user_ip')
                    ->count(),
                'new_shops' => Shop::whereDate('created_at', $date)->count(),
                'new_users' => User::whereDate('created_at', $date)->count()
            ];
        }

        // Traffic sources analysis
        $trafficSources = ShopAnalytics::select('metadata->source as source', DB::raw('COUNT(*) as visits'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('metadata->source')
            ->groupBy('metadata->source')
            ->orderBy('visits', 'desc')
            ->get();

        // Device type distribution
        $deviceTypes = ShopAnalytics::select('metadata->device_type as device_type', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('metadata->device_type')
            ->groupBy('metadata->device_type')
            ->get();

        return view('admin.analytics.index', compact(
            'topShopsByViews',
            'topCitiesByVisitors',
            'topSearchTerms',
            'popularCategories',
            'dailyAnalytics',
            'trafficSources',
            'deviceTypes'
        ));
    }

    /**
     * Show detailed shop performance analytics
     */
    public function shopPerformance()
    {
        $shops = Shop::select(
                'shops.id',
                'shops.name',
                'shops.slug',
                'shops.user_id',
                'shops.city_id',
                'shops.category_id',
                'shops.rating',
                'shops.review_count',
                'shops.is_verified',
                'shops.is_featured',
                'shops.is_active',
                'shops.created_at',
                DB::raw('COUNT(shop_analytics.id) as total_views')
            )
            ->leftJoin('shop_analytics', 'shops.id', '=', 'shop_analytics.shop_id')
            ->with(['city:id,name', 'category:id,name', 'user:id,name'])
            ->groupBy(
                'shops.id',
                'shops.name', 
                'shops.slug',
                'shops.user_id',
                'shops.city_id',
                'shops.category_id',
                'shops.rating',
                'shops.review_count',
                'shops.is_verified',
                'shops.is_featured',
                'shops.is_active',
                'shops.created_at'
            )
            ->orderByRaw('COUNT(shop_analytics.id) DESC')
            ->paginate(20);

        foreach ($shops as $shop) {
            // Get analytics for each shop
            $shop->analytics = [
                'total_views' => ShopAnalytics::where('shop_id', $shop->id)
                    ->where('event_type', 'shop_view')
                    ->count(),
                'views_this_month' => ShopAnalytics::where('shop_id', $shop->id)
                    ->where('event_type', 'shop_view')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'unique_visitors' => ShopAnalytics::where('shop_id', $shop->id)
                    ->where('event_type', 'shop_view')
                    ->distinct('user_ip')
                    ->count('user_ip'),
                'contact_clicks' => ShopAnalytics::where('shop_id', $shop->id)
                    ->where('event_type', 'contact_click')
                    ->count(),
                'conversion_rate' => 0 // Calculate based on your business logic
            ];
        }

        return view('admin.analytics.shop-performance', compact('shops'));
    }

    /**
     * Show city-wise analytics
     */
    public function cityAnalytics()
    {
        $cities = City::withCount(['shops', 'users'])
            ->get()
            ->map(function($city) {
                // Get analytics for shops in this city
                $analytics = ShopAnalytics::join('shops', 'shop_analytics.shop_id', '=', 'shops.id')
                    ->where('shops.city_id', $city->id)
                    ->where('shop_analytics.created_at', '>=', Carbon::now()->subDays(30))
                    ->select('shop_analytics.*')
                    ->get();

                $city->analytics = [
                    'total_views' => $analytics->where('event_type', 'shop_view')->count(),
                    'unique_visitors' => $analytics->where('event_type', 'shop_view')->unique('user_ip')->count(),
                    'searches' => $analytics->where('event_type', 'search')->count(),
                    'contact_clicks' => $analytics->where('event_type', 'contact_click')->count(),
                    'bounce_rate' => $this->calculateBounceRate($city->id)
                ];

                return $city;
            })
            ->sortByDesc('analytics.total_views');

        // City comparison data for charts
        $cityComparison = $cities->take(10)->map(function($city) {
            return [
                'name' => $city->name,
                'views' => $city->analytics['total_views'],
                'visitors' => $city->analytics['unique_visitors'],
                'shops' => $city->shops_count,
                'users' => $city->users_count
            ];
        });

        return view('admin.analytics.cities', compact('cities', 'cityComparison'));
    }

    /**
     * Show user behavior analytics
     */
    public function userBehavior()
    {
        // User engagement metrics
        $userMetrics = [
            'total_users' => User::count(),
            'active_users_today' => User::whereDate('last_login_at', today())->count(),
            'active_users_week' => User::where('last_login_at', '>=', Carbon::now()->subWeek())->count(),
            'active_users_month' => User::where('last_login_at', '>=', Carbon::now()->subMonth())->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_week' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'new_users_month' => User::where('created_at', '>=', Carbon::now()->subMonth())->count()
        ];

        // User activity patterns (hourly)
        $hourlyActivity = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourlyActivity[] = [
                'hour' => $hour,
                'activity' => ShopAnalytics::whereTime('created_at', '>=', sprintf('%02d:00:00', $hour))
                    ->whereTime('created_at', '<', sprintf('%02d:00:00', ($hour + 1) % 24))
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->count()
            ];
        }

        // Most active users
        $activeUsers = User::leftJoin('shop_analytics', 'users.id', '=', 'shop_analytics.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.is_active',
                'users.created_at',
                DB::raw('COUNT(shop_analytics.id) as activity_count')
            )
            ->where('shop_analytics.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.is_active',
                'users.created_at'
            )
            ->orderBy('activity_count', 'desc')
            ->limit(20)
            ->get();

        // User retention analysis
        $retentionData = $this->calculateUserRetention();

        return view('admin.analytics.user-behavior', compact(
            'userMetrics',
            'hourlyActivity',
            'activeUsers',
            'retentionData'
        ));
    }

    /**
     * Generate analytics report
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:overview,shops,cities,users'
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        switch ($validated['type']) {
            case 'overview':
                $data = $this->generateOverviewReport($startDate, $endDate);
                break;
            case 'shops':
                $data = $this->generateShopsReport($startDate, $endDate);
                break;
            case 'cities':
                $data = $this->generateCitiesReport($startDate, $endDate);
                break;
            case 'users':
                $data = $this->generateUsersReport($startDate, $endDate);
                break;
        }

        return response()->json($data);
    }

    /**
     * Calculate bounce rate for a city
     */
    private function calculateBounceRate($cityId)
    {
        // Since we don't have session_duration, we'll calculate bounce rate differently
        // We'll consider a bounced session as one with only one shop_view event
        $totalSessions = ShopAnalytics::join('shops', 'shop_analytics.shop_id', '=', 'shops.id')
            ->where('shops.city_id', $cityId)
            ->where('shop_analytics.event_type', 'shop_view')
            ->where('shop_analytics.created_at', '>=', Carbon::now()->subDays(30))
            ->distinct('shop_analytics.user_ip')
            ->count();

        // Count users who viewed only one shop (simplified bounce rate)
        $singleViewSessions = ShopAnalytics::join('shops', 'shop_analytics.shop_id', '=', 'shops.id')
            ->where('shops.city_id', $cityId)
            ->where('shop_analytics.event_type', 'shop_view')
            ->where('shop_analytics.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('shop_analytics.user_ip')
            ->havingRaw('COUNT(*) = 1')
            ->count();

        return $totalSessions > 0 ? round(($singleViewSessions / $totalSessions) * 100, 2) : 0;
    }

    /**
     * Calculate user retention rates
     */
    private function calculateUserRetention()
    {
        $retentionData = [];
        
        for ($week = 1; $week <= 8; $week++) {
            $startDate = Carbon::now()->subWeeks($week);
            $endDate = Carbon::now()->subWeeks($week - 1);
            
            $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->pluck('id');
            $returnedUsers = User::whereIn('id', $newUsers)
                ->where('last_login_at', '>', $endDate)
                ->count();
            
            $retentionRate = $newUsers->count() > 0 ? 
                round(($returnedUsers / $newUsers->count()) * 100, 2) : 0;
            
            $retentionData[] = [
                'week' => $week,
                'retention_rate' => $retentionRate,
                'new_users' => $newUsers->count(),
                'returned_users' => $returnedUsers
            ];
        }
        
        return $retentionData;
    }

    /**
     * Generate overview report
     */
    private function generateOverviewReport($startDate, $endDate)
    {
        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'metrics' => [
                'total_views' => ShopAnalytics::whereBetween('created_at', [$startDate, $endDate])
                    ->where('event_type', 'shop_view')
                    ->count(),
                'unique_visitors' => ShopAnalytics::whereBetween('created_at', [$startDate, $endDate])
                    ->distinct('user_ip')
                    ->count(),
                'new_shops' => Shop::whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'searches' => ShopAnalytics::whereBetween('created_at', [$startDate, $endDate])
                    ->where('event_type', 'search')
                    ->count()
            ]
        ];
    }

    /**
     * Generate shops report
     */
    private function generateShopsReport($startDate, $endDate)
    {
        return Shop::with(['city:id,name', 'category:id,name'])
            ->leftJoin('shop_analytics', function($join) use ($startDate, $endDate) {
                $join->on('shops.id', '=', 'shop_analytics.shop_id')
                     ->whereBetween('shop_analytics.created_at', [$startDate, $endDate]);
            })
            ->select(
                'shops.id',
                'shops.name',
                'shops.slug',
                'shops.user_id',
                'shops.city_id',
                'shops.category_id',
                'shops.rating',
                'shops.review_count',
                'shops.is_verified',
                'shops.is_featured',
                'shops.is_active',
                'shops.created_at',
                DB::raw('COUNT(shop_analytics.id) as views')
            )
            ->groupBy(
                'shops.id',
                'shops.name',
                'shops.slug',
                'shops.user_id',
                'shops.city_id',
                'shops.category_id',
                'shops.rating',
                'shops.review_count',
                'shops.is_verified',
                'shops.is_featured',
                'shops.is_active',
                'shops.created_at'
            )
            ->orderBy('views', 'desc')
            ->get();
    }

    /**
     * Generate cities report
     */
    private function generateCitiesReport($startDate, $endDate)
    {
        return City::leftJoin('shops', 'cities.id', '=', 'shops.city_id')
            ->leftJoin('shop_analytics', function($join) use ($startDate, $endDate) {
                $join->on('shops.id', '=', 'shop_analytics.shop_id')
                     ->whereBetween('shop_analytics.created_at', [$startDate, $endDate]);
            })
            ->select(
                'cities.id',
                'cities.name',
                'cities.slug',
                'cities.governorate',
                'cities.is_active',
                'cities.created_at',
                DB::raw('COUNT(shop_analytics.id) as total_activity')
            )
            ->groupBy(
                'cities.id',
                'cities.name',
                'cities.slug',
                'cities.governorate',
                'cities.is_active',
                'cities.created_at'
            )
            ->orderBy('total_activity', 'desc')
            ->get();
    }

    /**
     * Generate users report
     */
    private function generateUsersReport($startDate, $endDate)
    {
        return User::leftJoin('shop_analytics', function($join) use ($startDate, $endDate) {
            $join->on('users.id', '=', 'shop_analytics.user_id')
                 ->whereBetween('shop_analytics.created_at', [$startDate, $endDate]);
        })
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.role',
            'users.is_active',
            'users.created_at',
            DB::raw('COUNT(shop_analytics.id) as activity_count')
        )
        ->groupBy(
            'users.id',
            'users.name',
            'users.email',
            'users.role',
            'users.is_active',
            'users.created_at'
        )
        ->orderBy('activity_count', 'desc')
        ->get();
    }
}