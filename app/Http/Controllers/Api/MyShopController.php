<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MyShopController extends Controller
{
    /**
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
     * Create new shop
     */
    public function store(Request $request)
    {
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

        $shop = Shop::create($data);
        $shop->load(['city', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Shop created successfully',
            'data' => $shop
        ], 201);
    }

    /**
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