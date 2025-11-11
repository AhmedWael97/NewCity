<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShopController extends Controller
{
    /**
     * Display a listing of shops for admin
     */
    public function index(Request $request): JsonResponse
    {
        $query = Shop::with(['city', 'category', 'user']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by verification
        if ($request->has('verified')) {
            $query->where('is_verified', $request->verified === 'true');
        }

        // Filter by city
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $shops = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $shops
        ]);
    }

    /**
     * Display the specified shop
     */
    public function show(Shop $shop): JsonResponse
    {
        $shop->load(['city', 'category', 'user']);

        return response()->json([
            'success' => true,
            'data' => $shop
        ]);
    }

    /**
     * Update shop verification status
     */
    public function verify(Request $request, Shop $shop): JsonResponse
    {
        $validated = $request->validate([
            'is_verified' => 'required|boolean'
        ]);

        $shop->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shop verification status updated successfully',
            'data' => $shop
        ]);
    }

    /**
     * Update shop active status
     */
    public function toggleStatus(Shop $shop): JsonResponse
    {
        $shop->update(['is_active' => !$shop->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Shop status updated successfully',
            'data' => $shop
        ]);
    }

    /**
     * Remove the specified shop
     */
    public function destroy(Shop $shop): JsonResponse
    {
        $shop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted successfully'
        ]);
    }
}