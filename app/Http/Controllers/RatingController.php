<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    /**
     * Store a new rating
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|exists:shops,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();
        $shopId = $request->shop_id;

        // Check if user already rated this shop
        $existingRating = Rating::where('user_id', $userId)
                               ->where('shop_id', $shopId)
                               ->first();

        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قمت بتقييم هذا المتجر من قبل'
            ], 409);
        }

        // Create new rating with pending status
        $rating = Rating::create([
            'user_id' => $userId,
            'shop_id' => $shopId,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        // Update shop rating
        $shop = Shop::find($shopId);
        $shop->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة التقييم بنجاح',
            'data' => [
                'rating' => $rating->load('user'),
                'shop_rating' => $shop->fresh(['rating', 'review_count'])
            ]
        ]);
    }

    /**
     * Update an existing rating
     */
    public function update(Request $request, Rating $rating)
    {
        // Check if user owns this rating
        if ($rating->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بتعديل هذا التقييم'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $rating->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update shop rating
        $rating->shop->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التقييم بنجاح',
            'data' => [
                'rating' => $rating->fresh(['user']),
                'shop_rating' => $rating->shop->fresh(['rating', 'review_count'])
            ]
        ]);
    }

    /**
     * Delete a rating
     */
    public function destroy(Rating $rating)
    {
        // Check if user owns this rating or is admin
        if ($rating->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بحذف هذا التقييم'
            ], 403);
        }

        $shop = $rating->shop;
        $rating->delete();

        // Update shop rating
        $shop->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التقييم بنجاح',
            'data' => [
                'shop_rating' => $shop->fresh(['rating', 'review_count'])
            ]
        ]);
    }

    /**
     * Get ratings for a shop
     */
    public function index(Request $request, Shop $shop)
    {
        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = $shop->ratings()->with('user:id,name,avatar');

        // Filter by rating
        if ($request->has('rating_filter')) {
            $query->where('rating', $request->rating_filter);
        }

        // Filter by verified only
        if ($request->get('verified_only')) {
            $query->where('is_verified', true);
        }

        // Filter by comments only
        if ($request->get('comments_only')) {
            $query->whereNotNull('comment')->where('comment', '!=', '');
        }

        $ratings = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'ratings' => $ratings,
                'shop_stats' => [
                    'average_rating' => $shop->averageRating(),
                    'total_ratings' => $shop->totalRatings(),
                    'rating_distribution' => $shop->getRatingDistribution()
                ]
            ]
        ]);
    }

    /**
     * Mark rating as helpful/not helpful
     */
    public function toggleHelpful(Request $request, Rating $rating)
    {
        $userId = Auth::id();
        $helpfulVotes = $rating->helpful_votes ?? [];

        if (in_array($userId, $helpfulVotes)) {
            // Remove vote
            $helpfulVotes = array_filter($helpfulVotes, function($id) use ($userId) {
                return $id !== $userId;
            });
            $message = 'تم إلغاء التصويت';
        } else {
            // Add vote
            $helpfulVotes[] = $userId;
            $message = 'تم التصويت كمفيد';
        }

        $rating->update(['helpful_votes' => array_values($helpfulVotes)]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'helpful_count' => count($helpfulVotes),
                'is_helpful' => in_array($userId, $helpfulVotes)
            ]
        ]);
    }

    /**
     * Get user's rating for a specific shop
     */
    public function getUserRating(Shop $shop)
    {
        $rating = Rating::where('user_id', Auth::id())
                       ->where('shop_id', $shop->id)
                       ->first();

        return response()->json([
            'success' => true,
            'data' => $rating
        ]);
    }
}
