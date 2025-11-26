<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\Category;
use App\Models\City;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Storage;
use App\Services\ShopImageGenerator;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isShopOwner()) {
            return $this->shopOwnerDashboard();
        }
        
        return $this->regularUserDashboard();
    }

    private function shopOwnerDashboard()
    {
        $seoData = [
            'title' => 'لوحة تحكم صاحب المتجر - اكتشف المدن',
            'description' => 'إدارة متاجرك ومراقبة أدائها من خلال لوحة التحكم الخاصة بأصحاب المتاجر',
            'keywords' => 'لوحة تحكم, صاحب متجر, إدارة متاجر, إحصائيات',
            'canonical' => route('shop-owner.dashboard')
        ];

        $shops = Auth::user()->shops()->with(['city', 'category'])->get();
        
        return view('shop-owner.dashboard', compact('shops', 'seoData'));
    }

    private function regularUserDashboard()
    {
        // Redirect regular users to their profile page
        return redirect()->route('profile');
    }

    public function createShop()
    {
        // Check if a subscription plan is selected
        if (!request()->has('plan')) {
            return redirect()->route('shop-owner.subscriptions')
                ->with('info', 'يجب عليك اختيار باقة اشتراك قبل إضافة متجر جديد.');
        }

        // Verify the plan exists and is active
        $subscriptionPlan = \App\Models\SubscriptionPlan::active()->find(request('plan'));
        if (!$subscriptionPlan) {
            return redirect()->route('shop-owner.subscriptions')
                ->with('error', 'الباقة المختارة غير متاحة. يرجى اختيار باقة أخرى.');
        }

        $seoData = [
            'title' => 'إضافة متجر جديد - اكتشف المدن',
            'description' => 'أضف متجرك الجديد وابدأ في جذب العملاء من خلال منصة اكتشف المدن',
            'keywords' => 'إضافة متجر, متجر جديد, تسجيل متجر, أصحاب المتاجر',
            'canonical' => route('shop-owner.create-shop')
        ];

        $categories = Category::all();
        $cities = City::all();
        
        return view('shop-owner.create-shop', compact('categories', 'cities', 'seoData', 'subscriptionPlan'));
    }

    public function subscriptions()
    {
        $seoData = [
            'title' => 'باقات الاشتراك - اكتشف المدن',
            'description' => 'اختر الباقة المناسبة لمتجرك وابدأ في جذب العملاء',
            'keywords' => 'باقات اشتراك, خطط تسعير, متاجر',
            'canonical' => route('shop-owner.subscriptions')
        ];

        $plans = \App\Models\SubscriptionPlan::active()->ordered()->get();
        $userShops = Auth::user()->shops()->with('activeSubscription.subscriptionPlan')->get();
        
        return view('shop-owner.subscriptions', compact('plans', 'userShops', 'seoData'));
    }

    public function showPayment(Request $request)
    {
        // Validate plan parameter
        $plan = \App\Models\SubscriptionPlan::active()->findOrFail($request->plan);
        $billingCycle = $request->get('cycle', 'monthly');
        
        // Calculate amount
        $amount = $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        
        // Get active payment methods
        $paymentMethods = PaymentService::getActivePaymentMethods();
        
        $seoData = [
            'title' => 'الدفع - اكتشف المدن',
            'description' => 'اختر طريقة الدفع المناسبة',
            'keywords' => 'دفع, اشتراك, متاجر',
            'canonical' => route('shop-owner.payment')
        ];
        
        return view('shop-owner.payment', compact('plan', 'billingCycle', 'amount', 'paymentMethods', 'seoData'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|string',
            'payment_notes' => 'nullable|string|max:1000',
            'transfer_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'transfer_reference' => 'nullable|string|max:255'
        ]);

        $plan = \App\Models\SubscriptionPlan::findOrFail($request->plan_id);
        
        // Prepare payment details
        $paymentDetails = [
            'notes' => $request->payment_notes,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ];

        // Handle file upload for bank transfer
        if ($request->hasFile('transfer_receipt')) {
            $path = $request->file('transfer_receipt')->store('payment-receipts', 'public');
            $paymentDetails['receipt_path'] = $path;
        }

        if ($request->filled('transfer_reference')) {
            $paymentDetails['transfer_reference'] = $request->transfer_reference;
        }

        // Store payment info in session to use after shop creation
        session([
            'pending_payment' => [
                'plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'payment_method' => $request->payment_method,
                'payment_details' => $paymentDetails
            ]
        ]);

        // Redirect to shop creation
        return redirect()->route('shop-owner.create-shop', ['plan' => $plan->id])
            ->with('success', 'تم تسجيل بيانات الدفع. الآن أكمل معلومات المتجر.');
    }

    public function storeShop(Request $request)
    {
        // Check if user has selected a subscription package
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'working_hours' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $shopData = $request->only([
            'name', 'category_id', 'city_id', 'phone', 'address', 
            'description', 'latitude', 'longitude', 'working_hours'
        ]);
        
        $shopData['user_id'] = Auth::id();
        $shopData['status'] = 'pending';
        
        // Handle images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('shops', 'public');
                $images[] = $path;
            }
            $shopData['images'] = $images;
        } else {
            // Generate default images if none uploaded
            $category = Category::find($shopData['category_id']);
            $imageGenerator = new ShopImageGenerator();
            $shopData['images'] = $imageGenerator->generateMultipleImages(
                $shopData['name'],
                $category->name ?? 'Shop',
                $category->icon ?? null,
                3
            );
        }

        $shop = Shop::create($shopData);

        // Get payment info from session
        $pendingPayment = session('pending_payment');
        
        if ($pendingPayment) {
            // Use PaymentService to process subscription payment
            $paymentService = new PaymentService();
            $subscriptionPlan = \App\Models\SubscriptionPlan::findOrFail($pendingPayment['plan_id']);
            
            $paymentService->processSubscriptionPayment(
                $shop,
                $subscriptionPlan,
                $pendingPayment['payment_method'],
                $pendingPayment['billing_cycle'],
                $pendingPayment['payment_details']
            );
            
            // Clear payment session
            session()->forget('pending_payment');
            
            return redirect()->route('shop-owner.dashboard')
                ->with('success', 'تم إرسال طلب إضافة المتجر وبيانات الدفع بنجاح! سيتم مراجعته خلال 24-48 ساعة.');
        }

        return redirect()->route('shop-owner.dashboard')
            ->with('success', 'تم إرسال طلب إضافة المتجر بنجاح! سيتم مراجعته خلال 24-48 ساعة.');
    }

    public function editShop(Shop $shop)
    {
        // Ensure user owns this shop
        if ($shop->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المتجر.');
        }

        $seoData = [
            'title' => 'تعديل متجر: ' . $shop->name . ' - اكتشف المدن',
            'description' => 'تعديل معلومات متجر ' . $shop->name . ' وتحديث البيانات والصور',
            'keywords' => 'تعديل متجر, ' . $shop->name . ', تحديث معلومات متجر',
            'canonical' => route('shop-owner.shops.edit', $shop)
        ];

        $categories = Category::all();
        $cities = City::all();
        
        return view('shop-owner.edit-shop', compact('shop', 'categories', 'cities', 'seoData'));
    }

    public function updateShop(Request $request, Shop $shop)
    {
        // Ensure user owns this shop
        if ($shop->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المتجر.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'working_hours' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer'
        ]);

        $shopData = $request->only([
            'name', 'category_id', 'city_id', 'phone', 'address', 
            'description', 'latitude', 'longitude', 'working_hours'
        ]);

        // Handle image deletion
        $currentImages = $shop->images ?? [];
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $index) {
                if (isset($currentImages[$index])) {
                    // Delete file from storage
                    Storage::disk('public')->delete($currentImages[$index]);
                    unset($currentImages[$index]);
                }
            }
            $currentImages = array_values($currentImages); // Reindex array
        }

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shops', 'public');
                $currentImages[] = $path;
            }
        }

        $shopData['images'] = $currentImages;
        
        // If shop was approved and user made changes, set back to pending
        if ($shop->status === 'approved') {
            $shopData['status'] = 'pending';
            $shopData['verification_notes'] = 'تم تحديث المتجر - في انتظار مراجعة التعديلات';
        }

        $shop->update($shopData);

        return redirect()->route('shop-owner.dashboard')
            ->with('success', 'تم تحديث المتجر بنجاح!' . 
                ($shop->status === 'pending' ? ' سيتم مراجعة التعديلات قريباً.' : ''));
    }
}