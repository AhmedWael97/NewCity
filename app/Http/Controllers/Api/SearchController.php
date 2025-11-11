<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\CategoryResource;
use App\Models\Shop;
use App\Models\UserService;
use App\Models\City;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Search",
 *     description="Search endpoints for shops, services, cities"
 * )
 */
class SearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/search",
     *     summary="Global search across shops, services, and cities",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=true,
     *         description="Search query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Search type filter",
     *         @OA\Schema(type="string", enum={"shops", "services", "cities", "all"}, default="all")
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="User latitude for distance calculation",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="User longitude for distance calculation",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Search radius in kilometers",
     *         @OA\Schema(type="number", format="float", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="shops", type="array", @OA\Items(ref="#/components/schemas/Shop")),
     *                 @OA\Property(property="services", type="array", @OA\Items(ref="#/components/schemas/UserService")),
     *                 @OA\Property(property="cities", type="array", @OA\Items(ref="#/components/schemas/City")),
     *                 @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total_results", type="integer"),
     *                 @OA\Property(property="search_query", type="string"),
     *                 @OA\Property(property="search_type", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));
        $type = $request->input('type', 'all');
        $cityId = $request->input('city_id');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius', 10);

        if (empty($query)) {
            return response()->json([
                'message' => 'Search query is required',
                'data' => [
                    'shops' => [],
                    'services' => [],
                    'cities' => [],
                    'categories' => []
                ],
                'meta' => [
                    'total_results' => 0,
                    'search_query' => $query,
                    'search_type' => $type
                ]
            ], 422);
        }

        $results = [];
        $totalResults = 0;

        // Search shops
        if ($type === 'all' || $type === 'shops') {
            $shopsQuery = Shop::query()
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('address', 'like', "%{$query}%");
                })
                ->with(['city', 'category']);

            if ($cityId) {
                $shopsQuery->where('city_id', $cityId);
            }

            if ($latitude && $longitude) {
                $shopsQuery->whereRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) < ?",
                    [$latitude, $longitude, $latitude, $radius]
                );
            }

            $shops = $shopsQuery->take(10)->get();
            $results['shops'] = ShopResource::collection($shops);
            $totalResults += $shops->count();
        }

        // Search user services
        if ($type === 'all' || $type === 'services') {
            $servicesQuery = UserService::query()
                ->where('is_active', true)
                ->where('is_verified', true)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->with(['user', 'city', 'serviceCategory']);

            if ($cityId) {
                $servicesQuery->where('city_id', $cityId);
            }

            $services = $servicesQuery->take(10)->get();
            $results['services'] = UserServiceResource::collection($services);
            $totalResults += $services->count();
        }

        // Search cities
        if ($type === 'all' || $type === 'cities') {
            $cities = City::query()
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('name_ar', 'like', "%{$query}%");
                })
                ->take(5)
                ->get();

            $results['cities'] = CityResource::collection($cities);
            $totalResults += $cities->count();
        }

        // Search categories
        if ($type === 'all' || $type === 'categories') {
            $categories = Category::query()
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('name_ar', 'like', "%{$query}%");
                })
                ->take(5)
                ->get();

            $results['categories'] = CategoryResource::collection($categories);
            $totalResults += $categories->count();
        }

        return response()->json([
            'data' => $results,
            'meta' => [
                'total_results' => $totalResults,
                'search_query' => $query,
                'search_type' => $type
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/search/suggestions",
     *     summary="Get search suggestions",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=true,
     *         description="Partial search query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of suggestions to return",
     *         @OA\Schema(type="integer", default=10, maximum=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search suggestions",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="text", type="string"),
     *                     @OA\Property(property="type", type="string"),
     *                     @OA\Property(property="category", type="string"),
     *                     @OA\Property(property="city", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));
        $limit = min($request->input('limit', 10), 20);

        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $suggestions = [];

        // Shop suggestions
        $shops = Shop::query()
            ->where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->select('name', 'city_id')
            ->with('city:id,name')
            ->take(5)
            ->get();

        foreach ($shops as $shop) {
            $suggestions[] = [
                'text' => $shop->name,
                'type' => 'shop',
                'category' => 'متجر',
                'city' => $shop->city->name ?? null
            ];
        }

        // Service suggestions
        $services = UserService::query()
            ->where('is_active', true)
            ->where('is_verified', true)
            ->where('title', 'like', "%{$query}%")
            ->select('title', 'city_id')
            ->with('city:id,name')
            ->take(5)
            ->get();

        foreach ($services as $service) {
            $suggestions[] = [
                'text' => $service->title,
                'type' => 'service',
                'category' => 'خدمة',
                'city' => $service->city->name ?? null
            ];
        }

        // Category suggestions
        $categories = Category::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('name_ar', 'like', "%{$query}%");
            })
            ->select('name', 'name_ar')
            ->take(3)
            ->get();

        foreach ($categories as $category) {
            $suggestions[] = [
                'text' => $category->name_ar ?: $category->name,
                'type' => 'category',
                'category' => 'فئة',
                'city' => null
            ];
        }

        // City suggestions
        $cities = City::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('name_ar', 'like', "%{$query}%");
            })
            ->select('name', 'name_ar')
            ->take(3)
            ->get();

        foreach ($cities as $city) {
            $suggestions[] = [
                'text' => $city->name_ar ?: $city->name,
                'type' => 'city',
                'category' => 'مدينة',
                'city' => null
            ];
        }

        // Limit and return suggestions
        $suggestions = array_slice($suggestions, 0, $limit);

        return response()->json([
            'data' => $suggestions
        ]);
    }
}