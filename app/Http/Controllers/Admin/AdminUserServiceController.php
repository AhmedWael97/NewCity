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
     * Show the form for creating a new service
     */
    public function create()
    {
        $cities = City::orderBy('name_ar')->get();
        $categories = ServiceCategory::orderBy('name_ar')->get();
        return view('admin.user-services.create', compact('cities', 'categories'));
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // User information
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email',
            'user_phone' => 'required|string|max:20',
            'user_password' => 'required|string|min:8',
            
            // Service information
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
            'images.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        try {
            \DB::beginTransaction();

            // Create user account
            $user = \App\Models\User::create([
                'name' => $validated['user_name'],
                'email' => $validated['user_email'],
                'phone' => $validated['user_phone'],
                'password' => \Hash::make($validated['user_password']),
                'user_type' => 'regular',
                'is_verified' => true,
                'is_active' => true,
                'city_id' => $validated['city_id'],
            ]);

            // Handle image uploads
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('user-services', 'public');
                    $imagePaths[] = $path;
                }
            }

            // Create service
            $service = UserService::create([
                'user_id' => $user->id,
                'service_category_id' => $validated['service_category_id'],
                'city_id' => $validated['city_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'pricing_type' => $validated['pricing_type'],
                'price_from' => $validated['price_from'],
                'price_to' => $validated['price_to'],
                'phone' => $validated['phone'],
                'whatsapp' => $validated['whatsapp'],
                'address' => $validated['address'],
                'images' => !empty($imagePaths) ? $imagePaths : null,
                'is_active' => $request->has('is_active'),
                'is_verified' => $request->has('is_verified'),
                'is_featured' => $request->has('is_featured'),
            ]);

            \DB::commit();

            return redirect()
                ->route('admin.user-services.index')
                ->with('success', 'تم إنشاء الخدمة وحساب المستخدم بنجاح');
        } catch (\Exception $e) {
            \DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء الإنشاء: ' . $e->getMessage());
        }
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
        $service = $userService; // Alias for the view
        return view('admin.user-services.edit', compact('service', 'cities', 'categories'));
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
            'existing_images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle existing images
        $existingImages = $request->input('existing_images', []);
        
        // Handle new image uploads
        $newImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services', 'public');
                $newImages[] = $path;
            }
        }

        // Merge existing and new images
        $allImages = array_merge($existingImages, $newImages);
        
        // Delete removed images from storage
        if ($userService->images && is_array($userService->images)) {
            foreach ($userService->images as $oldImage) {
                if (!in_array($oldImage, $existingImages)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        }

        // Update service data
        $validated['images'] = $allImages;
        $validated['is_active'] = $request->has('is_active');
        $validated['is_verified'] = $request->has('is_verified');
        $validated['is_featured'] = $request->has('is_featured');

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
