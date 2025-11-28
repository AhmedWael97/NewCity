<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserService;
use App\Models\City;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminUserServiceController extends Controller
{
    /**
     * Display a listing of user services
     */
    public function index(Request $request)
    {
        $query = UserService::with(['user', 'city', 'serviceCategory']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by category
        if ($request->filled('service_category_id')) {
            $query->where('service_category_id', $request->service_category_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by verification
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        // Filter by featured
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        $services = $query->latest()->paginate(20);
        $cities = City::orderBy('name')->get();
        $categories = ServiceCategory::orderBy('name')->get();

        return view('admin.user-services.index', compact('services', 'cities', 'categories'));
    }

    /**
     * Display the specified service
     */
    public function show(UserService $userService)
    {
        $userService->load(['user', 'city', 'serviceCategory']);
        return view('admin.user-services.show', compact('userService'));
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit(UserService $userService)
    {
        $cities = City::orderBy('name')->get();
        $categories = ServiceCategory::orderBy('name')->get();
        return view('admin.user-services.edit', compact('userService', 'cities', 'categories'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, UserService $userService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'service_category_id' => 'required|exists:service_categories,id',
            'city_id' => 'required|exists:cities,id',
            'pricing_type' => 'required|in:fixed,hourly,per_km,negotiable',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $userService->update($validated);

        return redirect()
            ->route('admin.user-services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * Remove the specified service
     */
    public function destroy(UserService $userService)
    {
        try {
            // Delete images
            if ($userService->images && is_array($userService->images)) {
                foreach ($userService->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $userService->delete();

            return redirect()
                ->route('admin.user-services.index')
                ->with('success', 'تم حذف الخدمة بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.user-services.index')
                ->with('error', 'حدث خطأ أثناء حذف الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * Verify a service
     */
    public function verify(UserService $userService)
    {
        $userService->update([
            'is_verified' => !$userService->is_verified,
            'verified_at' => !$userService->is_verified ? now() : null,
        ]);

        $status = $userService->is_verified ? 'تم التحقق من' : 'تم إلغاء التحقق من';
        return back()->with('success', "{$status} الخدمة بنجاح");
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(UserService $userService)
    {
        $userService->update([
            'is_featured' => !$userService->is_featured,
            'featured_until' => !$userService->is_featured ? now()->addDays(30) : null,
        ]);

        $status = $userService->is_featured ? 'تم إبراز' : 'تم إلغاء إبراز';
        return back()->with('success', "{$status} الخدمة بنجاح");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:verify,unverify,activate,deactivate,feature,unfeature,delete',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:user_services,id',
        ]);

        $services = UserService::whereIn('id', $request->services);

        switch ($request->action) {
            case 'verify':
                $services->update(['is_verified' => true, 'verified_at' => now()]);
                $message = 'تم التحقق من الخدمات المحددة';
                break;
            case 'unverify':
                $services->update(['is_verified' => false, 'verified_at' => null]);
                $message = 'تم إلغاء التحقق من الخدمات المحددة';
                break;
            case 'activate':
                $services->update(['is_active' => true]);
                $message = 'تم تفعيل الخدمات المحددة';
                break;
            case 'deactivate':
                $services->update(['is_active' => false]);
                $message = 'تم إلغاء تفعيل الخدمات المحددة';
                break;
            case 'feature':
                $services->update(['is_featured' => true, 'featured_until' => now()->addDays(30)]);
                $message = 'تم إبراز الخدمات المحددة';
                break;
            case 'unfeature':
                $services->update(['is_featured' => false, 'featured_until' => null]);
                $message = 'تم إلغاء إبراز الخدمات المحددة';
                break;
            case 'delete':
                foreach ($services->get() as $service) {
                    if ($service->images && is_array($service->images)) {
                        foreach ($service->images as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }
                $services->delete();
                $message = 'تم حذف الخدمات المحددة';
                break;
        }

        return back()->with('success', $message);
    }
}
