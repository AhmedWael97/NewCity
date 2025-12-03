<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterApiController extends Controller
{
    /**
     * Subscribe to newsletter
     * 
     * @group Newsletter
     * @bodyParam email string required The subscriber's email address. Example: user@example.com
     * @bodyParam name string optional The subscriber's name. Example: أحمد محمد
     * 
     * @response 201 {
     *   "success": true,
     *   "message": "تم الاشتراك بنجاح! سنرسل لك أحدث العروض والمتاجر",
     *   "data": {
     *     "id": 1,
     *     "email": "user@example.com",
     *     "name": "أحمد محمد",
     *     "subscribed_at": "2025-12-03T10:30:00.000000Z"
     *   }
     * }
     * 
     * @response 422 {
     *   "success": false,
     *   "message": "فشل التحقق من البيانات",
     *   "errors": {
     *     "email": ["البريد الإلكتروني مطلوب"]
     *   }
     * }
     * 
     * @response 409 {
     *   "success": false,
     *   "message": "هذا البريد الإلكتروني مشترك بالفعل"
     * }
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.max' => 'البريد الإلكتروني طويل جداً',
            'name.max' => 'الاسم طويل جداً',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if already subscribed
        $existing = NewsletterSubscriber::where('email', $request->email)->first();
        
        if ($existing) {
            if ($existing->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا البريد الإلكتروني مشترك بالفعل'
                ], 409);
            } else {
                // Re-activate subscription
                $existing->update([
                    'is_active' => true,
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'تم إعادة تفعيل اشتراكك بنجاح!',
                    'data' => [
                        'id' => $existing->id,
                        'email' => $existing->email,
                        'name' => $existing->name,
                        'subscribed_at' => $existing->subscribed_at,
                    ]
                ], 200);
            }
        }

        // Create new subscription
        $subscriber = NewsletterSubscriber::create([
            'email' => $request->email,
            'name' => $request->name,
            'subscribed_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الاشتراك بنجاح! سنرسل لك أحدث العروض والمتاجر',
            'data' => [
                'id' => $subscriber->id,
                'email' => $subscriber->email,
                'name' => $subscriber->name,
                'subscribed_at' => $subscriber->subscribed_at,
            ]
        ], 201);
    }

    /**
     * Unsubscribe from newsletter
     * 
     * @group Newsletter
     * @bodyParam email string required The subscriber's email address. Example: user@example.com
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "تم إلغاء الاشتراك بنجاح"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "البريد الإلكتروني غير مشترك"
     * }
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $subscriber = NewsletterSubscriber::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني غير مشترك'
            ], 404);
        }

        $subscriber->unsubscribe();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الاشتراك بنجاح'
        ], 200);
    }

    /**
     * Check subscription status
     * 
     * @group Newsletter
     * @urlParam email string required The subscriber's email address. Example: user@example.com
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "is_subscribed": true,
     *     "subscribed_at": "2025-12-03T10:30:00.000000Z"
     *   }
     * }
     */
    public function checkStatus(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني مطلوب'
            ], 400);
        }

        $subscriber = NewsletterSubscriber::where('email', $email)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'is_subscribed' => (bool) $subscriber,
                'subscribed_at' => $subscriber ? $subscriber->subscribed_at : null,
            ]
        ], 200);
    }
}
