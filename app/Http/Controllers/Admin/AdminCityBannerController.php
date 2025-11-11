<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CityBanner;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCityBannerController extends Controller
{
    /**
     * Display a listing of city banners.
     */
    public function index(Request $request)
    {
        $query = CityBanner::with('city');

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $banners = $query->orderBy('priority', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        $cities = City::where('is_active', true)
                     ->orderBy('name')
                     ->get(['id', 'name']);

        return view('admin.city-banners.index', compact('banners', 'cities'));
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        $cities = City::where('is_active', true)
                     ->orderBy('name')
                     ->get(['id', 'name']);

        return view('admin.city-banners.create', compact('cities'));
    }

    /**
     * Store a newly created banner in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'link_type' => 'required|in:internal,external,none',
            'link_url' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'priority' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($validated['title']) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('banners', $filename, 'public');
            $validated['image'] = Storage::url($path);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        CityBanner::create($validated);

        return redirect()
            ->route('admin.city-banners.index')
            ->with('success', 'Banner created successfully!');
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(CityBanner $cityBanner)
    {
        $cities = City::where('is_active', true)
                     ->orderBy('name')
                     ->get(['id', 'name']);

        return view('admin.city-banners.edit', compact('cityBanner', 'cities'));
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(Request $request, CityBanner $cityBanner)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'link_type' => 'required|in:internal,external,none',
            'link_url' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'priority' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($cityBanner->image && Str::startsWith($cityBanner->image, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $cityBanner->image);
                Storage::disk('public')->delete($oldPath);
            }

            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($validated['title']) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('banners', $filename, 'public');
            $validated['image'] = Storage::url($path);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $cityBanner->update($validated);

        return redirect()
            ->route('admin.city-banners.index')
            ->with('success', 'Banner updated successfully!');
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy(CityBanner $cityBanner)
    {
        // Delete image if exists
        if ($cityBanner->image && Str::startsWith($cityBanner->image, '/storage/')) {
            $path = str_replace('/storage/', '', $cityBanner->image);
            Storage::disk('public')->delete($path);
        }

        $cityBanner->delete();

        return redirect()
            ->route('admin.city-banners.index')
            ->with('success', 'Banner deleted successfully!');
    }

    /**
     * Toggle banner active status.
     */
    public function toggleStatus(CityBanner $cityBanner)
    {
        $cityBanner->update([
            'is_active' => !$cityBanner->is_active
        ]);

        $status = $cityBanner->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Banner {$status} successfully!");
    }
}
