<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminShopApprovalController extends Controller
{
    /**
     * Display pending shop approvals
     */
    public function index(Request $request)
    {
        $query = Shop::with(['user', 'city', 'category', 'subscriptions' => function($q) {
                $q->latest();
            }])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
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

        $shops = $query->paginate(20);

        $stats = [
            'pending_count' => Shop::where('status', 'pending')->count(),
            'approved_today' => Shop::where('status', 'approved')
                ->whereDate('verified_at', today())
                ->count(),
            'rejected_today' => Shop::where('status', 'rejected')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        $cities = \App\Models\City::all();
        $categories = \App\Models\Category::all();

        return view('admin.shop-approvals.index', compact('shops', 'stats', 'cities', 'categories'));
    }

    /**
     * Show shop approval details
     */
    public function show(Shop $shop)
    {
        $shop->load(['user', 'city', 'category', 'subscriptions.subscriptionPlan']);
        
        // Get latest subscription
        $latestSubscription = $shop->subscriptions()->latest()->first();

        return view('admin.shop-approvals.show', compact('shop', 'latestSubscription'));
    }

    /**
     * Approve shop and payment
     */
    public function approve(Request $request, Shop $shop)
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
            'verify_payment' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Update shop status
            $shop->update([
                'status' => 'approved',
                'is_active' => true,
                'verified_at' => now(),
                'verification_notes' => $request->approval_notes ?? 'تمت الموافقة على المتجر',
            ]);

            // If verify_payment is checked, also activate the subscription
            if ($request->verify_payment) {
                $latestSubscription = $shop->subscriptions()->latest()->first();
                if ($latestSubscription && $latestSubscription->status === 'pending') {
                    $paymentService = new \App\Services\PaymentService();
                    $paymentService->verifyAndActivatePayment($latestSubscription, [
                        'verified_by' => auth()->id(),
                        'verified_at' => now()->toDateTimeString(),
                        'verification_notes' => 'تم التحقق تلقائياً عند الموافقة على المتجر',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.shop-approvals.index')
                ->with('success', 'تم الموافقة على المتجر بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Reject shop
     */
    public function reject(Request $request, Shop $shop)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'reject_payment' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Update shop status
            $shop->update([
                'status' => 'rejected',
                'verification_notes' => $request->rejection_reason,
            ]);

            // If reject_payment is checked, also reject the subscription
            if ($request->reject_payment) {
                $latestSubscription = $shop->subscriptions()->latest()->first();
                if ($latestSubscription && $latestSubscription->status === 'pending') {
                    $latestSubscription->update([
                        'status' => 'rejected',
                        'cancellation_reason' => $request->rejection_reason,
                        'cancelled_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.shop-approvals.index')
                ->with('success', 'تم رفض المتجر');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Request changes from shop owner
     */
    public function requestChanges(Request $request, Shop $shop)
    {
        $request->validate([
            'requested_changes' => 'required|string|max:1000',
        ]);

        try {
            $shop->update([
                'status' => 'pending',
                'verification_notes' => 'مطلوب تعديلات: ' . $request->requested_changes,
            ]);

            // TODO: Send notification to shop owner

            return redirect()->route('admin.shop-approvals.index')
                ->with('success', 'تم إرسال طلب التعديلات لصاحب المتجر');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
