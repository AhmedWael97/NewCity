<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\ShopAnalytics;

class ShopController extends Controller
{
    public function show($slug)
    {
        $shop = Shop::where('slug', $slug)
            ->with([
                'city',
                'category',
                'activeProducts' => function($query) {
                    $query->orderBy('is_featured', 'desc')
                          ->orderBy('sort_order')
                          ->orderBy('name');
                },
                'activeServices' => function($query) {
                    $query->orderBy('is_featured', 'desc')
                          ->orderBy('sort_order')
                          ->orderBy('name');
                },
                'ratings' => function($query) {
                    $query->with('user:id,name,avatar')
                          ->latest()
                          ->limit(5);
                }
            ])
            ->withCount([
                'analytics as total_views' => function($query) {
                    $query->where('event_type', 'shop_view');
                }
            ])
            ->firstOrFail();

        // Track shop view
        ShopAnalytics::track($shop->id, 'shop_view', Auth::id(), [
            'city_id' => $shop->city_id,
            'category_id' => $shop->category_id
        ]);

        // Get featured products and services separately for highlights
        $featuredProducts = $shop->activeProducts->where('is_featured', true)->take(6);
        $featuredServices = $shop->activeServices->where('is_featured', true)->take(6);
        
        // Get regular products and services for tabs
        $products = $shop->activeProducts->take(12);
        $services = $shop->activeServices->take(12);

        // Get user's rating if authenticated
        $userRating = null;
        if (Auth::check()) {
            $userRating = $shop->ratings()
                              ->where('user_id', Auth::id())
                              ->first();
        }

        // Get similar shops (same category and city, excluding current shop)
        $similarShops = Shop::where('id', '!=', $shop->id)
            ->where('category_id', $shop->category_id)
            ->where('city_id', $shop->city_id)
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['city:id,name', 'category:id,name'])
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('is_featured')
            ->limit(3)
            ->get();

        $seoData = [
            'title' => $shop->name . ' — ' . ($shop->city->name ?? ''),
            'description' => substr($shop->description ?? $shop->name, 0, 160),
            'keywords' => $shop->name . ', ' . ($shop->category->name ?? ''),
            'canonical' => route('shop.show', $shop->slug),
            'og_image' => ($shop->images && is_array($shop->images) && count($shop->images)) ? asset('storage/' . $shop->images[0]) : asset('images/og-default.jpg'),
        ];

        // Contact info is now globally available via ContactInfoServiceProvider

        return view('shop', compact('shop', 'seoData', 'featuredProducts', 'featuredServices', 'products', 'services', 'userRating', 'similarShops'));
    }
}
