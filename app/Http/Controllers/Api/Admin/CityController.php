<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CityController extends Controller
{
    /**
     * Get all cities (including inactive)
     */
    public function index(Request $request)
    {
        $query = City::query();

        // Search by name or country
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $cities = $query->withCount('shops')->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    /**
     * Create new city
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['name']);

        // Check for duplicate slug
        $originalSlug = $data['slug'];
        $counter = 1;
        while (City::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $city = City::create($data);

        return response()->json([
            'success' => true,
            'message' => 'City created successfully',
            'data' => $city
        ], 201);
    }

    /**
     * Get single city
     */
    public function show(City $city)
    {
        $city->load(['shops.category', 'shops.user']);
        $city->loadCount('shops');

        return response()->json([
            'success' => true,
            'data' => $city
        ]);
    }

    /**
     * Update city
     */
    public function update(Request $request, City $city)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'is_active' => 'boolean',
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
        if ($data['name'] !== $city->name) {
            $data['slug'] = Str::slug($data['name']);
            
            // Check for duplicate slug
            $originalSlug = $data['slug'];
            $counter = 1;
            while (City::where('slug', $data['slug'])->where('id', '!=', $city->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $city->update($data);

        return response()->json([
            'success' => true,
            'message' => 'City updated successfully',
            'data' => $city
        ]);
    }

    /**
     * Delete city
     */
    public function destroy(City $city)
    {
        // Check if city has shops
        if ($city->shops()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete city with existing shops'
            ], 422);
        }

        $city->delete();

        return response()->json([
            'success' => true,
            'message' => 'City deleted successfully'
        ]);
    }
}