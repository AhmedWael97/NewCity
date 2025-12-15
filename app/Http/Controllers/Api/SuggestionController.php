<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopSuggestion;
use App\Models\CitySuggestion;
use App\Services\AdminEmailQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuggestionController extends Controller
{
    /**
     * Submit a shop suggestion
     *
     * @OA\Post(
     *     path="/api/v1/suggestions/shop",
     *     tags={"Suggestions"},
     *     summary="Submit a shop suggestion",
     *     description="Submit a suggestion for a new shop to be added to the platform",
     *     operationId="suggestShop",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shop_name", "city_id", "category_id", "phone"},
     *             @OA\Property(property="shop_name", type="string", maxLength=255, example="مطعم الأصالة"),
     *             @OA\Property(property="city_id", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=5),
     *             @OA\Property(property="location", type="string", maxLength=500, example="حي النزهة، شارع الملك فهد"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="0551234567"),
     *             @OA\Property(property="whatsapp", type="string", maxLength=20, example="0551234567"),
     *             @OA\Property(property="description", type="string", maxLength=1000, example="مطعم يقدم أشهى المأكولات التقليدية"),
     *             @OA\Property(property="google_maps_link", type="string", format="uri", maxLength=500, example="https://maps.google.com/?q=24.7136,46.6753")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Suggestion submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="شكراً لك! تم إرسال اقتراحك بنجاح وسنقوم بمراجعته قريباً."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="suggestion_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="pending")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="بيانات غير صحيحة"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="shop_name", type="array", @OA\Items(type="string", example="اسم المتجر مطلوب")),
     *                 @OA\Property(property="city_id", type="array", @OA\Items(type="string", example="المدينة المحددة غير موجودة")),
     *                 @OA\Property(property="category_id", type="array", @OA\Items(type="string", example="التصنيف مطلوب")),
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string", example="رقم الهاتف مطلوب"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء حفظ الاقتراح. الرجاء المحاولة مرة أخرى.")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestShop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'location' => 'nullable|string|max:500',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'google_maps_link' => 'nullable|url|max:500',
        ], [
            'shop_name.required' => 'اسم المتجر مطلوب',
            'shop_name.max' => 'اسم المتجر يجب ألا يتجاوز 255 حرف',
            'city_id.required' => 'المدينة مطلوبة',
            'city_id.exists' => 'المدينة المحددة غير موجودة',
            'category_id.required' => 'التصنيف مطلوب',
            'category_id.exists' => 'التصنيف المحدد غير موجود',
            'location.max' => 'الموقع يجب ألا يتجاوز 500 حرف',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 رقم',
            'whatsapp.max' => 'رقم الواتساب يجب ألا يتجاوز 20 رقم',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
            'google_maps_link.url' => 'رابط خرائط جوجل غير صحيح',
            'google_maps_link.max' => 'رابط خرائط جوجل يجب ألا يتجاوز 500 حرف',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suggestion = ShopSuggestion::create([
                'shop_name' => $request->shop_name,
                'city_id' => $request->city_id,
                'category_id' => $request->category_id,
                'location' => $request->location,
                'phone' => $request->phone,
                'whatsapp' => $request->whatsapp,
                'description' => $request->description,
                'google_maps_link' => $request->google_maps_link,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'pending',
            ]);

            // Queue email notification to admins (API request)
            AdminEmailQueueService::queueShopSuggestion($suggestion->load('city'));

            return response()->json([
                'success' => true,
                'message' => 'شكراً لك! تم إرسال اقتراحك بنجاح وسنقوم بمراجعته قريباً.',
                'data' => [
                    'suggestion_id' => $suggestion->id,
                    'status' => $suggestion->status,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ الاقتراح. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * Submit a city suggestion
     *
     * @OA\Post(
     *     path="/api/v1/suggestions/city",
     *     tags={"Suggestions"},
     *     summary="Submit a city suggestion",
     *     description="Submit a suggestion for a new city to be added to the platform",
     *     operationId="suggestCity",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"city_name", "phone", "group_url"},
     *             @OA\Property(property="city_name", type="string", maxLength=255, example="الرياض"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="0551234567"),
     *             @OA\Property(property="group_url", type="string", format="uri", maxLength=500, example="https://chat.whatsapp.com/example123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Suggestion submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="شكراً لك! تم إرسال اقتراحك بنجاح وسنقوم بمراجعته قريباً."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="suggestion_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="pending")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="بيانات غير صحيحة"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="city_name", type="array", @OA\Items(type="string", example="اسم المدينة مطلوب")),
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string", example="رقم الهاتف مطلوب")),
     *                 @OA\Property(property="group_url", type="array", @OA\Items(type="string", example="رابط المجموعة غير صحيح"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء حفظ الاقتراح. الرجاء المحاولة مرة أخرى.")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestCity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'group_url' => 'required|url|max:500',
        ], [
            'city_name.required' => 'اسم المدينة مطلوب',
            'city_name.max' => 'اسم المدينة يجب ألا يتجاوز 255 حرف',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 رقم',
            'group_url.required' => 'رابط المجموعة مطلوب',
            'group_url.url' => 'رابط المجموعة غير صحيح',
            'group_url.max' => 'رابط المجموعة يجب ألا يتجاوز 500 حرف',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suggestion = CitySuggestion::create([
                'city_name' => $request->city_name,
                'phone' => $request->phone,
                'group_url' => $request->group_url,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'pending',
            ]);

            // Queue email notification to admins (API request)
            AdminEmailQueueService::queueCitySuggestion($suggestion);

            return response()->json([
                'success' => true,
                'message' => 'شكراً لك! تم إرسال اقتراحك بنجاح وسنقوم بمراجعته قريباً.',
                'data' => [
                    'suggestion_id' => $suggestion->id,
                    'status' => $suggestion->status,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ الاقتراح. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }
}
