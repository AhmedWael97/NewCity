<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShopOwnerDashboardController extends Controller
{
    /**
     * Display the shop owner dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's shops
        $shops = Shop::where('owner_id', $user->id)->get();
        $shopIds = $shops->pluck('id');

        // Calculate statistics
        $stats = [
            'total_shops' => $shops->count(),
            'active_shops' => $shops->where('status', 'active')->count(),
            'pending_shops' => $shops->where('status', 'pending')->count(),
            'verified_shops' => $shops->where('is_verified', true)->count(),
            'featured_shops' => $shops->where('is_featured', true)->count(),
            'total_ratings' => Rating::whereIn('shop_id', $shopIds)->count(),
            'average_rating' => Rating::whereIn('shop_id', $shopIds)->avg('rating') ?? 0,
            'total_views' => $shops->sum('views') ?? 0,
        ];

        // Monthly statistics for the last 12 months
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'ratings' => Rating::whereIn('shop_id', $shopIds)
                                 ->whereYear('created_at', $date->year)
                                 ->whereMonth('created_at', $date->month)
                                 ->count(),
                'average_rating' => Rating::whereIn('shop_id', $shopIds)
                                        ->whereYear('created_at', $date->year)
                                        ->whereMonth('created_at', $date->month)
                                        ->avg('rating') ?? 0,
            ];
        }

        // Recent ratings
        $recentRatings = Rating::whereIn('shop_id', $shopIds)
                             ->with(['user', 'shop'])
                             ->orderBy('created_at', 'desc')
                             ->limit(10)
                             ->get();

        // Shop performance
        $shopPerformance = $shops->map(function ($shop) {
            return [
                'shop' => $shop,
                'ratings_count' => $shop->ratings()->count(),
                'average_rating' => $shop->ratings()->avg('rating') ?? 0,
                'views' => $shop->views ?? 0,
                'last_rating' => $shop->ratings()->latest()->first()?->created_at,
            ];
        })->sortByDesc('average_rating');

        return view('shop-owner.dashboard', compact(
            'stats',
            'monthlyStats',
            'recentRatings',
            'shopPerformance',
            'shops'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests.
     */
    public function getStats()
    {
        $user = Auth::user();
        $shops = Shop::where('owner_id', $user->id)->get();
        $shopIds = $shops->pluck('id');

        $stats = [
            'total_shops' => $shops->count(),
            'active_shops' => $shops->where('status', 'active')->count(),
            'pending_shops' => $shops->where('status', 'pending')->count(),
            'verified_shops' => $shops->where('is_verified', true)->count(),
            'featured_shops' => $shops->where('is_featured', true)->count(),
            'total_ratings' => Rating::whereIn('shop_id', $shopIds)->count(),
            'average_rating' => Rating::whereIn('shop_id', $shopIds)->avg('rating') ?? 0,
            'total_views' => $shops->sum('views') ?? 0,
            'today_ratings' => Rating::whereIn('shop_id', $shopIds)
                                   ->whereDate('created_at', today())
                                   ->count(),
            'this_week_ratings' => Rating::whereIn('shop_id', $shopIds)
                                       ->whereBetween('created_at', [
                                           now()->startOfWeek(),
                                           now()->endOfWeek()
                                       ])
                                       ->count(),
            'this_month_ratings' => Rating::whereIn('shop_id', $shopIds)
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get monthly chart data.
     */
    public function getMonthlyChart()
    {
        $user = Auth::user();
        $shops = Shop::where('owner_id', $user->id)->get();
        $shopIds = $shops->pluck('id');

        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'ratings' => Rating::whereIn('shop_id', $shopIds)
                                 ->whereYear('created_at', $date->year)
                                 ->whereMonth('created_at', $date->month)
                                 ->count(),
                'average_rating' => Rating::whereIn('shop_id', $shopIds)
                                        ->whereYear('created_at', $date->year)
                                        ->whereMonth('created_at', $date->month)
                                        ->avg('rating') ?? 0,
            ];
        }

        return response()->json($monthlyData);
    }

    /**
     * Get recent activities.
     */
    public function getRecentActivities()
    {
        $user = Auth::user();
        $shops = Shop::where('owner_id', $user->id)->get();
        $shopIds = $shops->pluck('id');

        $recentRatings = Rating::whereIn('shop_id', $shopIds)
                             ->with(['user', 'shop'])
                             ->orderBy('created_at', 'desc')
                             ->limit(20)
                             ->get();

        return response()->json($recentRatings);
    }

    /**
     * Get shop performance analytics.
     */
    public function getShopAnalytics(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'month'); // day, week, month, year
        
        $shops = Shop::where('owner_id', $user->id)->get();
        $shopIds = $shops->pluck('id');

        $analytics = [];

        foreach ($shops as $shop) {
            $query = Rating::where('shop_id', $shop->id);
            
            switch ($period) {
                case 'day':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }

            $analytics[] = [
                'shop' => $shop,
                'ratings_count' => $query->count(),
                'average_rating' => $query->avg('rating') ?? 0,
                'rating_distribution' => [
                    5 => $query->clone()->where('rating', 5)->count(),
                    4 => $query->clone()->where('rating', 4)->count(),
                    3 => $query->clone()->where('rating', 3)->count(),
                    2 => $query->clone()->where('rating', 2)->count(),
                    1 => $query->clone()->where('rating', 1)->count(),
                ],
                'views' => $shop->views ?? 0,
            ];
        }

        return response()->json($analytics);
    }

    /**
     * Update shop owner profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $user->update($request->only(['name', 'phone', 'city_id']));

        return redirect()
            ->back()
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * Get notifications for shop owner.
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $shops = Shop::where('owner_id', $user->id)->get();
        $shopIds = $shops->pluck('id');

        $notifications = [
            'new_ratings' => Rating::whereIn('shop_id', $shopIds)
                                 ->where('created_at', '>=', now()->subDays(7))
                                 ->count(),
            'pending_shops' => $shops->where('status', 'pending')->count(),
            'unverified_shops' => $shops->where('is_verified', false)->count(),
            'low_rated_shops' => $shops->filter(function ($shop) {
                return $shop->ratings()->avg('rating') < 3 && $shop->ratings()->count() >= 5;
            })->count(),
        ];

        return response()->json($notifications);
    }
}