<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\ShopImageGenerator;

/**
 * @OA\Tag(
 *     name="My Shops",
 *     description="Endpoints for managing authenticated user's shops (shop owner only)"
 * )
 */
class MyShopController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/my-shops",
     *     summary="Get user's shops",
     *     description="Get all shops owned by the authenticated user",
     *     tags={"My Shops"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
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
     * 
     * Get user's shops
     */
    public function index(Request $request)
    {
        $shops = $request->user()
            ->shops()
            ->with(['city', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $shops
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/my-shops",
     *     summary="Create a new shop",
     *     description="Create a new shop. Only users with shop_owner type can create shops.",
     *     tags={"My Shops"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "city_id", "category_id", "address", "latitude", "longitude"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="My Amazing Shop"),
     *             @OA\Property(property="description", type="string", example="A detailed description of the shop"),
     *             @OA\Property(property="city_id", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="address", type="string", maxLength=500, example="123 Main Street"),
     *             @OA\Property(property="latitude", type="number", format="float", example=40.7128),
     *             @OA\Property(property="longitude", type="number", format="float", example=-74.0060),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="+1234567890"),
     *             @OA\Property(property="email", type="string", format="email", example="shop@example.com"),
     *             @OA\Property(property="website", type="string", format="url", example="https://example.com"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="opening_hours", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Shop created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shop created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Shop")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not a shop owner",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Only shop owners can create shops")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     * 
     * Create new shop
     */
    public function store(Request $request)
    {
        // Check if user is shop owner
        if (!$request->user()->isShopOwner()) {
            return response()->json([
                'success' => false,
                'message' => 'Only shop owners can create shops'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string|max:500',
            'opening_hours' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['name']) . '-' . time();

        // Generate default images if none provided
        if (empty($data['images'])) {
            $category = \App\Models\Category::find($data['category_id']);
            $imageGenerator = new ShopImageGenerator();
            $data['images'] = $imageGenerator->generateMultipleImages(
                $data['name'],
                $category->name ?? 'Shop',
                $category->icon ?? null,
                3
            );
        }

        $shop = Shop::create($data);
        $shop->load(['city', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Shop created successfully',
            'data' => $shop
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/my-shops/{shop}",
     *     summary="Get single shop",
     *     description="Get details of a specific shop owned by the authenticated user",
     *     tags={"My Shops"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="shop",
     *         in="path",
     *         required=true,
     *         description="Shop ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Shop")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Shop does not belong to user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found"
     *     )
     * )
     * 
     * Get single shop
     */
    public function show(Request $request, Shop $shop)
    {
        // Check if shop belongs to authenticated user
        if ($shop->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $shop->load(['city', 'category']);

        return response()->json([
            'success' => true,
            'data' => $shop
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/my-shops/{shop}",
     *     summary="Update shop",
     *     description="Update details of a shop owned by the authenticated user",
     *     tags={"My Shops"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="shop",
     *         in="path",
     *         required=true,
     *         description="Shop ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "address", "latitude", "longitude"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="address", type="string", maxLength=500),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float"),
     *             @OA\Property(property="phone", type="string", maxLength=20),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="website", type="string", format="url"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="opening_hours", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shop updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Shop")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Shop does not belong to user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     * 
     * Update shop
     */
    public function update(Request $request, Shop $shop)
    {
        // Check if shop belongs to authenticated user
        if ($shop->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string|max:500',
            'opening_hours' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        // Update slug if name changed
        if ($data['name'] !== $shop->name) {
            $data['slug'] = Str::slug($data['name']) . '-' . time();
        }

        $shop->update($data);
        $shop->load(['city', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Shop updated successfully',
            'data' => $shop
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/my-shops/{shop}",
     *     summary="Delete shop",
     *     description="Delete a shop owned by the authenticated user",
     *     tags={"My Shops"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="shop",
     *         in="path",
     *         required=true,
     *         description="Shop ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shop deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Shop does not belong to user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found"
     *     )
     * )
     * 
     * Delete shop
     */
    public function destroy(Request $request, Shop $shop)
    {
        // Check if shop belongs to authenticated user
        if ($shop->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $shop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted successfully'
        ]);
    }
}