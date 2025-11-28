<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

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
     *     security={{"sanctum":{}}},
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
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول لعرض المفضلة'
            ], 401);
        }
        
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
     *     security={{"sanctum":{}}},
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
    public function store(Request $request, $shopId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول لإضافة المتجر للمفضلة'
            ], 401);
        }
        
        $shop = Shop::findOrFail($shopId);

        // Check if already favorited
        if ($user->favoriteShops()->where('shop_id', $shopId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'المتجر موجود بالفعل في المفضلة'
            ], 422);
        }

        $user->favoriteShops()->attach($shopId);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المتجر للمفضلة بنجاح',
            'data' => [
                'shop' => $shop->only(['id', 'name', 'slug', 'images']),
                'is_favorite' => true
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/shops/{shopId}/favorite",
     *     summary="Remove shop from favorites",
     *     tags={"Shop Favorites"},
     *     security={{"sanctum":{}}},
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
    public function destroy(Request $request, $shopId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول لإزالة المتجر من المفضلة'
            ], 401);
        }
        
        $shop = Shop::findOrFail($shopId);

        $user->favoriteShops()->detach($shopId);

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة المتجر من المفضلة بنجاح',
            'data' => [
                'is_favorite' => false
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/shops/{shopId}/is-favorite",
     *     summary="Check if shop is in user's favorites",
     *     tags={"Shop Favorites"},
     *     security={{"sanctum":{}}},
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
    public function check(Request $request, $shopId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'User not authenticated',
                'data' => [
                    'is_favorite' => false
                ]
            ], 200);
        }
        
        $shop = Shop::findOrFail($shopId);

        $isFavorite = $user->favoriteShops()->where('shop_id', $shopId)->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_favorite' => $isFavorite
            ]
        ]);
    }
}
