<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\City;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminShopController extends Controller
{
    /**
     * Display a listing of shops.
     */
    public function index(Request $request)
    {
        $query = Shop::with(['city', 'category', 'owner']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($ownerQuery) use ($search) {
                      $ownerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by verification
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $shops = $query->paginate(15)->withQueryString();

        // Get filter options
        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.shops.index', compact('shops', 'cities', 'categories'));
    }

    /**
     * Display pending shops for approval.
     */
    public function pending(Request $request)
    {
        $query = Shop::with(['city', 'category', 'owner'])
                    ->where('status', Shop::STATUS_PENDING);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($ownerQuery) use ($search) {
                      $ownerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $shops = $query->orderBy('created_at', 'desc')
                      ->paginate(15)
                      ->withQueryString();

        return view('admin.shops.pending', compact('shops'));
    }

    /**
     * Alias for pending method for route compatibility
     */
    public function pendingReview(Request $request)
    {
        return $this->pending($request);
    }

    /**
     * Show the form for creating a new shop.
     */
    public function create(Request $request)
    {
        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        
        // Pre-fill data from shop suggestion if provided
        $suggestion = null;
        if ($request->filled('suggestion_id')) {
            $suggestion = \App\Models\ShopSuggestion::find($request->suggestion_id);
        }
        
        return view('admin.shops.create', compact('cities', 'categories', 'users', 'suggestion'));
    }

    /**
     * Store a newly created shop in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'opening_hours' => 'nullable|json',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:pending,approved,rejected,suspended',
            'is_verified' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'suggestion_id' => 'nullable|exists:shop_suggestions,id',
        ]);

        // Handle images upload
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('shops', 'public');
            }
        } else {
            // Generate default images if none uploaded
            $category = Category::find($request->category_id);
            $imageGenerator = new \App\Services\ShopImageGenerator();
            $imagePaths = $imageGenerator->generateMultipleImages(
                $request->name,
                $category->name ?? 'Shop',
                $category->icon ?? null,
                3
            );
        }

        $shop = new Shop();
        $shop->fill($request->except(['images', 'suggestion_id']));
        $shop->images = $imagePaths;
        $shop->save();

        // If created from a suggestion, mark it as completed
        if ($request->filled('suggestion_id')) {
            $suggestion = \App\Models\ShopSuggestion::find($request->suggestion_id);
            if ($suggestion) {
                $suggestion->update([
                    'status' => 'completed',
                    'reviewed_by' => auth()->guard('admin')->id(),
                    'reviewed_at' => now(),
                    'admin_notes' => ($suggestion->admin_notes ? $suggestion->admin_notes . "\n\n" : '') . 
                                   "تم إنشاء المتجر بنجاح (ID: {$shop->id})"
                ]);
            }
        }

        return redirect()
            ->route('admin.shops.index')
            ->with('success', 'تم إنشاء المتجر بنجاح');
    }

    /**
     * Display the specified shop.
     */
    public function show(Shop $shop)
    {
        $shop->load(['city', 'category', 'owner', 'ratings.user']);
        
        return view('admin.shops.show', compact('shop'));
    }

    /**
     * Show the form for editing the specified shop.
     */
    public function edit(Shop $shop)
    {
        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        
        return view('admin.shops.edit', compact('shop', 'cities', 'categories', 'users'));
    }

    /**
     * Update the specified shop in storage.
     */
    public function update(Request $request, Shop $shop)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'opening_hours' => 'nullable|json',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer',
            'status' => 'nullable|in:pending,approved,rejected,suspended',
            'is_verified' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image deletion
        $imagePaths = $shop->images ?? [];
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $index) {
                if (isset($imagePaths[$index])) {
                    Storage::disk('public')->delete($imagePaths[$index]);
                    unset($imagePaths[$index]);
                }
            }
            $imagePaths = array_values($imagePaths); // Reindex array
        }

        // Handle new images upload (append to existing)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('shops', 'public');
            }
        }

        $shop->fill($request->except(['images', 'delete_images']));
        $shop->images = $imagePaths;
        $shop->save();

        return redirect()
            ->route('admin.shops.index')
            ->with('success', 'تم تحديث المتجر بنجاح');
    }

    /**
     * Remove the specified shop from storage.
     */
    public function destroy(Shop $shop)
    {
        // Delete associated images
        if ($shop->image) {
            Storage::disk('public')->delete($shop->image);
        }
        
        if ($shop->gallery) {
            $gallery = json_decode($shop->gallery, true);
            foreach ($gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $shop->delete();

        return redirect()
            ->route('admin.shops.index')
            ->with('success', 'تم حذف المتجر بنجاح');
    }

    /**
     * Approve a pending shop.
     */
    public function approve(Shop $shop)
    {
        $shop->update([
            'status' => 'active',
            'is_verified' => true
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم الموافقة على المتجر وتفعيله بنجاح');
    }

    /**
     * Reject a pending shop.
     */
    public function reject(Request $request, Shop $shop)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $shop->update([
            'status' => Shop::STATUS_REJECTED,
            'is_active' => false,
            'is_verified' => false,
            'verification_notes' => $request->rejection_reason
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم رفض المتجر');
    }

    /**
     * Toggle shop verification status.
     */
    public function toggleVerification(Shop $shop)
    {
        $shop->update([
            'is_verified' => !$shop->is_verified
        ]);

        $status = $shop->is_verified ? 'تم التحقق من المتجر' : 'تم إلغاء التحقق من المتجر';

        return redirect()
            ->back()
            ->with('success', $status);
    }

    /**
     * Toggle shop featured status.
     */
    public function toggleFeatured(Shop $shop)
    {
        $isFeatured = !$shop->is_featured;
        
        $shop->update([
            'is_featured' => $isFeatured,
            'featured_until' => $isFeatured ? now()->addDays(30) : null,
            'featured_priority' => $isFeatured ? 10 : 0
        ]);

        $status = $shop->is_featured ? 'تم إضافة المتجر للمميزة' : 'تم إزالة المتجر من المميزة';

        return redirect()
            ->back()
            ->with('success', $status);
    }

    /**
     * Toggle shop status (active/inactive).
     */
    public function toggleStatus(Shop $shop)
    {
        $newStatus = $shop->status === 'active' ? 'inactive' : 'active';
        
        $shop->update([
            'status' => $newStatus
        ]);

        $statusText = $newStatus === 'active' ? 'تم تفعيل المتجر' : 'تم إلغاء تفعيل المتجر';

        return redirect()
            ->back()
            ->with('success', $statusText);
    }

    /**
     * Bulk actions for shops.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'shop_ids' => 'required|array',
            'shop_ids.*' => 'exists:shops,id',
            'action' => 'required|in:approve,reject,delete,verify,unverify,feature,unfeature,activate,deactivate'
        ]);

        $shopIds = $request->shop_ids ?? $request->shops ?? [];
        $shops = Shop::whereIn('id', $shopIds);
        $count = $shops->count();

        switch ($request->action) {
            case 'approve':
                $shops->update([
                    'status' => Shop::STATUS_APPROVED,
                    'is_verified' => true,
                    'is_active' => true,
                    'verified_at' => now()
                ]);
                $message = "تم الموافقة على {$count} متجر";
                break;
                
            case 'reject':
                $shops->update(['status' => 'inactive']);
                $message = "تم رفض {$count} متجر";
                break;
                
            case 'delete':
                // Delete associated images for each shop
                foreach ($shops->get() as $shop) {
                    if ($shop->image) {
                        Storage::disk('public')->delete($shop->image);
                    }
                    if ($shop->gallery) {
                        $gallery = json_decode($shop->gallery, true);
                        foreach ($gallery as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }
                $shops->delete();
                $message = "تم حذف {$count} متجر";
                break;
                
            case 'verify':
                $shops->update(['is_verified' => true]);
                $message = "تم التحقق من {$count} متجر";
                break;
                
            case 'unverify':
                $shops->update(['is_verified' => false]);
                $message = "تم إلغاء التحقق من {$count} متجر";
                break;
                
            case 'feature':
                $shops->update(['is_featured' => true]);
                $message = "تم إضافة {$count} متجر للمميزة";
                break;
                
            case 'unfeature':
                $shops->update(['is_featured' => false]);
                $message = "تم إزالة {$count} متجر من المميزة";
                break;
                
            case 'activate':
                $shops->update(['status' => 'active']);
                $message = "تم تفعيل {$count} متجر";
                break;
                
            case 'deactivate':
                $shops->update(['status' => 'inactive']);
                $message = "تم إلغاء تفعيل {$count} متجر";
                break;
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Show form for setting featured details
     */
    public function editFeatured(Shop $shop)
    {
        return view('admin.shops.featured', compact('shop'));
    }

    /**
     * Update featured details
     */
    public function updateFeatured(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'is_featured' => 'boolean',
            'featured_priority' => 'nullable|integer|min:0|max:100',
            'featured_until' => 'nullable|date|after:today',
        ]);

        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        $shop->update($validated);

        return redirect()
            ->route('admin.shops.index')
            ->with('success', 'تم تحديث إعدادات المتجر المميز بنجاح!');
    }

    /**
     * Verify a shop (set is_verified to true and approve if pending)
     */
    public function verify(Shop $shop)
    {
        $shop->update([
            'is_verified' => true,
            'is_active' => true,
            'status' => Shop::STATUS_APPROVED,
            'verified_at' => now()
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم التحقق من المتجر وتفعيله بنجاح!');
    }

    /**
     * Display shops on Google Maps
     */
    public function mapView(Request $request)
    {
        // Get cities and categories for dropdowns
        $cities = City::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.shops.map', compact('cities', 'categories'));
    }

    /**
     * Import shop from Google Places
     */
    public function importFromGoogle(Request $request)
    {
        $validated = $request->validate([
            'place_id' => 'required|string',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'google_types' => 'nullable|array',
        ]);

        // Check if shop already exists with this place_id
        $existingShop = Shop::where('google_place_id', $validated['place_id'])->first();
        
        if ($existingShop) {
            return response()->json([
                'success' => false,
                'message' => 'هذا المتجر موجود بالفعل في النظام',
                'shop_id' => $existingShop->id
            ], 409);
        }

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $counter = 1;
        while (Shop::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['name']) . '-' . $counter;
            $counter++;
        }

        // Create shop
        $shop = Shop::create([
            'user_id' => $validated['user_id'],
            'city_id' => $validated['city_id'],
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => 'تم استيراد هذا المتجر من Google Maps',
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'phone' => $validated['phone'],
            'website' => $validated['website'],
            'rating' => $validated['rating'] ?? 0,
            'review_count' => $validated['review_count'] ?? 0,
            'google_place_id' => $validated['place_id'],
            'google_types' => $validated['google_types'] ?? [],
            'is_verified' => false,
            'is_active' => false,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المتجر بنجاح',
            'shop' => $shop
        ]);
    }
}