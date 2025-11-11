<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Shop Favorites",
 *     description="Endpoints for managing favorite shops"
 * )
 */
class ShopFavoriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/user/favorites",
     *     summary="Get user's favorite shops",
     *     tags={"Shop Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorites retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $favorites = $user->favoriteShops()
            ->with(['city:id,name', 'category:id,name'])
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Favorites retrieved successfully',
            'data' => $favorites
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/shops/{shopId}/favorite",
     *     summary="Add shop to favorites",
     *     tags={"Shop Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="shopId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop added to favorites"
     *     )
     * )
     */
    public function store($shopId)
    {
        $shop = Shop::findOrFail($shopId);
        $user = Auth::user();

        // Check if already favorited
        if ($user->favoriteShops()->where('shop_id', $shopId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Shop is already in favorites'
            ], 422);
        }

        $user->favoriteShops()->attach($shopId);

        return response()->json([
            'success' => true,
            'message' => 'Shop added to favorites',
            'data' => [
                'shop' => $shop->only(['id', 'name', 'slug', 'images'])
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/shops/{shopId}/favorite",
     *     summary="Remove shop from favorites",
     *     tags={"Shop Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="shopId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop removed from favorites"
     *     )
     * )
     */
    public function destroy($shopId)
    {
        $shop = Shop::findOrFail($shopId);
        $user = Auth::user();

        $user->favoriteShops()->detach($shopId);

        return response()->json([
            'success' => true,
            'message' => 'Shop removed from favorites'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/shops/{shopId}/is-favorite",
     *     summary="Check if shop is in user's favorites",
     *     tags={"Shop Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="shopId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite status retrieved"
     *     )
     * )
     */
    public function check($shopId)
    {
        $shop = Shop::findOrFail($shopId);
        $user = Auth::user();

        $isFavorite = $user->favoriteShops()->where('shop_id', $shopId)->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_favorite' => $isFavorite
            ]
        ]);
    }
}
