<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRatingController extends Controller
{
    /**
     * Display a listing of ratings.
     */
    public function index(Request $request)
    {
        $query = Rating::with(['user', 'shop']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('shop', function($shopQuery) use ($search) {
                      $shopQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by rating value
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by shop
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $ratings = $query->paginate(15)->withQueryString();

        // Get filter options
        $shops = Shop::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.ratings.index', compact('ratings', 'shops', 'users'));
    }

    /**
     * Display the specified rating.
     */
    public function show(Rating $rating)
    {
        $rating->load(['user', 'shop.city']);
        
        return view('admin.ratings.show', compact('rating'));
    }

    /**
     * Show the form for editing the specified rating.
     */
    public function edit(Rating $rating)
    {
        $rating->load(['user', 'shop']);
        
        return view('admin.ratings.edit', compact('rating'));
    }

    /**
     * Update the specified rating in storage.
     */
    public function update(Request $request, Rating $rating)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'status' => 'required|in:active,hidden,reported',
        ]);

        $rating->update($request->only(['rating', 'comment', 'status']));

        return redirect()
            ->route('admin.ratings.index')
            ->with('success', 'تم تحديث التقييم بنجاح');
    }

    /**
     * Remove the specified rating from storage.
     */
    public function destroy(Rating $rating)
    {
        $rating->delete();

        return redirect()
            ->route('admin.ratings.index')
            ->with('success', 'تم حذف التقييم بنجاح');
    }

    /**
     * Approve a rating (set status to active).
     */
    public function approve(Rating $rating)
    {
        $rating->update(['status' => 'active']);

        return redirect()
            ->back()
            ->with('success', 'تم الموافقة على التقييم');
    }

    /**
     * Hide a rating (set status to hidden).
     */
    public function hide(Rating $rating)
    {
        $rating->update(['status' => 'hidden']);

        return redirect()
            ->back()
            ->with('success', 'تم إخفاء التقييم');
    }

    /**
     * Report a rating (set status to reported).
     */
    public function report(Rating $rating)
    {
        $rating->update(['status' => 'reported']);

        return redirect()
            ->back()
            ->with('success', 'تم الإبلاغ عن التقييم');
    }

    /**
     * Verify a rating.
     */
    public function verify(Rating $rating)
    {
        $rating->update(['is_verified' => true]);

        return redirect()
            ->back()
            ->with('success', 'تم التحقق من التقييم بنجاح');
    }

    /**
     * Toggle rating status.
     */
    public function toggleStatus(Rating $rating)
    {
        $newStatus = $rating->status === 'active' ? 'hidden' : 'active';
        
        $rating->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'تم إظهار التقييم' : 'تم إخفاء التقييم';

        return redirect()
            ->back()
            ->with('success', $statusText);
    }

    /**
     * Bulk actions for ratings.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ratings' => 'required|array',
            'ratings.*' => 'exists:ratings,id',
            'action' => 'required|in:approve,hide,report,delete'
        ]);

        $ratings = Rating::whereIn('id', $request->ratings);
        $count = $ratings->count();

        switch ($request->action) {
            case 'approve':
                $ratings->update(['status' => 'active']);
                $message = "تم الموافقة على {$count} تقييم";
                break;
                
            case 'hide':
                $ratings->update(['status' => 'hidden']);
                $message = "تم إخفاء {$count} تقييم";
                break;
                
            case 'report':
                $ratings->update(['status' => 'reported']);
                $message = "تم الإبلاغ عن {$count} تقييم";
                break;
                
            case 'delete':
                $ratings->delete();
                $message = "تم حذف {$count} تقييم";
                break;
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Get ratings statistics for dashboard.
     */
    public function getStats()
    {
        $stats = [
            'total' => Rating::count(),
            'active' => Rating::where('status', 'active')->count(),
            'hidden' => Rating::where('status', 'hidden')->count(),
            'reported' => Rating::where('status', 'reported')->count(),
            'today' => Rating::whereDate('created_at', today())->count(),
            'this_week' => Rating::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => Rating::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->count(),
            'average_rating' => Rating::where('status', 'active')->avg('rating') ?? 0,
        ];

        // Ratings distribution
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = Rating::where('status', 'active')
                                    ->where('rating', $i)
                                    ->count();
        }
        $stats['distribution'] = $distribution;

        return response()->json($stats);
    }

    /**
     * Get top rated shops.
     */
    public function getTopRatedShops($limit = 10)
    {
        $shops = Shop::withAvg('ratings', 'rating')
                    ->withCount('ratings')
                    ->having('ratings_count', '>=', 5) // At least 5 ratings
                    ->orderBy('ratings_avg_rating', 'desc')
                    ->limit($limit)
                    ->get();

        return response()->json($shops);
    }

    /**
     * Get recent ratings.
     */
    public function getRecentRatings($limit = 10)
    {
        $ratings = Rating::with(['user', 'shop'])
                        ->where('status', 'active')
                        ->orderBy('created_at', 'desc')
                        ->limit($limit)
                        ->get();

        return response()->json($ratings);
    }

    /**
     * Get ratings analytics data.
     */
    public function getAnalytics(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        
        $query = Rating::where('status', 'active');
        
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

        $analytics = [
            'total_ratings' => $query->count(),
            'average_rating' => $query->avg('rating') ?? 0,
            'distribution' => [],
            'trend' => []
        ];

        // Ratings distribution
        for ($i = 1; $i <= 5; $i++) {
            $analytics['distribution'][$i] = $query->clone()->where('rating', $i)->count();
        }

        // Trend data (last 30 days)
        $trendQuery = Rating::where('status', 'active')
                           ->whereBetween('created_at', [
                               now()->subDays(30),
                               now()
                           ]);

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $analytics['trend'][] = [
                'date' => $date,
                'count' => $trendQuery->clone()->whereDate('created_at', $date)->count(),
                'average' => $trendQuery->clone()->whereDate('created_at', $date)->avg('rating') ?? 0
            ];
        }

        return response()->json($analytics);
    }
}