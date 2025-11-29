<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Http\Resources\NewsCategoryResource;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="News",
 *     description="News and articles management endpoints"
 * )
 */
class NewsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/news",
     *     summary="Get news articles list",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in title, description, and content",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort by: latest (default), popular, oldest",
     *         @OA\Schema(type="string", enum={"latest", "popular", "oldest"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News articles list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="news", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="slug", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="thumbnail_url", type="string"),
     *                     @OA\Property(property="published_at", type="string", format="date-time"),
     *                     @OA\Property(property="views_count", type="integer"),
     *                     @OA\Property(property="reading_time", type="integer"),
     *                     @OA\Property(property="category", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="slug", type="string")
     *                     ),
     *                     @OA\Property(property="city", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 )),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $query = News::with(['category', 'city'])
            ->active()
            ->latest();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by city
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'popular':
                    $query->orderBy('views_count', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('published_at', 'asc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        }

        $news = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'news' => NewsResource::collection($news),
                'total' => $news->total(),
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/news/{slug}",
     *     summary="Get single news article by slug",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="News article slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News article details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="thumbnail_url", type="string"),
     *                 @OA\Property(property="images_url", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="published_at", type="string", format="date-time"),
     *                 @OA\Property(property="views_count", type="integer"),
     *                 @OA\Property(property="reading_time", type="integer"),
     *                 @OA\Property(property="category", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="slug", type="string")
     *                 ),
     *                 @OA\Property(property="city", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(property="related_news", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="slug", type="string"),
     *                     @OA\Property(property="thumbnail_url", type="string")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="News article not found")
     * )
     */
    public function show($slug)
    {
        $news = News::with(['category', 'city'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Increment views
        $news->incrementViews();

        // Get related news
        $relatedNews = News::with(['category'])
            ->active()
            ->where('id', '!=', $news->id)
            ->when($news->category_id, function ($query) use ($news) {
                $query->where('category_id', $news->category_id);
            })
            ->latest()
            ->limit(4)
            ->get();

        $newsResource = new NewsResource($news);
        $newsData = $newsResource->toArray(request());
        $newsData['related_news'] = $relatedNews->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'thumbnail_url' => $item->thumbnail_url,
                'published_at' => $item->published_at,
                'views_count' => $item->views_count,
                'reading_time' => $item->reading_time,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $newsData
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/news/categories/list",
     *     summary="Get all news categories",
     *     tags={"News"},
     *     @OA\Response(
     *         response=200,
     *         description="News categories list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="news_count", type="integer")
     *             ))
     *         )
     *     )
     * )
     */
    public function categories()
    {
        $categories = NewsCategory::where('is_active', true)
            ->withCount(['activeNews'])
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => NewsCategoryResource::collection($categories)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/news/latest",
     *     summary="Get latest news articles",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of articles to return",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Latest news articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="thumbnail_url", type="string"),
     *                 @OA\Property(property="published_at", type="string", format="date-time")
     *             ))
     *         )
     *     )
     * )
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $query = News::with(['category', 'city'])
            ->active()
            ->latest();

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        $news = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => NewsResource::collection($news)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/news/featured",
     *     summary="Get featured news articles (most viewed)",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of articles to return",
     *         @OA\Schema(type="integer", default=5)
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Featured news articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="thumbnail_url", type="string"),
     *                 @OA\Property(property="views_count", type="integer")
     *             ))
     *         )
     *     )
     * )
     */
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 5);
        
        $query = News::with(['category', 'city'])
            ->active()
            ->orderBy('views_count', 'desc');

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        $news = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => NewsResource::collection($news)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/news/category/{slug}",
     *     summary="Get news articles by category slug",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Category slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News articles by category",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="category", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="slug", type="string")
     *                 ),
     *                 @OA\Property(property="news", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="slug", type="string")
     *                 )),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function byCategory($slug, Request $request)
    {
        $category = NewsCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $perPage = $request->get('per_page', 15);
        
        $news = News::with(['category', 'city'])
            ->active()
            ->where('category_id', $category->id)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new NewsCategoryResource($category),
                'news' => NewsResource::collection($news),
                'total' => $news->total(),
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cities/{city_id}/news",
     *     summary="Get news articles for a specific city",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="path",
     *         description="City ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News articles for city",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="news", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string")
     *                 )),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function byCity($cityId, Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        $news = News::with(['category', 'city'])
            ->active()
            ->where('city_id', $cityId)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'news' => NewsResource::collection($news),
                'total' => $news->total(),
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
            ]
        ]);
    }
}
