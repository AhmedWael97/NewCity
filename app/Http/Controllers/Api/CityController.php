<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ShopAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Cities",
 *     description="City management endpoints"
 * )
 */
class CityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/cities",
     *     summary="Get all cities",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by city name, country, or state",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="Filter by country",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="state",
     *         in="query",
     *         description="Filter by state",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cities list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/City")),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * Get all cities
     */
    public function index(Request $request)
    {
        $query = City::query()->active();

        // Search by name or country
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        // Filter by state
        if ($request->has('state')) {
            $query->where('state', $request->state);
        }

        $cities = $query->orderBy('name')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cities/{city}",
     *     summary="Get single city with shops",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         required=true,
     *         description="City ID or slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City details with shops",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/City")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="City not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="City not found")
     *         )
     *     )
     * )
     * Get single city with shops
     */
    public function show(City $city)
    {
        if (!$city->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ], 404);
        }

        $city->load(['activeShops.category', 'activeShops.user']);

        // Get shop categories in this city
        $categories = $city->activeShops()
            ->with('category')
            ->get()
            ->pluck('category')
            ->unique('id')
            ->values();

        // Get basic statistics
        $totalShops = $city->shops()->count();
        $activeShops = $city->activeShops()->count();
        $featuredShops = $city->shops()->where('is_featured', true)->where('is_active', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'city' => $city,
                'categories' => $categories,
                'shops_count' => $activeShops,
                'theme_config' => $city->theme_config,
                'statistics' => [
                    'total_shops' => $totalShops,
                    'active_shops' => $activeShops,
                    'featured_shops_count' => $featuredShops
                ]
            ]
        ]);
    }

    /**
     * Get optimized cities list for selection modal
     * Ultra-fast endpoint with minimal data and caching
     */
    public function forSelection(Request $request)
    {
        $cities = \Illuminate\Support\Facades\Cache::remember('cities_selection_modal', 1800, function () {
            return City::select(['id', 'name', 'slug', 'state', 'country'])
                ->where('is_active', true)
                ->withCount(['shops as shops_count' => function ($query) {
                    $query->where('is_active', true)->where('is_verified', true);
                }])
                ->orderByDesc('shops_count')
                ->orderBy('name')
                ->limit(50)
                ->get()
                ->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name' => $city->name,
                        'slug' => $city->slug,
                        'state' => $city->state,
                        'country' => $city->country,
                        'shops_count' => $city->shops_count ?? 0,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'cities' => $cities
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cities/{city}/featured-shops",
     *     summary="Get featured shops for a city",
     *     description="Returns paginated list of featured shops for a specific city",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         description="City ID or slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of shops per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Featured shops list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="shops", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="slug", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="rating", type="number", format="float"),
     *                     @OA\Property(property="review_count", type="integer"),
     *                     @OA\Property(property="featured_until", type="string", format="date-time"),
     *                     @OA\Property(property="category", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="icon", type="string")
     *                     )
     *                 )),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="City not found")
     * )
     */
    public function featuredShops(Request $request, City $city)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $shops = $city->shops()
            ->activeFeatured()
            ->with(['category:id,name,icon', 'city:id,name'])
            ->paginate($limit);

        // Track featured shops view
        if ($shops->isNotEmpty()) {
            ShopAnalytics::track(
                $shops->first()->id,
                'featured_shops_view',
                Auth::id(),
                [
                    'city_id' => $city->id,
                    'total_results' => $shops->total(),
                    'page' => $shops->currentPage(),
                    'source' => 'api'
                ]
            );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'shops' => $shops->items(),
                'total' => $shops->total(),
                'current_page' => $shops->currentPage(),
                'last_page' => $shops->lastPage(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cities/{city}/latest-shops",
     *     summary="Get latest shops for a city",
     *     description="Returns paginated list of recently added shops for a specific city",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         description="City ID or slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of shops per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days to look back for latest shops",
     *         @OA\Schema(type="integer", default=30)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Latest shops list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="shops", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="slug", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="rating", type="number", format="float"),
     *                     @OA\Property(property="review_count", type="integer"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="category", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="icon", type="string")
     *                     )
     *                 )),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="City not found")
     * )
     */
    public function latestShops(Request $request, City $city)
    {

        
        $limit = $request->input('limit', 15);

        $shops = $city->shops()
            ->where('is_active', true)
            ->with(['category:id,name,icon'])
            ->select(['id', 'name', 'slug', 'description', 'category_id', 'city_id', 'rating', 'review_count', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        // Track latest shops view
        if ($shops->isNotEmpty()) {
            ShopAnalytics::track(
                $shops->first()->id,
                'latest_shops_view',
                Auth::id(),
                [
                    'city_id' => $city->id,
                    'total_results' => $shops->total(),
                    'page' => $shops->currentPage(),
                    'source' => 'api'
                ]
            );
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'shops' => $shops->items(),
                'total' => $shops->total(),
                'current_page' => $shops->currentPage(),
                'last_page' => $shops->lastPage(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cities/{city}/statistics",
     *     summary="Get city statistics",
     *     description="Returns aggregated statistics for a city (cached for 1 hour)",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         description="City ID or slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_shops", type="integer", example=150),
     *                 @OA\Property(property="active_shops", type="integer", example=142),
     *                 @OA\Property(property="total_categories", type="integer", example=25),
     *                 @OA\Property(property="total_reviews", type="integer", example=1250),
     *                 @OA\Property(property="average_rating", type="number", format="float", example=4.35),
     *                 @OA\Property(property="new_shops_this_month", type="integer", example=8),
     *                 @OA\Property(property="featured_shops_count", type="integer", example=12)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="City not found")
     * )
     */
    public function statistics(City $city)
    {
        $cacheKey = "city_statistics_{$city->id}";
        
        $stats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($city) {
            $totalShops = $city->shops()->count();
            $activeShops = $city->shops()->active()->count();
            $featuredShops = $city->shops()->where('is_featured', true)->count();
            
            // Get categories count
            $categoriesCount = $city->shops()
                ->distinct('category_id')
                ->whereNotNull('category_id')
                ->count('category_id');
            
            // Get total reviews
            $totalReviews = $city->shops()->sum('review_count');
            
            // Calculate average rating
            $avgRating = $city->shops()
                ->where('review_count', '>', 0)
                ->avg('rating');
            
            // Get new shops this month
            $newShopsThisMonth = $city->shops()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            return [
                'total_shops' => $totalShops,
                'active_shops' => $activeShops,
                'total_categories' => $categoriesCount,
                'total_reviews' => (int) $totalReviews,
                'average_rating' => round($avgRating, 2),
                'new_shops_this_month' => $newShopsThisMonth,
                'featured_shops_count' => $featuredShops,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cities/{city}/banners",
     *     summary="Get city promotional banners",
     *     description="Returns active promotional banners for a specific city",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         description="City ID or slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City banners",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="banners", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string", example="Summer Sale 2024"),
     *                     @OA\Property(property="description", type="string", example="Get up to 50% off on selected items"),
     *                     @OA\Property(property="image", type="string", example="https://example.com/banner.jpg"),
     *                     @OA\Property(property="link_type", type="string", enum={"internal", "external", "none"}, example="internal"),
     *                     @OA\Property(property="link_url", type="string", example="/shops/summer-deals"),
     *                     @OA\Property(property="start_date", type="string", format="date-time"),
     *                     @OA\Property(property="end_date", type="string", format="date-time"),
     *                     @OA\Property(property="priority", type="integer", example=10)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="City not found")
     * )
     */
    public function banners(City $city)
    {
        $banners = $city->activeBanners()
            ->select(['id', 'title', 'description', 'image', 'link_type', 'link_url', 'start_date', 'end_date', 'priority'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cities/{city}/services",
     *     summary="Get services in a city with sorting options",
     *     description="Returns all user services in a specific city with various sorting and filtering options",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         description="City ID or slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order",
     *         @OA\Schema(
     *             type="string",
     *             enum={"latest", "oldest", "rating_high", "rating_low", "price_low", "price_high", "featured", "name_asc", "name_desc"},
     *             default="latest"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by service category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pricing_type",
     *         in="query",
     *         description="Filter by pricing type",
     *         @OA\Schema(type="string", enum={"fixed", "hourly", "per_km", "negotiable"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in service title and description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City services list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="services", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="slug", type="string"),
     *                     @OA\Property(property="pricing_type", type="string"),
     *                     @OA\Property(property="base_price", type="number"),
     *                     @OA\Property(property="rating", type="number", format="float"),
     *                     @OA\Property(property="is_verified", type="boolean"),
     *                     @OA\Property(property="is_featured", type="boolean"),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     ),
     *                     @OA\Property(property="serviceCategory", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="icon", type="string")
     *                     )
     *                 )),
     *                 @OA\Property(property="meta", type="object",
     *                     @OA\Property(property="total", type="integer"),
     *                     @OA\Property(property="per_page", type="integer"),
     *                     @OA\Property(property="current_page", type="integer"),
     *                     @OA\Property(property="last_page", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="City not found")
     * )
     */
    public function services(Request $request, City $city)
    {
        if (!$city->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ], 404);
        }

        $query = \App\Models\UserService::query()
            ->with(['user', 'city', 'serviceCategory'])
            ->where('city_id', $city->id)
            ->where('is_active', true)
            ->where('is_verified', true);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('service_category_id', $request->category_id);
        }

        // Filter by pricing type
        if ($request->filled('pricing_type')) {
            $query->where('pricing_type', $request->pricing_type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'featured':
                $query->orderByRaw('is_featured DESC, featured_until DESC NULLS LAST')
                      ->orderBy('rating', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->input('per_page', 15);
        $services = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'services' => \App\Http\Resources\UserServiceResource::collection($services),
                'meta' => [
                    'total' => $services->total(),
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                ]
            ]
        ]);
    }
}