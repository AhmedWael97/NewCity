<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Http\Resources\ShopCollection;
use App\Http\Resources\RatingResource;
use App\Models\Shop;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Shops",
 *     description="Shop management endpoints"
 * )
 */
class ShopController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/shops",
     *     summary="Get all shops with filtering",
     *     tags={"Shops"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by shop name, description, or address",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Filter featured shops only",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="User latitude for distance calculation",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="User longitude for distance calculation",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Search radius in kilometers",
     *         @OA\Schema(type="number", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shops list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Shop")),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * Get all shops with filtering
     */
    public function index(Request $request)
    {
        $query = Shop::query()
            ->active()
            ->with(['city', 'category', 'user'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc');

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by city
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by verified status
        if ($request->has('verified') && $request->verified) {
            $query->verified();
        }

        // Filter by featured status
        if ($request->has('featured') && $request->featured) {
            $query->featured();
        }

        // Filter by minimum rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        $shops = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $shops
        ]);
    }

    /**
     * Get single shop
     */
    public function show(Shop $shop)
    {
        if (!$shop->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        $shop->load(['city', 'category', 'user']);

        return response()->json([
            'success' => true,
            'data' => $shop
        ]);
    }

    /**
     * Find nearby shops
     */
    public function nearby(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10; // Default 10km radius
        $limit = $request->limit ?? 20;

        $query = Shop::query()
            ->active()
            ->with(['city', 'category', 'user'])
            ->withinRadius($latitude, $longitude, $radius);

        // Filter by category if specified
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $shops = $query->take($limit)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shops' => $shops,
                'center' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ],
                'radius' => $radius
            ]
        ]);
    }
}