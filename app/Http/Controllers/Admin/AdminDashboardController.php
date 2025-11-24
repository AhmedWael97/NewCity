<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Rating;
use App\Models\City;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\ShopAnalytics;
use App\Models\ShopSubscription;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function index()
    {
        // Overview Statistics
        $stats = [
            'total_users' => User::count(),
            'total_shops' => Shop::count(),
            'total_cities' => City::count(),
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
            'total_services' => Service::count(),
            'total_ratings' => Rating::count(),
            'active_subscriptions' => ShopSubscription::where('status', 'active')->count(),
            'pending_tickets' => SupportTicket::where('status', 'open')->count(),
            'pending_shops' => Shop::where('status', 'pending')->count(),
            'verified_shops' => Shop::where('is_verified', true)->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'new_shops_this_month' => Shop::whereMonth('created_at', now()->month)->count(),
        ];

        // Recent Growth (this month vs last month)
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $growth = [
            'users' => [
                'current' => User::where('created_at', '>=', $currentMonth)->count(),
                'previous' => User::whereBetween('created_at', [$lastMonth, $currentMonth])->count()
            ],
            'shops' => [
                'current' => Shop::where('created_at', '>=', $currentMonth)->count(),
                'previous' => Shop::whereBetween('created_at', [$lastMonth, $currentMonth])->count()
            ]
        ];

        // Top Performing Shops (by views)
        $topShops = ShopAnalytics::select('shop_id', DB::raw('COUNT(*) as total_views'))
            ->where('event_type', 'shop_view')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('shop_id')
            ->orderBy('total_views', 'desc')
            ->with('shop')
            ->limit(10)
            ->get();

        // Top Cities (by visitor count)
        $topCities = ShopAnalytics::select('shops.city_id', DB::raw('COUNT(DISTINCT shop_analytics.user_ip) as unique_visitors'))
            ->join('shops', 'shop_analytics.shop_id', '=', 'shops.id')
            ->where('shop_analytics.created_at', '>=', Carbon::now()->subDays(30))
            ->where('shop_analytics.event_type', 'shop_view')
            ->whereNotNull('shops.city_id')
            ->groupBy('shops.city_id')
            ->orderBy('unique_visitors', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                // Create a simple object with city relationship
                $cityData = new \stdClass();
                $cityData->unique_visitors = $item->unique_visitors;
                $cityData->city = \App\Models\City::select('id', 'name_ar', 'governorate')->find($item->city_id);
                return $cityData;
            });

        // Revenue Analytics (subscription revenue)
        $revenueData = ShopSubscription::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount_paid) as daily_revenue')
        )
        ->where('status', 'active')
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Get recent activities
        $recent_users = User::latest()->take(5)->get();
        $recent_shops = Shop::with(['user', 'city', 'category'])->latest()->take(5)->get();
        $recent_ratings = Rating::with(['user', 'shop'])->latest()->take(5)->get();

        // Recent Subscriptions
        $recentSubscriptions = ShopSubscription::with(['shop', 'subscriptionPlan'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Support Tickets
        $recentTickets = SupportTicket::with(['user', 'city'])
            ->latest()
            ->take(5)
            ->get();

        // Popular Categories
        $popularCategories = Category::withCount(['shops' => function($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }])
        ->orderBy('shops_count', 'desc')
        ->limit(10)
        ->get();

        // Analytics Chart Data (last 30 days)
        $analyticsData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $analyticsData[] = [
                'date' => $date,
                'views' => ShopAnalytics::whereDate('created_at', $date)
                    ->where('event_type', 'shop_view')
                    ->count(),
                'searches' => ShopAnalytics::whereDate('created_at', $date)
                    ->where('event_type', 'search')
                    ->count(),
                'new_users' => User::whereDate('created_at', $date)->count(),
                'new_shops' => Shop::whereDate('created_at', $date)->count()
            ];
        }

        // Get monthly statistics for charts
        $monthly_stats = $this->getMonthlyStats();
        
        // Get top rated shops
        $top_shops = Shop::where('rating', '>', 0)
            ->orderBy('rating', 'desc')
            ->take(10)
            ->get();

        // Get user distribution by type
        $user_distribution = User::select('user_type', DB::raw('count(*) as count'))
            ->groupBy('user_type')
            ->get();

        // Get shop distribution by status
        $shop_distribution = Shop::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'growth',
            'topShops',
            'topCities',
            'revenueData',
            'popularCategories',
            'analyticsData',
            'recent_users',
            'recent_shops',
            'recent_ratings',
            'recentSubscriptions',
            'recentTickets',
            'monthly_stats',
            'top_shops',
            'user_distribution',
            'shop_distribution'
        ));
    }

    /**
     * Get monthly statistics for the last 12 months
     */
    private function getMonthlyStats()
    {
        $months = [];
        $users_data = [];
        $shops_data = [];
        $ratings_data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $users_data[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $shops_data[] = Shop::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $ratings_data[] = Rating::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'months' => $months,
            'users' => $users_data,
            'shops' => $shops_data,
            'ratings' => $ratings_data,
        ];
    }

    /**
     * Get system health status
     */
    public function systemHealth()
    {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
            'queues' => $this->checkQueueHealth(),
        ];

        return response()->json($health);
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    private function checkStorageHealth()
    {
        try {
            $diskSpace = disk_free_space(storage_path());
            $totalSpace = disk_total_space(storage_path());
            $usedPercent = (($totalSpace - $diskSpace) / $totalSpace) * 100;
            
            $status = $usedPercent > 90 ? 'warning' : 'healthy';
            return [
                'status' => $status,
                'message' => sprintf('Disk usage: %.1f%%', $usedPercent),
                'free_space' => $this->formatBytes($diskSpace),
                'total_space' => $this->formatBytes($totalSpace)
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Unable to check storage'];
        }
    }

    private function checkCacheHealth()
    {
        try {
            cache()->put('health_check', 'ok', 60);
            $test = cache()->get('health_check');
            return ['status' => 'healthy', 'message' => 'Cache is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache is not working'];
        }
    }

    private function checkQueueHealth()
    {
        try {
            // Check if any failed jobs exist
            $failedJobs = DB::table('failed_jobs')->count();
            $status = $failedJobs > 0 ? 'warning' : 'healthy';
            return [
                'status' => $status,
                'message' => $failedJobs > 0 ? "{$failedJobs} failed jobs" : 'No failed jobs',
                'failed_jobs' => $failedJobs
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Unable to check queue status'];
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $base = log($size, 1024);
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
    }
}