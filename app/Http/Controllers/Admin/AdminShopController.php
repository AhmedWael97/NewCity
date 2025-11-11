<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\City;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                    ->where('status', 'pending');

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
    public function create()
    {
        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        
        return view('admin.shops.create', compact('cities', 'categories', 'users'));
    }

    /**
     * Store a newly created shop in storage.
     */
    public function store(Request $request)
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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:pending,approved,rejected,suspended',
            'is_verified' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $shop = new Shop();
        $shop->fill($request->except(['images']));
        
        // Handle images upload
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('shops', 'public');
            }
            $shop->images = $imagePaths;
        }

        $shop->save();

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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:pending,approved,rejected,suspended',
            'is_verified' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $shop->fill($request->except(['images']));
        
        // Handle images upload
        if ($request->hasFile('images')) {
            // Delete old images
            if ($shop->images && is_array($shop->images)) {
                foreach ($shop->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            
            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('shops', 'public');
            }
            $shop->images = $imagePaths;
        }

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
    public function reject(Shop $shop)
    {
        $shop->update([
            'status' => 'inactive'
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
            'shops' => 'required|array',
            'shops.*' => 'exists:shops,id',
            'action' => 'required|in:approve,reject,delete,verify,unverify,feature,unfeature,activate,deactivate'
        ]);

        $shops = Shop::whereIn('id', $request->shops);
        $count = $shops->count();

        switch ($request->action) {
            case 'approve':
                $shops->update(['status' => 'active', 'is_verified' => true]);
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
            'status' => $shop->status === 'pending' ? 'active' : $shop->status
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم التحقق من المتجر وتفعيله بنجاح!');
    }
}