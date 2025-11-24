# Payment Gateway Integration Guide

## Overview
This guide explains how to integrate new payment gateways into the SENÚ (سنو) platform. The payment system is built with modularity in mind, making it easy to add new payment methods while maintaining consistency.

---

## Architecture

### Core Components
1. **PaymentService** (`app/Services/PaymentService.php`) - Core payment processing logic
2. **Payment View** (`resources/views/shop-owner/payment.blade.php`) - Payment selection UI
3. **DashboardController** (`app/Http/Controllers/DashboardController.php`) - Payment flow orchestration
4. **ShopSubscription Model** (`app/Models/ShopSubscription.php`) - Payment data storage

---

## Adding a New Payment Method

### Step 1: Register Payment Method

Open `app/Services/PaymentService.php` and add your payment method to the `getAvailablePaymentMethods()` array:

```php
self::PAYMENT_METHOD_YOUR_GATEWAY => [
    'name' => 'اسم البوابة بالعربي',
    'name_en' => 'Gateway Name in English',
    'description' => 'وصف قصير للبوابة',
    'icon' => '💳', // Emoji or icon class
    'is_active' => true, // Set to true when ready for production
    'requires_verification' => false, // Set true for manual verification
    'processing_time' => 'فوري', // e.g., "فوري", "1-2 أيام"
    
    // Optional: Additional configuration
    'api_endpoint' => 'https://api.gateway.com',
    'merchant_id' => env('YOUR_GATEWAY_MERCHANT_ID'),
    'api_key' => env('YOUR_GATEWAY_API_KEY'),
],
```

**Add constant at top of class:**
```php
const PAYMENT_METHOD_YOUR_GATEWAY = 'your_gateway';
```

---

### Step 2: Configure Environment Variables

Add required credentials to `.env`:

```env
YOUR_GATEWAY_MERCHANT_ID=your_merchant_id
YOUR_GATEWAY_API_KEY=your_api_key
YOUR_GATEWAY_SECRET_KEY=your_secret_key
YOUR_GATEWAY_WEBHOOK_URL=https://yourdomain.com/webhooks/your-gateway
```

Add to `config/services.php`:

```php
'your_gateway' => [
    'merchant_id' => env('YOUR_GATEWAY_MERCHANT_ID'),
    'api_key' => env('YOUR_GATEWAY_API_KEY'),
    'secret_key' => env('YOUR_GATEWAY_SECRET_KEY'),
    'webhook_url' => env('YOUR_GATEWAY_WEBHOOK_URL'),
    'sandbox_mode' => env('YOUR_GATEWAY_SANDBOX', true),
],
```

---

### Step 3: Create Gateway Service Class

Create `app/Services/PaymentGateways/YourGatewayService.php`:

```php
<?php

namespace App\Services\PaymentGateways;

use App\Models\ShopSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YourGatewayService
{
    protected $merchantId;
    protected $apiKey;
    protected $secretKey;
    protected $baseUrl;
    protected $sandboxMode;

    public function __construct()
    {
        $config = config('services.your_gateway');
        $this->merchantId = $config['merchant_id'];
        $this->apiKey = $config['api_key'];
        $this->secretKey = $config['secret_key'];
        $this->sandboxMode = $config['sandbox_mode'];
        
        $this->baseUrl = $this->sandboxMode 
            ? 'https://sandbox.gateway.com/api' 
            : 'https://api.gateway.com';
    }

    /**
     * Initiate payment
     */
    public function initiatePayment(ShopSubscription $subscription): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payments', [
                'merchant_id' => $this->merchantId,
                'amount' => $subscription->amount_paid,
                'currency' => 'EGP',
                'order_id' => $subscription->transaction_id,
                'customer_email' => $subscription->shop->user->email,
                'customer_phone' => $subscription->shop->user->phone,
                'callback_url' => route('webhooks.your-gateway'),
                'return_url' => route('shop-owner.dashboard'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'payment_url' => $data['payment_url'],
                    'payment_id' => $data['payment_id'],
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Payment initiation failed',
            ];
        } catch (\Exception $e) {
            Log::error('Your Gateway payment failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment callback
     */
    public function verifyPayment(array $callbackData): array
    {
        try {
            // Verify signature
            $expectedSignature = $this->generateSignature($callbackData);
            
            if ($callbackData['signature'] !== $expectedSignature) {
                return [
                    'success' => false,
                    'error' => 'Invalid signature',
                ];
            }

            // Verify with gateway API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/payments/' . $callbackData['payment_id']);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'status' => $data['status'],
                    'transaction_id' => $data['transaction_id'],
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => 'Payment verification failed',
            ];
        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate signature for security
     */
    protected function generateSignature(array $data): string
    {
        ksort($data);
        $signatureString = '';
        
        foreach ($data as $key => $value) {
            if ($key !== 'signature') {
                $signatureString .= $key . '=' . $value;
            }
        }
        
        return hash_hmac('sha256', $signatureString, $this->secretKey);
    }

    /**
     * Refund payment
     */
    public function refund(ShopSubscription $subscription): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/refunds', [
                'payment_id' => $subscription->transaction_id,
                'amount' => $subscription->amount_paid,
                'reason' => $subscription->cancellation_reason,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'refund_id' => $response->json()['refund_id'],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Refund failed',
            ];
        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
```

---

### Step 4: Update PaymentService

Add method to handle the new gateway in `app/Services/PaymentService.php`:

```php
use App\Services\PaymentGateways\YourGatewayService;

/**
 * Process payment based on method
 */
public function processPaymentByMethod(
    ShopSubscription $subscription,
    string $paymentMethod
): array {
    switch ($paymentMethod) {
        case self::PAYMENT_METHOD_YOUR_GATEWAY:
            $gateway = new YourGatewayService();
            return $gateway->initiatePayment($subscription);
            
        case self::PAYMENT_METHOD_FAWRY:
            // Will be implemented
            break;
            
        // ... other methods
            
        default:
            return [
                'success' => false,
                'error' => 'Unsupported payment method',
            ];
    }
}
```

---

### Step 5: Update Frontend (Payment View)

The payment view (`resources/views/shop-owner/payment.blade.php`) automatically displays all active payment methods from `PaymentService::getActivePaymentMethods()`.

**For custom UI elements** (like embedded payment forms), add after the payment methods loop:

```blade
<!-- Your Gateway Custom UI -->
@if(isset($paymentMethods['your_gateway']))
<div id="your_gateway_form" class="d-none mt-4">
    <h6 class="mb-3">بيانات الدفع</h6>
    
    <!-- Add your custom form fields here -->
    <div class="mb-3">
        <label for="card_number" class="form-label">رقم البطاقة</label>
        <input type="text" class="form-control" id="card_number" name="card_number">
    </div>
    
    <!-- Add more fields as needed -->
</div>
@endif
```

**Update JavaScript** to show/hide custom form:

```javascript
if (this.value === 'your_gateway') {
    document.getElementById('your_gateway_form')?.classList.remove('d-none');
    if (paymentDetailsSection) paymentDetailsSection.classList.remove('d-none');
}
```

---

### Step 6: Create Webhook Controller

Create `app/Http/Controllers/Webhooks/YourGatewayWebhookController.php`:

```php
<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\ShopSubscription;
use App\Services\PaymentGateways\YourGatewayService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class YourGatewayWebhookController extends Controller
{
    /**
     * Handle webhook from Your Gateway
     */
    public function handle(Request $request)
    {
        Log::info('Your Gateway webhook received', $request->all());

        $gateway = new YourGatewayService();
        $verification = $gateway->verifyPayment($request->all());

        if (!$verification['success']) {
            Log::error('Webhook verification failed', $verification);
            return response()->json(['error' => 'Verification failed'], 400);
        }

        // Find subscription by transaction ID
        $subscription = ShopSubscription::where('transaction_id', $request->order_id)->first();

        if (!$subscription) {
            Log::error('Subscription not found', ['order_id' => $request->order_id]);
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        // Update subscription based on payment status
        if ($verification['status'] === 'success' || $verification['status'] === 'completed') {
            $paymentService = new PaymentService();
            $paymentService->verifyAndActivatePayment($subscription, $verification['data']);
            
            return response()->json(['message' => 'Payment processed successfully']);
        }

        if ($verification['status'] === 'failed') {
            $subscription->update([
                'status' => 'failed',
                'payment_details' => array_merge($subscription->payment_details ?? [], [
                    'failure_reason' => $verification['data']['failure_reason'] ?? 'Unknown',
                    'failed_at' => now()->toDateTimeString(),
                ]),
            ]);
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
```

---

### Step 7: Register Webhook Route

Add to `routes/web.php` or create `routes/webhooks.php`:

```php
use App\Http\Controllers\Webhooks\YourGatewayWebhookController;

Route::post('/webhooks/your-gateway', [YourGatewayWebhookController::class, 'handle'])
    ->name('webhooks.your-gateway')
    ->withoutMiddleware(['web', 'csrf']); // Disable CSRF for webhooks
```

If using separate webhooks file, register it in `bootstrap/app.php`:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    webhooks: __DIR__.'/../routes/webhooks.php', // Add this
    health: '/up',
)
```

---

### Step 8: Update DashboardController

Modify `processPayment()` method to handle instant payment gateways:

```php
public function processPayment(Request $request)
{
    // ... existing validation ...

    $plan = \App\Models\SubscriptionPlan::findOrFail($request->plan_id);
    $paymentService = new PaymentService();
    
    // Check if payment method requires instant processing
    $paymentMethods = PaymentService::getAvailablePaymentMethods();
    $selectedMethod = $paymentMethods[$request->payment_method] ?? null;

    if ($selectedMethod && !$selectedMethod['requires_verification']) {
        // Create shop and subscription immediately for instant payment
        // Then redirect to payment gateway
        
        // Store in session for now, will be processed after shop creation
        session([
            'pending_payment' => [
                'plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'payment_method' => $request->payment_method,
                'payment_details' => $paymentDetails,
                'instant_payment' => true,
            ]
        ]);
    } else {
        // Manual verification payment (existing flow)
        session([
            'pending_payment' => [
                'plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'payment_method' => $request->payment_method,
                'payment_details' => $paymentDetails,
            ]
        ]);
    }

    return redirect()->route('shop-owner.create-shop', ['plan' => $plan->id])
        ->with('success', 'تم تسجيل بيانات الدفع. الآن أكمل معلومات المتجر.');
}
```

Update `storeShop()` to redirect to payment gateway for instant payments:

```php
$shop = Shop::create($shopData);
$pendingPayment = session('pending_payment');

if ($pendingPayment) {
    $paymentService = new PaymentService();
    $subscriptionPlan = \App\Models\SubscriptionPlan::findOrFail($pendingPayment['plan_id']);
    
    $subscription = $paymentService->processSubscriptionPayment(
        $shop,
        $subscriptionPlan,
        $pendingPayment['payment_method'],
        $pendingPayment['billing_cycle'],
        $pendingPayment['payment_details']
    );
    
    // If instant payment, redirect to gateway
    if ($pendingPayment['instant_payment'] ?? false) {
        $result = $paymentService->processPaymentByMethod(
            $subscription,
            $pendingPayment['payment_method']
        );
        
        if ($result['success']) {
            session()->forget('pending_payment');
            return redirect($result['payment_url']);
        }
    }
    
    session()->forget('pending_payment');
    return redirect()->route('shop-owner.dashboard')
        ->with('success', 'تم إرسال طلب إضافة المتجر وبيانات الدفع بنجاح!');
}
```

---

## Testing

### 1. Test Payment Initiation
```php
// In tinker or test
$subscription = ShopSubscription::first();
$gateway = new \App\Services\PaymentGateways\YourGatewayService();
$result = $gateway->initiatePayment($subscription);
dd($result);
```

### 2. Test Webhook
```bash
# Use tools like ngrok for local testing
ngrok http 8000

# Update webhook URL in gateway dashboard
# Send test webhook from gateway dashboard
```

### 3. Test Payment Flow
1. Select subscription plan
2. Choose your payment method
3. Complete shop creation
4. Verify redirection to payment gateway
5. Complete payment
6. Verify webhook received
7. Check subscription status updated to 'active'

---

## Security Best Practices

1. **Always verify signatures** in webhooks
2. **Use HTTPS** for production webhooks
3. **Store sensitive keys** in `.env`, never commit them
4. **Log all transactions** for audit trails
5. **Implement rate limiting** on webhook endpoints
6. **Validate all webhook data** before processing
7. **Use database transactions** for payment processing
8. **Handle idempotency** - check if webhook already processed

---

## Troubleshooting

### Common Issues

**1. Webhook not receiving data**
- Check firewall rules
- Verify webhook URL is publicly accessible
- Check gateway dashboard for failed webhook attempts
- Review webhook logs: `storage/logs/laravel.log`

**2. Signature verification fails**
- Verify secret key is correct
- Check signature algorithm matches gateway's requirement
- Ensure data is sorted correctly before hashing

**3. Payment stuck in pending**
- Check webhook is properly configured
- Verify gateway sends success callback
- Check logs for errors
- Manually verify payment in gateway dashboard

---

## Example Payment Methods Already Implemented

### Cash Payment
- Manual verification required
- No API integration needed
- Admin activates after cash received

### Bank Transfer
- Manual verification required
- Receipt upload system
- Admin verifies transfer and activates

---

## Support

For questions or issues with payment integration:
- Check logs: `storage/logs/laravel.log`
- Review PaymentService documentation
- Contact development team

---

## Changelog

- **v1.0.0** (2025-11-23): Initial payment system with Cash and Bank Transfer
- **v1.1.0** (TBD): Add Fawry integration
- **v1.2.0** (TBD): Add Vodafone Cash integration
- **v1.3.0** (TBD): Add Credit Card integration
