<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Advertisements",
 *     description="API endpoints for advertisements (mobile app)"
 * )
 */
class AdvertisementController extends Controller
{
    protected $adService;

    public function __construct(AdService $adService)
    {
        $this->adService = $adService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/ads",
     *     summary="Get advertisements for placement",
     *     tags={"Advertisements"},
     *     @OA\Parameter(
     *         name="placement",
     *         in="query",
     *         required=true,
     *         description="Ad placement (homepage, city_landing, shop_page, etc.)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="City ID for city-specific ads",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of ads to return (default: 3)",
     *         @OA\Schema(type="integer", default=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Advertisements retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'placement' => 'required|string',
            'city_id' => 'nullable|integer|exists:cities,id',
            'limit' => 'nullable|integer|min:1|max:10'
        ]);

        $placement = $validated['placement'];
        $cityId = $validated['city_id'] ?? null;
        $limit = $validated['limit'] ?? 3;

        $ads = $this->adService->getAdsForPlacement($placement, $cityId, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Advertisements retrieved successfully',
            'data' => $ads->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_path ? asset('storage/' . $ad->image_path) : null,
                    'click_url' => $ad->click_url,
                    'button_text' => $ad->button_text ?? 'اكتشف المزيد',
                    'type' => $ad->type,
                    'placement' => $ad->placement,
                ];
            })
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/ads/hero",
     *     summary="Get hero ads for city landing",
     *     tags={"Advertisements"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="City ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hero ads retrieved successfully"
     *     )
     * )
     */
    public function hero(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'nullable|integer|exists:cities,id'
        ]);

        $ads = $this->adService->getHeroAds($validated['city_id'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Hero ads retrieved successfully',
            'data' => $ads->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_path ? asset('storage/' . $ad->image_path) : null,
                    'click_url' => $ad->click_url,
                    'button_text' => $ad->button_text ?? 'اكتشف المزيد',
                ];
            })
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/ads/banner",
     *     summary="Get banner ads",
     *     tags={"Advertisements"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page name (homepage, shop_page, etc.)",
     *         @OA\Schema(type="string", default="homepage")
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="City ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Banner ads retrieved successfully"
     *     )
     * )
     */
    public function banner(Request $request)
    {
        $validated = $request->validate([
            'page' => 'nullable|string',
            'city_id' => 'nullable|integer|exists:cities,id'
        ]);

        $page = $validated['page'] ?? 'homepage';
        $cityId = $validated['city_id'] ?? null;

        $ads = $this->adService->getBannerAds($page, $cityId);

        return response()->json([
            'success' => true,
            'message' => 'Banner ads retrieved successfully',
            'data' => $ads->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_path ? asset('storage/' . $ad->image_path) : null,
                    'click_url' => $ad->click_url,
                    'button_text' => $ad->button_text ?? 'عرض التفاصيل',
                ];
            })
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/ads/sidebar",
     *     summary="Get sidebar ads",
     *     tags={"Advertisements"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page name (homepage, shop_page, etc.)",
     *         @OA\Schema(type="string", default="homepage")
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="City ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sidebar ads retrieved successfully"
     *     )
     * )
     */
    public function sidebar(Request $request)
    {
        $validated = $request->validate([
            'page' => 'nullable|string',
            'city_id' => 'nullable|integer|exists:cities,id'
        ]);

        $page = $validated['page'] ?? 'homepage';
        $cityId = $validated['city_id'] ?? null;

        $ads = $this->adService->getSidebarAds($page, $cityId);

        return response()->json([
            'success' => true,
            'message' => 'Sidebar ads retrieved successfully',
            'data' => $ads->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_path ? asset('storage/' . $ad->image_path) : null,
                    'click_url' => $ad->click_url,
                    'button_text' => $ad->button_text ?? 'اكتشف الآن',
                ];
            })
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/ads/{adId}/impression",
     *     summary="Record ad impression",
     *     tags={"Advertisements"},
     *     @OA\Parameter(
     *         name="adId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Impression recorded successfully"
     *     )
     * )
     */
    public function recordImpression($adId)
    {
        $this->adService->recordImpression($adId);

        return response()->json([
            'success' => true,
            'message' => 'Impression recorded successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/ads/{adId}/click",
     *     summary="Record ad click",
     *     tags={"Advertisements"},
     *     @OA\Parameter(
     *         name="adId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Click recorded successfully"
     *     )
     * )
     */
    public function recordClick($adId)
    {
        $clickUrl = $this->adService->recordClick($adId);

        return response()->json([
            'success' => true,
            'message' => 'Click recorded successfully',
            'data' => [
                'redirect_url' => $clickUrl
            ]
        ]);
    }
}
