<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCityController extends Controller
{

    /**
     * Display a listing of cities.
     */
    public function index(Request $request)
    {
        $this->authorize('view-cities');
        
        $query = City::withCount(['shops', 'users']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $cities = $query->paginate(15)->withQueryString();

        // Get unique countries for filter dropdown
        $countries = City::whereNotNull('country')
                        ->distinct()
                        ->pluck('country')
                        ->filter()
                        ->sort()
                        ->values();

        // Get unique states for filter dropdown
        $states = City::whereNotNull('state')
                     ->distinct()
                     ->pluck('state')
                     ->filter()
                     ->sort()
                     ->values();

        return view('admin.cities.index', compact('cities', 'countries', 'states'));
    }

    /**
     * Show the form for creating a new city.
     */
    public function create()
    {
        $this->authorize('create-cities');
        
        return view('admin.cities.create');
    }

    /**
     * Store a newly created city in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create-cities');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cities,slug',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            // Contact Information
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string',
            'contact_whatsapp' => 'nullable|string|max:20',
            // SEO Settings
            'meta_title' => 'nullable|string|max:255',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_description_ar' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_keywords_ar' => 'nullable|string|max:255',
            // Branding
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico|max:1024',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Social Media
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            // Analytics
            'google_analytics_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',
        ]);

        try {
            $city = new City();
            $city->fill($validated);
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('cities', 'public');
                $city->image = $imagePath;
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('cities/logos', 'public');
                $city->logo = $logoPath;
            }

            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->store('cities/favicons', 'public');
                $city->favicon = $faviconPath;
            }

            // Handle OG image upload
            if ($request->hasFile('og_image')) {
                $ogImagePath = $request->file('og_image')->store('cities/og-images', 'public');
                $city->og_image = $ogImagePath;
            }

            $city->save();

            return redirect()
                ->route('admin.cities.index')
                ->with('success', 'تم إنشاء المدينة بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ المدينة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified city.
     */
    public function show(City $city)
    {
        $this->authorize('view-cities');
        
        $city->load(['shops.category', 'users']);
        
        // Get city statistics
        $stats = [
            'total_shops' => $city->shops()->count(),
            'active_shops' => $city->shops()->where('status', 'active')->count(),
            'pending_shops' => $city->shops()->where('status', 'pending')->count(),
            'verified_shops' => $city->shops()->where('is_verified', true)->count(),
            'total_users' => $city->users()->count(),
            'average_rating' => $city->shops()->withAvg('ratings', 'rating')->get()->avg('ratings_avg_rating') ?? 0,
        ];
        
        return view('admin.cities.show', compact('city', 'stats'));
    }

    /**
     * Show the form for editing the specified city.
     */
    public function edit(City $city)
    {
        $this->authorize('edit-cities');
        
        return view('admin.cities.edit', compact('city'));
    }

    /**
     * Update the specified city in storage.
     */
    public function update(Request $request, City $city)
    {
        $this->authorize('edit-cities');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cities,slug,' . $city->id,
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            // Contact Information
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string',
            'contact_whatsapp' => 'nullable|string|max:20',
            // SEO Settings
            'meta_title' => 'nullable|string|max:255',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_description_ar' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_keywords_ar' => 'nullable|string|max:255',
            // Branding
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico|max:1024',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Social Media
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            // Analytics
            'google_analytics_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',
        ]);

        try {
            $city->fill($validated);
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($city->image) {
                    Storage::disk('public')->delete($city->image);
                }
                
                $imagePath = $request->file('image')->store('cities', 'public');
                $city->image = $imagePath;
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                if ($city->logo) {
                    Storage::disk('public')->delete($city->logo);
                }
                $logoPath = $request->file('logo')->store('cities/logos', 'public');
                $city->logo = $logoPath;
            }

            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                if ($city->favicon) {
                    Storage::disk('public')->delete($city->favicon);
                }
                $faviconPath = $request->file('favicon')->store('cities/favicons', 'public');
                $city->favicon = $faviconPath;
            }

            // Handle OG image upload
            if ($request->hasFile('og_image')) {
                if ($city->og_image) {
                    Storage::disk('public')->delete($city->og_image);
                }
                $ogImagePath = $request->file('og_image')->store('cities/og-images', 'public');
                $city->og_image = $ogImagePath;
            }

            $city->save();

            return redirect()
                ->route('admin.cities.index')
                ->with('success', 'تم تحديث المدينة بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المدينة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified city from storage.
     */
    public function destroy(City $city)
    {
        $this->authorize('delete-cities');
        
        // Check if city has shops
        if ($city->shops()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'لا يمكن حذف المدينة لأنها تحتوي على متاجر');
        }

        // Check if city has users
        if ($city->users()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'لا يمكن حذف المدينة لأنها تحتوي على مستخدمين');
        }

        // Delete associated image
        if ($city->image) {
            Storage::disk('public')->delete($city->image);
        }

        $city->delete();

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'تم حذف المدينة بنجاح');
    }

    /**
     * Toggle city status (active/inactive).
     */
    public function toggleStatus(City $city)
    {
        $this->authorize('edit-cities');
        
        $newStatus = $city->status === 'active' ? 'inactive' : 'active';
        
        $city->update([
            'status' => $newStatus
        ]);

        $statusText = $newStatus === 'active' ? 'تم تفعيل المدينة' : 'تم إلغاء تفعيل المدينة';

        return redirect()
            ->back()
            ->with('success', $statusText);
    }

    /**
     * Toggle city featured status.
     */
    public function toggleFeatured(City $city)
    {
        $city->update([
            'is_featured' => !$city->is_featured
        ]);

        $status = $city->is_featured ? 'تم إضافة المدينة للمميزة' : 'تم إزالة المدينة من المميزة';

        return redirect()
            ->back()
            ->with('success', $status);
    }

    /**
     * Bulk actions for cities.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'cities' => 'required|array',
            'cities.*' => 'exists:cities,id',
            'action' => 'required|in:delete,activate,deactivate,feature,unfeature'
        ]);

        $cities = City::whereIn('id', $request->cities);
        $count = $cities->count();

        switch ($request->action) {
            case 'delete':
                // Check if any city has shops or users
                foreach ($cities->get() as $city) {
                    if ($city->shops()->count() > 0 || $city->users()->count() > 0) {
                        return redirect()
                            ->back()
                            ->with('error', 'لا يمكن حذف بعض المدن لأنها تحتوي على متاجر أو مستخدمين');
                    }
                }
                
                // Delete associated images
                foreach ($cities->get() as $city) {
                    if ($city->image) {
                        Storage::disk('public')->delete($city->image);
                    }
                }
                $cities->delete();
                $message = "تم حذف {$count} مدينة";
                break;
                
            case 'activate':
                $cities->update(['status' => 'active']);
                $message = "تم تفعيل {$count} مدينة";
                break;
                
            case 'deactivate':
                $cities->update(['status' => 'inactive']);
                $message = "تم إلغاء تفعيل {$count} مدينة";
                break;
                
            case 'feature':
                $cities->update(['is_featured' => true]);
                $message = "تم إضافة {$count} مدينة للمميزة";
                break;
                
            case 'unfeature':
                $cities->update(['is_featured' => false]);
                $message = "تم إزالة {$count} مدينة من المميزة";
                break;
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Toggle city active status.
     */
    public function toggleActive(City $city)
    {
        $city->is_active = !$city->is_active;
        $city->save();

        $status = $city->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';
        
        return redirect()
            ->back()
            ->with('success', "{$status} المدينة: {$city->name}");
    }

    /**
     * Get cities statistics for dashboard.
     */
    public function getStats()
    {
        $stats = [
            'total' => City::count(),
            'active' => City::where('status', 'active')->count(),
            'inactive' => City::where('status', 'inactive')->count(),
            'featured' => City::where('is_featured', true)->count(),
            'with_shops' => City::has('shops')->count(),
            'without_shops' => City::doesntHave('shops')->count(),
        ];

        return response()->json($stats);
    }
}