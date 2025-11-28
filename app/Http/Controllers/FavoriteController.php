<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display user's favorite shops
     */
    public function index()
    {
        $user = Auth::user();
        
        $favorites = $user->favoriteShops()
            ->with(['city', 'category'])
            ->withCount('ratings')
            ->latest('shop_user_favorites.created_at')
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Add shop to favorites (AJAX)
     */
    public function addShop(Request $request, Shop $shop)
    {
        $user = Auth::user();

        // Check if already favorited
        if ($user->favoriteShops()->where('shop_id', $shop->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'المتجر موجود بالفعل في المفضلة',
                'is_favorite' => true
            ], 422);
        }

        $user->favoriteShops()->attach($shop->id);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المتجر للمفضلة بنجاح',
            'is_favorite' => true
        ]);
    }

    /**
     * Remove shop from favorites (AJAX)
     */
    public function removeShop(Request $request, Shop $shop)
    {
        $user = Auth::user();

        $user->favoriteShops()->detach($shop->id);

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة المتجر من المفضلة بنجاح',
            'is_favorite' => false
        ]);
    }

    /**
     * Check if shop is in favorites (AJAX)
     */
    public function checkShop(Request $request, Shop $shop)
    {
        $user = Auth::user();
        
        $isFavorite = $user->favoriteShops()->where('shop_id', $shop->id)->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }
}
