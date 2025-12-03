<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackApiController extends Controller
{
    /**
     * Submit feedback
     * 
     * @group Feedback
     * @bodyParam rating integer required Rating from 1 to 5. Example: 5
     * @bodyParam message string optional Feedback message. Example: موقع رائع وسهل الاستخدام!
     * @bodyParam email string optional User's email. Example: user@example.com
     * @bodyParam page_url string optional Page URL where feedback was given. Example: https://senueg.com/city/cairo
     * 
     * @response 201 {
     *   "success": true,
     *   "message": "شكراً لك! تقييمك يساعدنا على التحسين",
     *   "data": {
     *     "id": 1,
     *     "rating": 5,
     *     "message": "موقع رائع وسهل الاستخدام!",
     *     "submitted_at": "2025-12-03T10:30:00.000000Z"
     *   }
     * }
     * 
     * @response 422 {
     *   "success": false,
     *   "message": "فشل التحقق من البيانات",
     *   "errors": {
     *     "rating": ["التقييم مطلوب"]
     *   }
     * }
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'page_url' => 'nullable|string|max:500',
        ], [
            'rating.required' => 'التقييم مطلوب',
            'rating.integer' => 'التقييم يجب أن يكون رقماً',
            'rating.min' => 'التقييم يجب أن يكون على الأقل 1',
            'rating.max' => 'التقييم يجب ألا يتجاوز 5',
            'message.max' => 'الرسالة طويلة جداً (الحد الأقصى 1000 حرف)',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'page_url.max' => 'رابط الصفحة طويل جداً',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $feedback = Feedback::create([
            'user_id' => auth('sanctum')->id(),
            'rating' => $request->rating,
            'message' => $request->message,
            'email' => $request->email,
            'page_url' => $request->page_url ?? 'mobile-app',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'شكراً لك! تقييمك يساعدنا على التحسين',
            'data' => [
                'id' => $feedback->id,
                'rating' => $feedback->rating,
                'message' => $feedback->message,
                'submitted_at' => $feedback->submitted_at,
            ]
        ], 201);
    }

    /**
     * Get feedback statistics
     * 
     * @group Feedback
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "total_feedback": 234,
     *     "average_rating": 4.3,
     *     "rating_distribution": {
     *       "5": 152,
     *       "4": 70,
     *       "3": 7,
     *       "2": 3,
     *       "1": 2
     *     },
     *     "positive_count": 222,
     *     "negative_count": 5,
     *     "recent_feedback": [
     *       {
     *         "id": 1,
     *         "rating": 5,
     *         "message": "موقع رائع!",
     *         "submitted_at": "2025-12-03T10:30:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     */
    public function statistics(Request $request)
    {
        $stats = [
            'total_feedback' => Feedback::count(),
            'average_rating' => round(Feedback::avg('rating'), 2),
            'rating_distribution' => [
                '5' => Feedback::where('rating', 5)->count(),
                '4' => Feedback::where('rating', 4)->count(),
                '3' => Feedback::where('rating', 3)->count(),
                '2' => Feedback::where('rating', 2)->count(),
                '1' => Feedback::where('rating', 1)->count(),
            ],
            'positive_count' => Feedback::positive()->count(),
            'negative_count' => Feedback::negative()->count(),
            'recent_feedback' => Feedback::latest('submitted_at')
                ->limit(10)
                ->get()
                ->map(function ($feedback) {
                    return [
                        'id' => $feedback->id,
                        'rating' => $feedback->rating,
                        'message' => $feedback->message,
                        'submitted_at' => $feedback->submitted_at,
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ], 200);
    }

    /**
     * Get user's feedback history
     * 
     * @group Feedback
     * @authenticated
     * 
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "rating": 5,
     *       "message": "موقع رائع!",
     *       "page_url": "https://senueg.com/city/cairo",
     *       "submitted_at": "2025-12-03T10:30:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function userHistory(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        $feedbacks = Feedback::where('user_id', $user->id)
            ->latest('submitted_at')
            ->get()
            ->map(function ($feedback) {
                return [
                    'id' => $feedback->id,
                    'rating' => $feedback->rating,
                    'message' => $feedback->message,
                    'page_url' => $feedback->page_url,
                    'submitted_at' => $feedback->submitted_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $feedbacks
        ], 200);
    }

    /**
     * Update user's feedback
     * 
     * @group Feedback
     * @authenticated
     * @urlParam id integer required The feedback ID. Example: 1
     * @bodyParam rating integer required Rating from 1 to 5. Example: 4
     * @bodyParam message string optional Updated feedback message. Example: موقع جيد بعد التحديثات
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "تم تحديث التقييم بنجاح",
     *   "data": {
     *     "id": 1,
     *     "rating": 4,
     *     "message": "موقع جيد بعد التحديثات",
     *     "submitted_at": "2025-12-03T10:30:00.000000Z"
     *   }
     * }
     * 
     * @response 403 {
     *   "success": false,
     *   "message": "غير مصرح لك بتحديث هذا التقييم"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "التقييم غير موجود"
     * }
     */
    public function update(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'التقييم غير موجود'
            ], 404);
        }

        if ($feedback->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتحديث هذا التقييم'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'التقييم مطلوب',
            'rating.integer' => 'التقييم يجب أن يكون رقماً',
            'rating.min' => 'التقييم يجب أن يكون على الأقل 1',
            'rating.max' => 'التقييم يجب ألا يتجاوز 5',
            'message.max' => 'الرسالة طويلة جداً (الحد الأقصى 1000 حرف)',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $feedback->update([
            'rating' => $request->rating,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التقييم بنجاح',
            'data' => [
                'id' => $feedback->id,
                'rating' => $feedback->rating,
                'message' => $feedback->message,
                'submitted_at' => $feedback->submitted_at,
            ]
        ], 200);
    }

    /**
     * Delete user's feedback
     * 
     * @group Feedback
     * @authenticated
     * @urlParam id integer required The feedback ID. Example: 1
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "تم حذف التقييم بنجاح"
     * }
     * 
     * @response 403 {
     *   "success": false,
     *   "message": "غير مصرح لك بحذف هذا التقييم"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "التقييم غير موجود"
     * }
     */
    public function destroy($id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'التقييم غير موجود'
            ], 404);
        }

        if ($feedback->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذا التقييم'
            ], 403);
        }

        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التقييم بنجاح'
        ], 200);
    }
}
