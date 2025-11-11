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
            'total_views' => $this->getTotalViews($user->id),
            'total_contacts' => $this->getTotalContacts($user->id),
            'monthly_views' => $this->getMonthlyViews($user->id),
            'monthly_contacts' => $this->getMonthlyContacts($user->id),
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
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'city_id' => 'required|exists:cities,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'pricing_type' => 'required|in:fixed,hourly,distance,negotiable',
            'base_price' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'distance_rate' => 'nullable|numeric|min:0',
            'minimum_charge' => 'nullable|numeric|min:0',
            'contact_phone' => 'required|string|max:20',
            'contact_whatsapp' => 'nullable|string|max:20',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = Auth::user();
        
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
            'service_category_id' => $request->service_category_id,
            'city_id' => $request->city_id,
            'title' => $request->title,
            'description' => $request->description,
            'pricing_type' => $request->pricing_type,
            'base_price' => $request->base_price,
            'hourly_rate' => $request->hourly_rate,
            'distance_rate' => $request->distance_rate,
            'minimum_charge' => $request->minimum_charge,
            'contact_phone' => $request->contact_phone,
            'contact_whatsapp' => $request->contact_whatsapp,
            'experience_years' => $request->experience_years,
            'images' => $imagePaths,
            'subscription_plan_id' => $request->subscription_plan_id,
            'subscription_expires_at' => $this->calculateSubscriptionExpiry($request->subscription_plan_id),
            'availability_schedule' => $request->availability_schedule ?? [],
            'service_area' => $request->service_area ?? [],
            'requirements' => $request->requirements ?? [],
            'vehicle_info' => $request->vehicle_info ?? [],
            'certifications' => $request->certifications ?? [],
            'status' => 'pending',
            'is_active' => true,
        ]);

        return redirect()->route('user.services.index')
            ->with('success', 'تم إرسال خدمتك للمراجعة بنجاح');
    }

    /**
     * Show service details
     */
    public function show(UserService $service)
    {
        $this->authorize('view', $service);
        
        // Record view for analytics
        $this->recordAnalytics($service->id, 'view');
        
        // Get detailed analytics
        $analytics = $this->getServiceAnalytics($service->id);
        
        return view('user.services.show', compact('service', 'analytics'));
    }

    /**
     * Show edit form
     */
    public function edit(UserService $service)
    {
        $this->authorize('update', $service);
        
        $categories = ServiceCategory::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        $subscriptionPlans = SubscriptionPlan::where('is_active', true)->get();

        return view('user.services.edit', compact('service', 'categories', 'cities', 'subscriptionPlans'));
    }

    /**
     * Update service
     */
    public function update(Request $request, UserService $service)
    {
        $this->authorize('update', $service);
        
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'city_id' => 'required|exists:cities,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'pricing_type' => 'required|in:fixed,hourly,distance,negotiable',
            'base_price' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'distance_rate' => 'nullable|numeric|min:0',
            'minimum_charge' => 'nullable|numeric|min:0',
            'contact_phone' => 'required|string|max:20',
            'contact_whatsapp' => 'nullable|string|max:20',
            'experience_years' => 'nullable|integer|min:0|max:50',
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

        $service->update([
            'service_category_id' => $request->service_category_id,
            'city_id' => $request->city_id,
            'title' => $request->title,
            'description' => $request->description,
            'pricing_type' => $request->pricing_type,
            'base_price' => $request->base_price,
            'hourly_rate' => $request->hourly_rate,
            'distance_rate' => $request->distance_rate,
            'minimum_charge' => $request->minimum_charge,
            'contact_phone' => $request->contact_phone,
            'contact_whatsapp' => $request->contact_whatsapp,
            'experience_years' => $request->experience_years,
            'images' => array_values($imagePaths),
            'availability_schedule' => $request->availability_schedule ?? $service->availability_schedule,
            'service_area' => $request->service_area ?? $service->service_area,
            'requirements' => $request->requirements ?? $service->requirements,
            'vehicle_info' => $request->vehicle_info ?? $service->vehicle_info,
            'certifications' => $request->certifications ?? $service->certifications,
        ]);

        return redirect()->route('user.services.show', $service)
            ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * Delete service
     */
    public function destroy(UserService $service)
    {
        $this->authorize('delete', $service);
        
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
        $this->authorize('update', $service);
        
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
        $this->authorize('view', $service);
        
        $analytics = $this->getServiceAnalytics($service->id);
        $chartData = $this->getChartData($service->id);
        
        return view('user.services.analytics', compact('service', 'analytics', 'chartData'));
    }

    /**
     * Record contact analytics
     */
    public function recordContact(UserService $service, Request $request)
    {
        $this->recordAnalytics($service->id, 'contact', $request->contact_type ?? 'phone');
        
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