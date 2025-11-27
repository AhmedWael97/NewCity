<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\City;
use App\Models\ShopAnalytics;
use App\Models\UserEvent;
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

        // Conversion metrics (last 30 days)
        $conversions = [
            'phone_calls' => UserEvent::where('event_action', 'phone_call')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count(),
            'directions' => UserEvent::where('event_action', 'map_directions')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count(),
            'shares' => UserEvent::where('event_type', 'share')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count(),
            'favorites' => UserEvent::where('event_type', 'favorite')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count(),
        ];
        $conversions['total'] = array_sum($conversions);

        return view('admin.analytics.index', compact(
            'topShopsByViews',
            'topCitiesByVisitors',
            'topSearchTerms',
            'popularCategories',
            'dailyAnalytics',
            'trafficSources',
            'deviceTypes',
            'conversions'
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
            $totalViews = ShopAnalytics::where('shop_id', $shop->id)
                ->where('event_type', 'shop_view')
                ->count();
            
            $monthlyViews = ShopAnalytics::where('shop_id', $shop->id)
                ->where('event_type', 'shop_view')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            $uniqueVisitors = ShopAnalytics::where('shop_id', $shop->id)
                ->where('event_type', 'shop_view')
                ->distinct('user_ip')
                ->count('user_ip');
            
            // Count phone calls from both tables
            $phoneCalls = ShopAnalytics::where('shop_id', $shop->id)
                ->where('event_type', 'phone_call')
                ->count();
            
            $phoneCallsFromEvents = UserEvent::where('shop_id', $shop->id)
                ->where('event_action', 'phone_call')
                ->count();
            
            $totalPhoneCalls = $phoneCalls + $phoneCallsFromEvents;
            
            // Count map/directions clicks from both tables
            $mapClicks = ShopAnalytics::where('shop_id', $shop->id)
                ->where('event_type', 'map_directions')
                ->count();
            
            $mapClicksFromEvents = UserEvent::where('shop_id', $shop->id)
                ->where('event_action', 'map_directions')
                ->count();
            
            $totalMapClicks = $mapClicks + $mapClicksFromEvents;
            
            // Total contact clicks (calls + maps)
            $contactClicks = $totalPhoneCalls + $totalMapClicks;
            
            $shop->analytics = [
                'total_views' => $totalViews,
                'monthly_views' => $monthlyViews,
                'unique_visitors' => $uniqueVisitors,
                'phone_calls' => $totalPhoneCalls,
                'map_clicks' => $totalMapClicks,
                'contact_clicks' => $contactClicks,
                'conversion_rate' => $totalViews > 0 ? ($contactClicks / $totalViews) * 100 : 0
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
        $singleViewSessions = ShopAnalytics::select(DB::raw('COUNT(DISTINCT shop_analytics.user_ip) as count'))
            ->join('shops', 'shop_analytics.shop_id', '=', 'shops.id')
            ->where('shops.city_id', $cityId)
            ->where('shop_analytics.event_type', 'shop_view')
            ->where('shop_analytics.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('shop_analytics.user_ip')
            ->havingRaw('COUNT(*) = 1')
            ->get()
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
     * User Activity Heatmap
     */
    public function heatmap()
    {
        $days = request('days', 30);
        $dateFrom = Carbon::now()->subDays($days);

        // Total clicks
        $totalClicks = UserEvent::where('event_category', 'interaction')
            ->where('created_at', '>=', $dateFrom)
            ->count();

        // Top page by clicks
        $topPage = UserEvent::select('page_url', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $dateFrom)
            ->groupBy('page_url')
            ->orderBy('count', 'desc')
            ->first();

        // Average scroll depth
        $avgScrollDepth = UserEvent::where('created_at', '>=', $dateFrom)
            ->whereNotNull('scroll_depth')
            ->avg('scroll_depth') ?? 0;

        // Average time on page
        $avgTimeOnPage = UserEvent::where('created_at', '>=', $dateFrom)
            ->whereNotNull('time_on_page')
            ->avg('time_on_page') ?? 0;

        // Top clicked elements
        $topClickedElements = UserEvent::select(
                'event_label',
                'event_action',
                DB::raw('COUNT(*) as clicks')
            )
            ->where('event_category', 'interaction')
            ->where('created_at', '>=', $dateFrom)
            ->whereNotNull('event_label')
            ->groupBy('event_label', 'event_action')
            ->orderBy('clicks', 'desc')
            ->limit(10)
            ->get();

        // Click heatmap data
        $clickHeatmapData = UserEvent::select(
                'event_action as label',
                DB::raw('COUNT(*) as clicks')
            )
            ->whereIn('event_category', ['interaction', 'conversion'])
            ->where('created_at', '>=', $dateFrom)
            ->groupBy('event_action')
            ->orderBy('clicks', 'desc')
            ->limit(15)
            ->get();

        // Scroll depth distribution
        $scrollDepthDistribution = [
            UserEvent::where('created_at', '>=', $dateFrom)->whereBetween('scroll_depth', [0, 25])->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->whereBetween('scroll_depth', [26, 50])->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->whereBetween('scroll_depth', [51, 75])->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->whereBetween('scroll_depth', [76, 100])->count(),
        ];

        // Time distribution
        $timeDistribution = [
            UserEvent::where('created_at', '>=', $dateFrom)->where('time_on_page', '<', 10)->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->whereBetween('time_on_page', [10, 30])->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->whereBetween('time_on_page', [31, 60])->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->whereBetween('time_on_page', [61, 180])->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->where('time_on_page', '>', 180)->count(),
        ];

        // User journeys
        $userJourneys = UserEvent::select(
                'page_url as path',
                DB::raw('COUNT(DISTINCT session_id) as visits'),
                DB::raw('SUM(CASE WHEN event_action IN ("phone_call", "map_directions") THEN 1 ELSE 0 END) as conversions')
            )
            ->where('created_at', '>=', $dateFrom)
            ->whereNotNull('page_url')
            ->groupBy('page_url')
            ->orderBy('visits', 'desc')
            ->limit(10)
            ->get()
            ->map(function($journey) {
                $journey->conversion_rate = $journey->visits > 0 ? ($journey->conversions / $journey->visits) * 100 : 0;
                return $journey;
            });

        // Page performance
        $pagePerformance = UserEvent::select(
                'page_url',
                'page_title',
                DB::raw('COUNT(*) as visits'),
                DB::raw('AVG(time_on_page) as avg_time'),
                DB::raw('AVG(scroll_depth) as avg_scroll'),
                DB::raw('COUNT(DISTINCT session_id) as sessions'),
                DB::raw('SUM(CASE WHEN time_on_page < 5 THEN 1 ELSE 0 END) as bounces'),
                DB::raw('SUM(CASE WHEN event_action IN ("phone_call", "map_directions") THEN 1 ELSE 0 END) as conversions')
            )
            ->where('created_at', '>=', $dateFrom)
            ->where('event_type', 'page_view')
            ->whereNotNull('page_url')
            ->groupBy('page_url', 'page_title')
            ->orderBy('visits', 'desc')
            ->limit(20)
            ->get()
            ->map(function($page) {
                $page->bounce_rate = $page->sessions > 0 ? ($page->bounces / $page->sessions) * 100 : 0;
                $page->avg_time = $page->avg_time ?? 0;
                $page->avg_scroll = $page->avg_scroll ?? 0;
                return $page;
            });

        // Conversion funnel
        $conversionFunnel = [
            UserEvent::where('created_at', '>=', $dateFrom)->where('event_type', 'page_view')->distinct('session_id')->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->whereNotNull('shop_id')->distinct('session_id')->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->where('event_type', 'interaction')->distinct('session_id')->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->whereIn('event_action', ['phone_call', 'map_directions'])->distinct('session_id')->count(),
            UserEvent::where('created_at', '>=', $dateFrom)->where('event_category', 'conversion')->distinct('session_id')->count(),
        ];

        // Generate recommendations
        $recommendations = $this->generateRecommendations($pagePerformance, $avgScrollDepth, $totalClicks);

        return view('admin.analytics.heatmap', compact(
            'totalClicks',
            'topPage',
            'avgScrollDepth',
            'avgTimeOnPage',
            'topClickedElements',
            'clickHeatmapData',
            'scrollDepthDistribution',
            'timeDistribution',
            'userJourneys',
            'pagePerformance',
            'conversionFunnel',
            'recommendations'
        ));
    }

    /**
     * Generate AI-powered recommendations
     */
    private function generateRecommendations($pagePerformance, $avgScrollDepth, $totalClicks)
    {
        $recommendations = [];

        // Low scroll depth
        if ($avgScrollDepth < 50) {
            $recommendations[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => 'عمق تمرير منخفض',
                'description' => 'المستخدمون لا يتصفحون المحتوى بالكامل',
                'solution' => 'أضف محتوى جذاب في بداية الصفحة وقلل من طول الصفحات'
            ];
        }

        // High bounce rate pages
        $highBouncePage = $pagePerformance->firstWhere(fn($p) => $p->bounce_rate > 70);
        if ($highBouncePage) {
            $recommendations[] = [
                'type' => 'warning',
                'icon' => 'sign-out-alt',
                'title' => 'معدل ارتداد عالي',
                'description' => "صفحة {$highBouncePage->page_title} لديها معدل ارتداد {$highBouncePage->bounce_rate}%",
                'solution' => 'حسّن سرعة التحميل وأضف محتوى ذو قيمة في بداية الصفحة'
            ];
        }

        // Low conversion pages
        $lowConversionPage = $pagePerformance->firstWhere(fn($p) => $p->visits > 50 && $p->conversions == 0);
        if ($lowConversionPage) {
            $recommendations[] = [
                'type' => 'info',
                'icon' => 'bullseye',
                'title' => 'فرصة للتحسين',
                'description' => "صفحة {$lowConversionPage->page_title} لديها {$lowConversionPage->visits} زيارة بدون تحويلات",
                'solution' => 'أضف أزرار CTA واضحة (اتصال، الاتجاهات، مشاركة)'
            ];
        }

        // Low engagement
        if ($totalClicks < 100) {
            $recommendations[] = [
                'type' => 'warning',
                'icon' => 'mouse-pointer',
                'title' => 'تفاعل منخفض',
                'description' => 'عدد النقرات منخفض جداً',
                'solution' => 'أضف عناصر تفاعلية أكثر وحسّن تجربة المستخدم'
            ];
        }

        // Good performance
        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'title' => 'أداء ممتاز!',
                'description' => 'موقعك يعمل بشكل جيد',
                'solution' => 'استمر في مراقبة المقاييس وإجراء تحسينات صغيرة'
            ];
        }

        return $recommendations;
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