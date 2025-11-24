<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopSubscription;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display all payments
     */
    public function index(Request $request)
    {
        $query = ShopSubscription::with(['shop.user', 'shop.city', 'subscriptionPlan'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('shop', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => ShopSubscription::count(),
            'pending' => ShopSubscription::where('status', 'pending')->count(),
            'active' => ShopSubscription::where('status', 'active')->count(),
            'expired' => ShopSubscription::where('status', 'expired')->count(),
            'cancelled' => ShopSubscription::where('status', 'cancelled')->count(),
            'total_revenue' => ShopSubscription::where('status', 'active')->sum('amount_paid'),
            'pending_revenue' => ShopSubscription::where('status', 'pending')->sum('amount_paid'),
        ];

        $paymentMethods = PaymentService::getAvailablePaymentMethods();

        return view('admin.payments.index', compact('payments', 'stats', 'paymentMethods'));
    }

    /**
     * Display pending payments
     */
    public function pending()
    {
        $payments = ShopSubscription::with(['shop.user', 'shop.city', 'subscriptionPlan'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending_count' => $payments->total(),
            'pending_revenue' => ShopSubscription::where('status', 'pending')->sum('amount_paid'),
        ];

        return view('admin.payments.pending', compact('payments', 'stats'));
    }

    /**
     * Show payment details
     */
    public function show(ShopSubscription $subscription)
    {
        $subscription->load(['shop.user', 'shop.city', 'subscriptionPlan']);
        
        $paymentMethods = PaymentService::getAvailablePaymentMethods();
        $paymentMethodInfo = $paymentMethods[$subscription->payment_method] ?? null;

        return view('admin.payments.show', compact('subscription', 'paymentMethodInfo'));
    }

    /**
     * Verify and activate payment
     */
    public function verifyPayment(Request $request, ShopSubscription $subscription)
    {
        $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $verificationData = [
                'verified_by' => auth()->id(),
                'verified_at' => now()->toDateTimeString(),
                'verification_notes' => $request->verification_notes,
                'admin_ip' => $request->ip(),
            ];

            $result = $this->paymentService->verifyAndActivatePayment($subscription, $verificationData);

            if ($result) {
                // Also approve the shop if it's pending
                $shop = $subscription->shop;
                if ($shop->status === 'pending') {
                    $shop->update([
                        'status' => 'approved',
                        'is_active' => true,
                        'verified_at' => now(),
                        'verification_notes' => 'تم الموافقة على المتجر والاشتراك',
                    ]);
                }

                DB::commit();
                return redirect()->route('admin.payments.show', $subscription)
                    ->with('success', 'تم التحقق من الدفع وتفعيل الاشتراك بنجاح');
            }

            DB::rollBack();
            return back()->with('error', 'فشل التحقق من الدفع');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Reject payment
     */
    public function rejectPayment(Request $request, ShopSubscription $subscription)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            $subscription->update([
                'status' => 'rejected',
                'cancellation_reason' => $request->rejection_reason,
                'cancelled_at' => now(),
                'payment_details' => array_merge($subscription->payment_details ?? [], [
                    'rejected_by' => auth()->id(),
                    'rejected_at' => now()->toDateTimeString(),
                    'rejection_reason' => $request->rejection_reason,
                ]),
            ]);

            // Also reject the shop
            $shop = $subscription->shop;
            $shop->update([
                'status' => 'rejected',
                'verification_notes' => $request->rejection_reason,
            ]);

            return redirect()->route('admin.payments.pending')
                ->with('success', 'تم رفض الدفع والمتجر');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Refund payment
     */
    public function refund(Request $request, ShopSubscription $subscription)
    {
        $request->validate([
            'refund_reason' => 'required|string|max:1000',
        ]);

        try {
            $result = $this->paymentService->refundSubscription(
                $subscription,
                $request->refund_reason
            );

            if ($result) {
                return redirect()->route('admin.payments.show', $subscription)
                    ->with('success', 'تم استرداد المبلغ بنجاح');
            }

            return back()->with('error', 'فشل استرداد المبلغ');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
