<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Rating;
use App\Services\AdminEmailQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Shop Reviews",
 *     description="Endpoints for shop reviews management"
 * )
 */
class ShopReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/shops/{shopId}/reviews",
     *     summary="Get shop reviews",
     *     tags={"Shop Reviews"},
     *     @OA\Parameter(
     *         name="shopId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop reviews retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request, $shopId)
    {
        $shop = Shop::findOrFail($shopId);
        
        $reviews = Rating::where('shop_id', $shopId)
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Shop reviews retrieved successfully',
            'data' => [
                'shop' => [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'rating' => $shop->rating,
                    'review_count' => $shop->review_count,
                ],
                'reviews' => $reviews,
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/shops/{shopId}/reviews",
     *     summary="Submit a shop review",
     *     tags={"Shop Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="shopId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating", "comment"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="comment", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review submitted successfully"
     *     )
     * )
     */
    public function store(Request $request, $shopId)
    {
        $shop = Shop::findOrFail($shopId);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        // Check if user already reviewed this shop
        $existingReview = Rating::where('shop_id', $shopId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this shop'
            ], 422);
        }

        $review = Rating::create([
            'user_id' => Auth::id(),
            'shop_id' => $shopId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending',
        ]);

        // Queue email notification to admins (API request)
        AdminEmailQueueService::queueShopRating($review->load('shop'));

        // Note: Shop rating will be updated when admin approves the review
        // $this->updateShopRating($shop);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => [
                'review' => $review->load('user:id,name,email')
            ]
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/shops/{shopId}/reviews/{reviewId}",
     *     summary="Update a shop review",
     *     tags={"Shop Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="shopId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="reviewId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="comment", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review updated successfully"
     *     )
     * )
     */
    public function update(Request $request, $shopId, $reviewId)
    {
        $shop = Shop::findOrFail($shopId);
        $review = Rating::where('shop_id', $shopId)
            ->where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'sometimes|required|string|min:10|max:1000',
        ]);

        // Reset status to pending when user updates their review
        $validated['status'] = 'pending';
        $review->update($validated);

        // Update shop rating (will recalculate without this pending review)
        $this->updateShopRating($shop);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => [
                'review' => $review->load('user:id,name,email')
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/shops/{shopId}/reviews/{reviewId}",
     *     summary="Delete a shop review",
     *     tags={"Shop Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="shopId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="reviewId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review deleted successfully"
     *     )
     * )
     */
    public function destroy($shopId, $reviewId)
    {
        $shop = Shop::findOrFail($shopId);
        $review = Rating::where('shop_id', $shopId)
            ->where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $review->delete();

        // Update shop rating
        $this->updateShopRating($shop);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Update shop rating and review count
     */
    private function updateShopRating(Shop $shop)
    {
        $shop->updateRating();
    }
}