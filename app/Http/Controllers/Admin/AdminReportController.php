<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Display admin reports dashboard
     */
    public function index()
    {
        // Get various report statistics
        $userStats = [
            'total_users' => \App\Models\User::count(),
            'new_users_this_month' => \App\Models\User::whereMonth('created_at', now()->month)->count(),
            'verified_users' => \App\Models\User::where('is_verified', true)->count(),
            'shop_owners' => \App\Models\User::where('user_type', 'shop_owner')->count(),
        ];

        $shopStats = [
            'total_shops' => \App\Models\Shop::count(),
            'active_shops' => \App\Models\Shop::where('is_active', true)->count(),
            'verified_shops' => \App\Models\Shop::where('is_verified', true)->count(),
            'featured_shops' => \App\Models\Shop::where('is_featured', true)->count(),
        ];

        $cityStats = [
            'total_cities' => \App\Models\City::count(),
            'cities_with_shops' => \App\Models\City::whereHas('shops')->count(),
        ];

        $ratingStats = [
            'total_ratings' => \App\Models\Rating::count(),
            'average_rating' => \App\Models\Rating::avg('rating'),
            'ratings_this_month' => \App\Models\Rating::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.reports.index', compact(
            'userStats',
            'shopStats', 
            'cityStats',
            'ratingStats'
        ));
    }

    /**
     * Generate and download reports
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:users,shops,ratings,cities',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'required|in:csv,pdf'
        ]);

        // This would be implemented to generate actual reports
        return back()->with('success', 'Report generation feature will be implemented soon.');
    }
}