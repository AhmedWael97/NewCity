<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\ShopSubscription;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscription plans
     */
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->get();
        
        $stats = [
            'total_plans' => SubscriptionPlan::count(),
            'active_subscriptions' => ShopSubscription::where('status', 'active')->count(),
            'monthly_revenue' => ShopSubscription::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('amount_paid'),
            'total_revenue' => ShopSubscription::where('status', 'active')->sum('amount_paid')
        ];

        return view('admin.subscription-plans.index', compact('plans', 'stats'));
    }

    /**
     * Show the form for creating a new subscription plan
     */
    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    /**
     * Store a newly created subscription plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:subscription_plans,slug',
            'description' => 'required|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'max_shops' => 'required|integer|min:1',
            'max_products_per_shop' => 'required|integer|min:1',
            'max_services_per_shop' => 'required|integer|min:1',
            'max_images_per_shop' => 'required|integer|min:1',
            'analytics_access' => 'boolean',
            'priority_listing' => 'boolean',
            'verified_badge' => 'boolean',
            'custom_branding' => 'boolean',
            'social_media_integration' => 'boolean',
            'email_marketing' => 'boolean',
            'advanced_seo' => 'boolean',
            'customer_support' => 'boolean',
            'features' => 'required|array',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'integer|min:1'
        ]);

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'تم إنشاء خطة الاشتراك بنجاح');
    }

    /**
     * Display the specified subscription plan
     */
    public function show(SubscriptionPlan $subscription)
    {
        $subscriptions = $subscription->subscriptions()
            ->with(['shop', 'shop.user'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_subscribers' => $subscription->subscriptions()->where('status', 'active')->count(),
            'monthly_revenue' => $subscription->subscriptions()
                ->where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('amount_paid'),
            'total_revenue' => $subscription->subscriptions()
                ->where('status', 'active')
                ->sum('amount_paid'),
            'churn_rate' => $this->calculateChurnRate($subscription)
        ];

        return view('admin.subscription-plans.show', compact('subscription', 'subscriptions', 'stats'));
    }

    /**
     * Show the form for editing the specified subscription plan
     */
    public function edit(SubscriptionPlan $subscription)
    {
        return view('admin.subscription-plans.edit', compact('subscription'));
    }

    /**
     * Update the specified subscription plan
     */
    public function update(Request $request, SubscriptionPlan $subscription)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:subscription_plans,slug,' . $subscription->id,
            'description' => 'required|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'max_shops' => 'required|integer|min:1',
            'max_products_per_shop' => 'required|integer|min:1',
            'max_services_per_shop' => 'required|integer|min:1',
            'max_images_per_shop' => 'required|integer|min:1',
            'analytics_access' => 'boolean',
            'priority_listing' => 'boolean',
            'verified_badge' => 'boolean',
            'custom_branding' => 'boolean',
            'social_media_integration' => 'boolean',
            'email_marketing' => 'boolean',
            'advanced_seo' => 'boolean',
            'customer_support' => 'boolean',
            'features' => 'required|array',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'integer|min:1'
        ]);

        $subscription->update($validated);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم تحديث خطة الاشتراك بنجاح');
    }

    /**
     * Remove the specified subscription plan
     */
    public function destroy(SubscriptionPlan $subscription)
    {
        // Check if plan has active subscriptions
        if ($subscription->subscriptions()->where('status', 'active')->exists()) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'لا يمكن حذف خطة لديها اشتراكات نشطة');
        }

        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم حذف خطة الاشتراك بنجاح');
    }

    /**
     * Display active subscriptions
     */
    public function subscriptions()
    {
        $subscriptions = ShopSubscription::with(['shop', 'plan', 'shop.user'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_active' => ShopSubscription::where('status', 'active')->count(),
            'total_expired' => ShopSubscription::where('status', 'expired')->count(),
            'total_cancelled' => ShopSubscription::where('status', 'cancelled')->count(),
            'monthly_revenue' => ShopSubscription::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('amount_paid')
        ];

        return view('admin.subscriptions.subscriptions', compact('subscriptions', 'stats'));
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(ShopSubscription $subscription)
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return redirect()->back()->with('success', 'تم إلغاء الاشتراك بنجاح');
    }

    /**
     * Renew a subscription
     */
    public function renewSubscription(ShopSubscription $subscription)
    {
        $subscription->update([
            'status' => 'active',
            'expires_at' => now()->addMonth(),
            'cancelled_at' => null
        ]);

        return redirect()->back()->with('success', 'تم تجديد الاشتراك بنجاح');
    }

    /**
     * Calculate churn rate for a subscription plan
     */
    private function calculateChurnRate(SubscriptionPlan $plan)
    {
        $totalSubscriptions = $plan->subscriptions()->count();
        $cancelledSubscriptions = $plan->subscriptions()->where('status', 'cancelled')->count();

        if ($totalSubscriptions == 0) {
            return 0;
        }

        return round(($cancelledSubscriptions / $totalSubscriptions) * 100, 2);
    }

    /**
     * Analytics view for subscriptions
     */
    public function analytics()
    {
        // Revenue chart data (last 30 days)
        $revenueData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueData[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => ShopSubscription::whereDate('created_at', $date)
                    ->where('status', 'active')
                    ->sum('amount_paid')
            ];
        }

        // Plan distribution
        $planDistribution = SubscriptionPlan::withCount(['subscriptions' => function($query) {
            $query->where('status', 'active');
        }])->get();

        // Monthly subscription trends
        $subscriptionTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $subscriptionTrends[] = [
                'month' => $date->format('M Y'),
                'new_subscriptions' => ShopSubscription::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'cancelled_subscriptions' => ShopSubscription::whereYear('cancelled_at', $date->year)
                    ->whereMonth('cancelled_at', $date->month)
                    ->count()
            ];
        }

        return view('admin.subscriptions.analytics', compact(
            'revenueData',
            'planDistribution',
            'subscriptionTrends'
        ));
    }
}