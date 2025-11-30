<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Marketplace",
 *     description="Marketplace items management endpoints"
 * )
 */
class MarketplaceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/marketplace",
     *     summary="Get marketplace items list",
     *     tags={"Marketplace"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum price",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maximum price",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="condition",
     *         in="query",
     *         description="Item condition",
     *         @OA\Schema(type="string", enum={"new", "like_new", "good", "fair"})
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort by",
     *         @OA\Schema(type="string", enum={"newest", "price_low", "price_high", "most_viewed"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Items retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = MarketplaceItem::query()
            ->with(['user:id,name,phone', 'category:id,name', 'city:id,name'])
            ->active()
            ->availableToView();

        // Sponsored items first, then regular items
        $query->orderByRaw('
            CASE 
                WHEN is_sponsored = 1 AND sponsored_until > NOW() THEN 0 
                ELSE 1 
            END
        ')
        ->orderByDesc('sponsored_priority')
        ->orderByDesc('created_at');

        // Filters
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Custom sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'most_viewed':
                    $query->orderBy('view_count', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }

        $items = $query->paginate(20);

        // Add computed fields to each item
        $items->getCollection()->transform(function ($item) {
            $item->remaining_views = $item->remainingViews();
            $item->is_sponsored_active = $item->isSponsorshipActive();
            $item->sponsorship_days_remaining = $item->getSponsorshipDaysRemaining();
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Marketplace items retrieved successfully',
            'data' => $items,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/marketplace/sponsored",
     *     summary="Get sponsored marketplace items",
     *     tags={"Marketplace"},
     *     @OA\Response(
     *         response=200,
     *         description="Sponsored items retrieved successfully"
     *     )
     * )
     */
    public function sponsored(Request $request): JsonResponse
    {
        $items = MarketplaceItem::query()
            ->with(['user:id,name', 'category:id,name', 'city:id,name'])
            ->active()
            ->sponsored()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Sponsored items retrieved successfully',
            'data' => $items,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/marketplace",
     *     summary="Create a new marketplace item",
     *     tags={"Marketplace"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Item created successfully"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'condition' => 'required|in:new,like_new,good,fair',
            'is_negotiable' => 'boolean',
            'contact_phone' => 'nullable|string|max:20',
            'contact_whatsapp' => 'nullable|string|max:20',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('marketplace', 'public');
                $imagePaths[] = $path;
            }
        }

        $item = MarketplaceItem::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'city_id' => $request->city_id,
            'condition' => $request->condition,
            'is_negotiable' => $request->boolean('is_negotiable', true),
            'contact_phone' => $request->contact_phone ?? Auth::user()->phone,
            'contact_whatsapp' => $request->contact_whatsapp,
            'images' => $imagePaths,
            'status' => 'active', // Auto-approve for now, can add moderation later
            'approved_at' => now(),
            'max_views' => 50, // Default view limit
        ]);

        $item->load(['user', 'category', 'city']);

        return response()->json([
            'success' => true,
            'message' => 'Marketplace item created successfully',
            'data' => $item,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/marketplace/{id}",
     *     summary="Get marketplace item details",
     *     tags={"Marketplace"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item details retrieved successfully"
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        $item = MarketplaceItem::with(['user:id,name,phone,email', 'category', 'city'])
            ->findOrFail($id);

        // Check if item can be viewed
        if (!$item->canBeViewed()) {
            return response()->json([
                'success' => false,
                'message' => 'This item has reached its view limit. Contact the seller directly or ask them to sponsor the item.',
                'data' => [
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'status' => 'view_limit_reached',
                    'seller_contact' => $item->contact_phone,
                ],
            ], 403);
        }

        // Increment view count if not the owner
        if (!Auth::check() || !$item->isOwnedBy(Auth::user())) {
            $item->incrementViewCount();
        }

        // Add computed fields
        $item->remaining_views = $item->remainingViews();
        $item->is_sponsored_active = $item->isSponsorshipActive();
        $item->sponsorship_days_remaining = $item->getSponsorshipDaysRemaining();

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully',
            'data' => $item,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/marketplace/{id}",
     *     summary="Update marketplace item",
     *     tags={"Marketplace"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Item updated successfully"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $item = MarketplaceItem::findOrFail($id);

        // Check ownership
        if (!$item->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this item',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|min:20',
            'price' => 'sometimes|numeric|min:0',
            'condition' => 'sometimes|in:new,like_new,good,fair',
            'is_negotiable' => 'boolean',
            'contact_phone' => 'nullable|string|max:20',
            'contact_whatsapp' => 'nullable|string|max:20',
            'status' => 'sometimes|in:active,sold',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $item->update($request->only([
            'title',
            'description',
            'price',
            'condition',
            'is_negotiable',
            'contact_phone',
            'contact_whatsapp',
            'status',
        ]));

        $item->load(['user', 'category', 'city']);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'data' => $item,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/marketplace/{id}",
     *     summary="Delete marketplace item",
     *     tags={"Marketplace"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Item deleted successfully"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $item = MarketplaceItem::findOrFail($id);

        // Check ownership
        if (!$item->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this item',
            ], 403);
        }

        // Delete images from storage
        if ($item->images) {
            foreach ($item->images as $image) {
                if (str_contains($image, 'storage/marketplace/')) {
                    $path = str_replace(url('storage/'), '', $image);
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/my-marketplace-items",
     *     summary="Get current user's marketplace items",
     *     tags={"Marketplace"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Items retrieved successfully"
     *     )
     * )
     */
    public function myItems(Request $request): JsonResponse
    {
        $items = MarketplaceItem::with(['category', 'city', 'activeSponsorship'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        // Add computed fields
        $items->getCollection()->transform(function ($item) {
            $item->remaining_views = $item->remainingViews();
            $item->is_sponsored_active = $item->isSponsorshipActive();
            $item->sponsorship_days_remaining = $item->getSponsorshipDaysRemaining();
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Your items retrieved successfully',
            'data' => $items,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/marketplace/{id}/contact",
     *     summary="Record contact attempt",
     *     tags={"Marketplace"},
     *     @OA\Response(
     *         response=200,
     *         description="Contact recorded successfully"
     *     )
     * )
     */
    public function recordContact($id): JsonResponse
    {
        $item = MarketplaceItem::findOrFail($id);
        $item->incrementContactCount();

        return response()->json([
            'success' => true,
            'message' => 'Contact recorded successfully',
            'data' => [
                'contact_phone' => $item->contact_phone,
                'contact_whatsapp' => $item->contact_whatsapp,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/marketplace/{id}/mark-sold",
     *     summary="Mark item as sold",
     *     tags={"Marketplace"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Item marked as sold"
     *     )
     * )
     */
    public function markAsSold($id): JsonResponse
    {
        $item = MarketplaceItem::findOrFail($id);

        if (!$item->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to modify this item',
            ], 403);
        }

        $item->markAsSold();

        return response()->json([
            'success' => true,
            'message' => 'Item marked as sold successfully',
            'data' => $item,
        ]);
    }
}
