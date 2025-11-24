<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\ShopSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Available payment methods
     */
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_METHOD_FAWRY = 'fawry';
    const PAYMENT_METHOD_VODAFONE_CASH = 'vodafone_cash';
    const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    const PAYMENT_METHOD_PAYPAL = 'paypal';

    /**
     * Get all available payment methods
     */
    public static function getAvailablePaymentMethods(): array
    {
        return [
            self::PAYMENT_METHOD_CASH => [
                'name' => 'الدفع النقدي',
                'name_en' => 'Cash Payment',
                'description' => 'الدفع نقداً عند مندوب التحصيل',
                'icon' => '💵',
                'is_active' => true,
                'requires_verification' => true,
                'processing_time' => '1-2 أيام عمل'
            ],
            self::PAYMENT_METHOD_BANK_TRANSFER => [
                'name' => 'التحويل البنكي',
                'name_en' => 'Bank Transfer',
                'description' => 'تحويل مباشر إلى الحساب البنكي',
                'icon' => '🏦',
                'is_active' => true,
                'requires_verification' => true,
                'processing_time' => '1-3 أيام عمل',
                'bank_details' => [
                    'bank_name' => 'البنك الأهلي المصري',
                    'account_name' => 'SENÚ سنو',
                    'account_number' => 'XXXX-XXXX-XXXX',
                    'iban' => 'EG XX XXXX XXXX XXXX XXXX XXXX XXXX'
                ]
            ],
            self::PAYMENT_METHOD_FAWRY => [
                'name' => 'فوري',
                'name_en' => 'Fawry',
                'description' => 'الدفع عبر خدمة فوري',
                'icon' => '📱',
                'is_active' => false, // Will be implemented in next phase
                'requires_verification' => false,
                'processing_time' => 'فوري'
            ],
            self::PAYMENT_METHOD_VODAFONE_CASH => [
                'name' => 'فودافون كاش',
                'name_en' => 'Vodafone Cash',
                'description' => 'الدفع عبر محفظة فودافون كاش',
                'icon' => '📲',
                'is_active' => false, // Will be implemented in next phase
                'requires_verification' => false,
                'processing_time' => 'فوري'
            ],
            self::PAYMENT_METHOD_CREDIT_CARD => [
                'name' => 'بطاقة الائتمان',
                'name_en' => 'Credit Card',
                'description' => 'الدفع ببطاقة الائتمان أو الخصم',
                'icon' => '💳',
                'is_active' => false, // Will be implemented in next phase
                'requires_verification' => false,
                'processing_time' => 'فوري'
            ],
            self::PAYMENT_METHOD_PAYPAL => [
                'name' => 'باي بال',
                'name_en' => 'PayPal',
                'description' => 'الدفع عبر حساب باي بال',
                'icon' => '🌐',
                'is_active' => false, // Will be implemented in next phase
                'requires_verification' => false,
                'processing_time' => 'فوري'
            ]
        ];
    }

    /**
     * Get only active payment methods
     */
    public static function getActivePaymentMethods(): array
    {
        return array_filter(self::getAvailablePaymentMethods(), function ($method) {
            return $method['is_active'] === true;
        });
    }

    /**
     * Process payment for shop subscription
     */
    public function processSubscriptionPayment(
        Shop $shop,
        SubscriptionPlan $plan,
        string $paymentMethod,
        string $billingCycle = 'monthly',
        array $paymentDetails = []
    ): ShopSubscription {
        DB::beginTransaction();

        try {
            // Calculate amount based on billing cycle
            $amount = $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
            $duration = $billingCycle === 'yearly' ? 12 : 1;

            // Create subscription record
            $subscription = ShopSubscription::create([
                'shop_id' => $shop->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle' => $billingCycle,
                'amount_paid' => $amount,
                'status' => $this->getInitialPaymentStatus($paymentMethod),
                'starts_at' => now(),
                'ends_at' => now()->addMonths($duration),
                'payment_method' => $paymentMethod,
                'transaction_id' => $this->generateTransactionId(),
                'payment_details' => array_merge($paymentDetails, [
                    'payment_method' => $paymentMethod,
                    'billing_cycle' => $billingCycle,
                    'created_at' => now()->toDateTimeString()
                ]),
                'auto_renew' => true,
                'next_billing_date' => now()->addMonths($duration)
            ]);

            // Log payment attempt
            Log::info('Subscription payment initiated', [
                'shop_id' => $shop->id,
                'subscription_id' => $subscription->id,
                'payment_method' => $paymentMethod,
                'amount' => $amount
            ]);

            DB::commit();

            return $subscription;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription payment failed', [
                'shop_id' => $shop->id,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get initial payment status based on payment method
     */
    protected function getInitialPaymentStatus(string $paymentMethod): string
    {
        $methods = self::getAvailablePaymentMethods();
        
        if (!isset($methods[$paymentMethod])) {
            return 'pending';
        }

        // Methods that require verification start as pending
        if ($methods[$paymentMethod]['requires_verification']) {
            return 'pending';
        }

        // Instant payment methods start as active (will be implemented later)
        return 'pending';
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(uniqid()) . '-' . time();
    }

    /**
     * Verify and activate pending payment
     */
    public function verifyAndActivatePayment(ShopSubscription $subscription, array $verificationData = []): bool
    {
        try {
            $subscription->update([
                'status' => 'active',
                'payment_details' => array_merge($subscription->payment_details ?? [], [
                    'verified_at' => now()->toDateTimeString(),
                    'verified_by' => auth()->id(),
                    'verification_data' => $verificationData
                ])
            ]);

            Log::info('Subscription payment verified and activated', [
                'subscription_id' => $subscription->id,
                'shop_id' => $subscription->shop_id
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Process refund for subscription
     */
    public function refundSubscription(ShopSubscription $subscription, string $reason = ''): bool
    {
        try {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
                'payment_details' => array_merge($subscription->payment_details ?? [], [
                    'refunded_at' => now()->toDateTimeString(),
                    'refund_reason' => $reason
                ])
            ]);

            Log::info('Subscription refunded', [
                'subscription_id' => $subscription->id,
                'reason' => $reason
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(ShopSubscription $subscription, string $reason = ''): bool
    {
        try {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
                'auto_renew' => false
            ]);

            Log::info('Subscription cancelled', [
                'subscription_id' => $subscription->id,
                'reason' => $reason
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Cancellation failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Renew subscription
     */
    public function renewSubscription(ShopSubscription $subscription): ?ShopSubscription
    {
        try {
            $plan = $subscription->subscriptionPlan;
            $duration = $subscription->billing_cycle === 'yearly' ? 12 : 1;
            $amount = $subscription->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

            $newSubscription = ShopSubscription::create([
                'shop_id' => $subscription->shop_id,
                'subscription_plan_id' => $subscription->subscription_plan_id,
                'billing_cycle' => $subscription->billing_cycle,
                'amount_paid' => $amount,
                'status' => 'pending',
                'starts_at' => $subscription->ends_at,
                'ends_at' => $subscription->ends_at->addMonths($duration),
                'payment_method' => $subscription->payment_method,
                'transaction_id' => $this->generateTransactionId(),
                'payment_details' => [
                    'renewal' => true,
                    'previous_subscription_id' => $subscription->id
                ],
                'auto_renew' => $subscription->auto_renew,
                'next_billing_date' => $subscription->ends_at->addMonths($duration)
            ]);

            Log::info('Subscription renewed', [
                'old_subscription_id' => $subscription->id,
                'new_subscription_id' => $newSubscription->id
            ]);

            return $newSubscription;
        } catch (\Exception $e) {
            Log::error('Renewal failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
