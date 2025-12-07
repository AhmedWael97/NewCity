<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserService;
use App\Models\ServiceCategory;
use App\Models\City;
use App\Models\SubscriptionPlan;
use App\Models\ServiceAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserServiceController extends Controller
{
    /**
     * Display user's services
     */
    public function index()
    {
        $user = Auth::user();
        $services = UserService::where('user_id', $user->id)
            ->with(['serviceCategory', 'city', 'subscriptionPlan'])
            ->latest()
            ->paginate(10);

        // Get analytics summary
        $analytics = [
            'total_services' => $services->total(),
            'active_services' => UserService::where('user_id', $user->id)->active()->count(),
            'total_views' => 0,
            'total_contacts' => 0,
            'monthly_views' => 0,
            'monthly_contacts' => 0,
        ];

        return view('user.services.index', compact('services', 'analytics'));
    }

    /**
     * Show form to create new service
     */
    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        $subscriptionPlans = SubscriptionPlan::where('is_active', true)->get();

        return view('user.services.create', compact('categories', 'cities', 'subscriptionPlans'));
    }

    /**
     * Store new service
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has a city assigned
        if (!$user->city_id) {
            return back()->withErrors(['city_id' => 'يجب تحديد مدينتك في الملف الشخصي أولاً'])->withInput();
        }
        
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'pricing_type' => 'required|in:fixed,hourly,per_km,negotiable',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'requirements' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('user-services', 'public');
                $imagePaths[] = $path;
            }
        }

        // Prepare availability JSON
        $availability = null;
        if ($request->has('availability_days')) {
            $availability = [
                'days' => $request->availability_days ?? [],
                'hours_from' => $request->hours_from ?? '09:00',
                'hours_to' => $request->hours_to ?? '17:00',
            ];
        }

        // Prepare service areas JSON
        $serviceAreas = null;
        if ($request->has('service_areas') && !empty($request->service_areas)) {
            $serviceAreas = array_filter($request->service_areas);
        }

        // Create service - always use user's city
        $service = UserService::create([
            'user_id' => $user->id,
            'service_category_id' => $request->service_category_id,
            'city_id' => $user->city_id, // Force user's city
            'title' => $request->title,
            'description' => $request->description,
            'pricing_type' => $request->pricing_type,
            'price_from' => $request->price_from,
            'price_to' => $request->price_to,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'service_areas' => $serviceAreas,
            'requirements' => $request->requirements,
            'images' => $imagePaths,
            'availability' => $availability,
            'is_active' => true,
            'is_verified' => false,
        ]);

        return redirect()->route('user.services.index')
            ->with('success', 'تم إضافة خدمتك بنجاح');
    }

    /**
     * Display service details (public view)
     */
    public function show(UserService $service)
    {
        // Load relationships
        $service->load(['user', 'city', 'serviceCategory']);
        
        // Check if user is the owner (for showing owner-specific actions)
        $isOwner = Auth::check() && $service->user_id === Auth::id();
        
        // Track view only if viewer is not the owner
        if (!$isOwner) {
            $service->increment('total_views');
            
            // Track analytics if system exists
            try {
                ServiceAnalytics::create([
                    'user_service_id' => $service->id,
                    'type' => 'view',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'viewed_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Silent fail if analytics table doesn't exist
            }
        }
        
        // Get analytics only if user is the owner
        $analytics = null;
        if ($isOwner) {
            $analytics = [
                'total_views' => $service->total_views ?? 0,
                'total_contacts' => $service->total_contacts ?? 0,
                'this_month_views' => 0,
                'this_month_contacts' => 0,
            ];
        }
        
        // Get related services (same category, different service)
        $relatedServices = UserService::where('service_category_id', $service->service_category_id)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();
        
        return view('user.services.show', compact('service', 'analytics', 'isOwner', 'relatedServices'));
    }

    /**
     * Show edit form
     */
    public function edit(UserService $service)
    {
        // Check if user owns this service
        if ($service->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذه الخدمة');
        }
        
        $categories = ServiceCategory::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();

        return view('user.services.edit', compact('service', 'categories', 'cities'));
    }

    /**
     * Update service
     */
    public function update(Request $request, UserService $service)
    {
        // Check if user owns this service
        if ($service->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذه الخدمة');
        }
        
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'pricing_type' => 'required|in:fixed,hourly,per_km,negotiable',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'requirements' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image uploads
        $imagePaths = $service->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('user-services', 'public');
                $imagePaths[] = $path;
            }
        }

        // Remove deleted images
        if ($request->has('deleted_images')) {
            $deletedImages = json_decode($request->deleted_images, true) ?? [];
            foreach ($deletedImages as $deletedImage) {
                Storage::disk('public')->delete($deletedImage);
                $imagePaths = array_filter($imagePaths, function($path) use ($deletedImage) {
                    return $path !== $deletedImage;
                });
            }
        }

        // Prepare availability JSON
        $availability = null;
        if ($request->has('availability_days')) {
            $availability = [
                'days' => $request->availability_days ?? [],
                'hours_from' => $request->hours_from ?? '09:00',
                'hours_to' => $request->hours_to ?? '17:00',
            ];
        }

        // Prepare service areas JSON
        $serviceAreas = null;
        if ($request->has('service_areas') && !empty($request->service_areas)) {
            $serviceAreas = array_filter($request->service_areas);
        }

        $service->update([
            'service_category_id' => $request->service_category_id,
            // city_id is not updated - services are always in user's city
            'title' => $request->title,
            'description' => $request->description,
            'pricing_type' => $request->pricing_type,
            'price_from' => $request->price_from,
            'price_to' => $request->price_to,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'requirements' => $request->requirements,
            'images' => array_values($imagePaths),
            'availability' => $availability,
            'service_areas' => $serviceAreas,
        ]);

        return redirect()->route('user.services.show', $service)
            ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * Delete service
     */
    public function destroy(UserService $service)
    {
        // Check if user owns this service
        if ($service->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذه الخدمة');
        }
        
        // Delete images
        if ($service->images) {
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $service->delete();

        return redirect()->route('user.services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }

    /**
     * Toggle service status
     */
    public function toggleStatus(UserService $service)
    {
        // Check if user owns this service
        if ($service->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذه الخدمة');
        }
        
        $service->update([
            'is_active' => !$service->is_active
        ]);

        $status = $service->is_active ? 'مفعلة' : 'معطلة';
        return back()->with('success', "تم تغيير حالة الخدمة إلى {$status}");
    }

    /**
     * Get service analytics
     */
    public function analytics(UserService $service)
    {
        // Check if user owns this service
        if ($service->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بعرض تحليلات هذه الخدمة');
        }
        
        $analytics = [
            'total_views' => 0,
            'total_contacts' => 0,
            'this_month_views' => 0,
            'this_month_contacts' => 0,
        ];
        
        $chartData = [
            'labels' => [],
            'views' => [],
            'contacts' => [],
        ];
        
        $daily_data = [];
        
        return view('user.services.analytics', compact('service', 'analytics', 'chartData', 'daily_data'));
    }

    /**
     * Record contact analytics
     */
    public function recordContact(UserService $service, Request $request)
    {
        // Analytics recording disabled until service_analytics table is created
        return response()->json(['success' => true]);
    }

    /**
     * Private helper methods
     */
    private function getTotalViews($userId)
    {
        return ServiceAnalytics::whereHas('userService', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('metric_type', 'view')->sum('value');
    }

    private function getTotalContacts($userId)
    {
        return ServiceAnalytics::whereHas('userService', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('metric_type', 'contact')->sum('value');
    }

    private function getMonthlyViews($userId)
    {
        return ServiceAnalytics::whereHas('userService', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('metric_type', 'view')
          ->where('date', '>=', now()->startOfMonth())
          ->sum('value');
    }

    private function getMonthlyContacts($userId)
    {
        return ServiceAnalytics::whereHas('userService', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('metric_type', 'contact')
          ->where('date', '>=', now()->startOfMonth())
          ->sum('value');
    }

    private function getServiceAnalytics($serviceId)
    {
        $analytics = ServiceAnalytics::where('user_service_id', $serviceId)
            ->selectRaw('metric_type, SUM(value) as total, DATE(date) as date')
            ->groupBy('metric_type', 'date')
            ->orderBy('date', 'desc')
            ->get();

        return [
            'total_views' => $analytics->where('metric_type', 'view')->sum('total'),
            'total_contacts' => $analytics->where('metric_type', 'contact')->sum('total'),
            'this_month_views' => $analytics->where('metric_type', 'view')
                ->where('date', '>=', now()->startOfMonth()->toDateString())
                ->sum('total'),
            'this_month_contacts' => $analytics->where('metric_type', 'contact')
                ->where('date', '>=', now()->startOfMonth()->toDateString())
                ->sum('total'),
            'daily_data' => $analytics->groupBy('date'),
        ];
    }

    private function getChartData($serviceId)
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        $data = ServiceAnalytics::where('user_service_id', $serviceId)
            ->where('date', '>=', $thirtyDaysAgo)
            ->selectRaw('DATE(date) as date, metric_type, SUM(value) as total')
            ->groupBy('date', 'metric_type')
            ->orderBy('date')
            ->get();

        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartData[$date] = [
                'views' => $data->where('date', $date)->where('metric_type', 'view')->sum('total'),
                'contacts' => $data->where('date', $date)->where('metric_type', 'contact')->sum('total'),
            ];
        }

        return $chartData;
    }

    private function recordAnalytics($serviceId, $metricType, $metricValue = null)
    {
        ServiceAnalytics::create([
            'user_service_id' => $serviceId,
            'metric_type' => $metricType,
            'metric_value' => $metricValue,
            'value' => 1,
            'date' => now()->toDateString(),
            'hour' => now()->hour,
            'user_agent' => request()->header('User-Agent'),
            'ip_address' => request()->ip(),
        ]);
    }

    private function calculateSubscriptionExpiry($planId)
    {
        $plan = SubscriptionPlan::find($planId);
        
        if (!$plan) {
            return now()->addMonth(); // Default 1 month
        }

        switch ($plan->duration_type) {
            case 'monthly':
                return now()->addMonths($plan->duration_value);
            case 'yearly':
                return now()->addYears($plan->duration_value);
            case 'weekly':
                return now()->addWeeks($plan->duration_value);
            default:
                return now()->addDays($plan->duration_value);
        }
    }
}