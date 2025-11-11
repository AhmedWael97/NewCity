<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFavoriteController extends Controller
{
    /**
     * Display a listing of user favorites.
     */
    public function index(Request $request)
    {
        $query = DB::table('shop_user_favorites')
            ->join('users', 'shop_user_favorites.user_id', '=', 'users.id')
            ->join('shops', 'shop_user_favorites.shop_id', '=', 'shops.id')
            ->select(
                'shop_user_favorites.*',
                'users.name as user_name',
                'users.email as user_email',
                'shops.name as shop_name',
                'shops.status as shop_status'
            );

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('shops.name', 'like', "%{$search}%");
            });
        }

        // User filter
        if ($request->filled('user_id')) {
            $query->where('shop_user_favorites.user_id', $request->user_id);
        }

        // Shop filter
        if ($request->filled('shop_id')) {
            $query->where('shop_user_favorites.shop_id', $request->shop_id);
        }

        $favorites = $query->orderBy('shop_user_favorites.created_at', 'desc')->paginate(20);
        
        $users = User::select('id', 'name', 'email')->get();
        $shops = Shop::select('id', 'name')->where('status', 'approved')->get();

        return view('admin.favorites.index', compact('favorites', 'users', 'shops'));
    }

    /**
     * Display statistics about favorites.
     */
    public function statistics()
    {
        $stats = [
            'total_favorites' => DB::table('shop_user_favorites')->count(),
            'unique_users' => DB::table('shop_user_favorites')->distinct('user_id')->count('user_id'),
            'unique_shops' => DB::table('shop_user_favorites')->distinct('shop_id')->count('shop_id'),
            'most_favorited_shops' => Shop::withCount('favoritedByUsers')
                ->orderBy('favorited_by_users_count', 'desc')
                ->take(10)
                ->get(),
            'most_active_users' => User::withCount('favoriteShops')
                ->orderBy('favorite_shops_count', 'desc')
                ->take(10)
                ->get(),
        ];

        return view('admin.favorites.statistics', compact('stats'));
    }

    /**
     * Remove a favorite relationship.
     */
    public function destroy(Request $request)
    {
        $userId = $request->input('user_id');
        $shopId = $request->input('shop_id');

        DB::table('shop_user_favorites')
            ->where('user_id', $userId)
            ->where('shop_id', $shopId)
            ->delete();

        return back()->with('success', 'Favorite removed successfully.');
    }
}
