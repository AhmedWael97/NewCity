<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Shop;
use App\Models\Category;
use App\Services\CityDataService;
use Illuminate\Support\Facades\Cache;

class CityController extends Controller
{
    protected $cityDataService;

    public function __construct(CityDataService $cityDataService)
    {
        $this->cityDataService = $cityDataService;
    }

    /**
     * Display all cities
     */
    public function index()
    {
        $cities = City::active()
            ->withCount(['shops' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('shops_count', 'desc')
            ->get();

        $seoData = [
            'title' => 'جميع المدن - اكتشف المدن',
            'description' => 'استعرض جميع المدن المتاحة واكتشف أفضل المتاجر في كل مدينة',
            'keywords' => 'مدن مصر، دليل المدن، القاهرة، الإسكندرية، الجيزة',
            'canonical' => route('cities.index'),
        ];

        return view('cities.index', compact('cities', 'seoData'));
    }

    /**
     * Show city homepage with overview
     */
    public function show(City $city)
    {
        // Get city statistics
        $stats = $this->cityDataService->getCityStats($city->id);
        
        // Get featured shops in this city
        $featuredShops = $this->cityDataService->getFeaturedShops($city->id, 8);
        
        // Get popular categories in this city
        $popularCategories = $this->cityDataService->getPopularCategories($city->id, 12);
        
        // Get nearby cities
        $nearbyCities = $this->cityDataService->getNearbyCity($city->id, 4);

        // SEO data
        $seoData = [
            'title' => "اكتشف أفضل المتاجر والخدمات في {$city->name} - منصة اكتشف المدن",
            'description' => "تصفح مئات المتاجر المحلية في {$city->name}. اكتشف أفضل العروض والخدمات، واقرأ تقييمات العملاء في {$city->name}.",
            'keywords' => "متاجر {$city->name}, تسوق {$city->name}, دليل المتاجر {$city->name}, خدمات {$city->name}",
            'og_image' => $city->image ? asset('storage/' . $city->image) : asset('images/og-city-default.jpg'),
            'canonical' => route('city.landing', $city->slug),
            'breadcrumbs' => [
                ['name' => 'الرئيسية', 'url' =>url('/')],
                ['name' => 'المدن', 'url' => route('cities.index')],
                ['name' => $city->name, 'url' => route('city.landing', $city->slug)],
            ]
        ];

        return view('city.landing', compact('city', 'stats', 'featuredShops', 'popularCategories', 'nearbyCities', 'seoData'));
    }

    /**
     * Show all shops in a city with enhanced filtering
     */
    public function shops(Request $request, City $city)
    {
        $filters = [
            'search' => $request->get('q'),
            'category_id' => $request->get('category'),
            'min_rating' => $request->get('rating'),
            'latitude' => $request->get('lat'),
            'longitude' => $request->get('lng'),
        ];

        // Build query
        $query = Shop::where('city_id', $city->id)
            ->where('is_active', true)
            ->where('is_verified', true)
            ->with(['category:id,name,icon,slug', 'city:id,name,slug'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        // Apply search filter
        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('description', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('address', 'LIKE', "%{$filters['search']}%");
            });
        }

        // Apply category filter
        if ($filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply rating filter
        if ($filters['min_rating']) {
            $query->having('ratings_avg_rating', '>=', $filters['min_rating']);
        }

        // Apply sorting
        $sort = $request->get('sort', 'featured');
        switch ($sort) {
            case 'rating':
                $query->orderByDesc('ratings_avg_rating');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'featured':
            default:
                $query->orderByDesc('is_featured')
                      ->orderByDesc('ratings_avg_rating');
                break;
        }

        $shops = $query->paginate(20)->withQueryString();

        // Get categories with shop counts for this city
        $categories = Category::whereHas('shops', function ($query) use ($city) {
            $query->where('city_id', $city->id)
                  ->where('is_active', true)
                  ->where('is_verified', true);
        })
        ->withCount(['shops as shops_count' => function ($query) use ($city) {
            $query->where('city_id', $city->id)
                  ->where('is_active', true)
                  ->where('is_verified', true);
        }])
        ->orderByDesc('shops_count')
        ->get();

        $seoData = [
            'title' => "جميع المتاجر في {$city->name} - اكتشف المدن",
            'description' => "استعرض جميع المتاجر المعتمدة في {$city->name}. ابحث، قارن، واقرأ التقييمات لأفضل المتاجر المحلية.",
            'keywords' => "متاجر {$city->name}, دليل متاجر {$city->name}, تسوق {$city->name}",
            'canonical' => route('city.shops.index', $city->slug),
            'breadcrumbs' => [
                ['name' => 'الرئيسية', 'url' =>url('/')],
                ['name' => $city->name, 'url' => route('city.landing', $city->slug)],
                ['name' => 'المتاجر', 'url' => route('city.shops.index', $city->slug)],
            ]
        ];

        return view('city.shops', compact('city', 'shops', 'categories', 'filters', 'seoData'));
    }

    /**
     * Show featured shops in a city
     */
    public function featuredShops(City $city)
    {
        $featuredShops = $this->cityDataService->getFeaturedShops($city->id, 50);

        $seoData = [
            'title' => "المتاجر المميزة في {$city->name} - اكتشف المدن",
            'description' => "اكتشف أفضل المتاجر المميزة والموثوقة في {$city->name}. متاجر معتمدة ومجربة من العملاء.",
            'keywords' => "متاجر مميزة {$city->name}, أفضل متاجر {$city->name}",
            'canonical' => route('city.shops.featured', $city->slug),
        ];

        return view('city.featured-shops', compact('city', 'featuredShops', 'seoData'));
    }

    /**
     * Show shops by category in a city
     */
    public function shopsByCategory(City $city, Category $category)
    {
        $shops = $this->cityDataService->getCityShops($city->id, ['category_id' => $category->id])
            ->paginate(20);

        $seoData = [
            'title' => "{$category->name} في {$city->name} - اكتشف المدن",
            'description' => "اكتشف أفضل متاجر {$category->name} في {$city->name}. قارن الأسعار واقرأ التقييمات.",
            'keywords' => "{$category->name} {$city->name}, متاجر {$category->name}",
            'canonical' => route('city.shops.category', [$city->slug, $category->slug]),
        ];

        return view('city.category-shops', compact('city', 'category', 'shops', 'seoData'));
    }

    /**
     * Show all categories in a city
     */
    public function categories(City $city)
    {
        $categories = $this->cityDataService->getPopularCategories($city->id, 100);

        $seoData = [
            'title' => "فئات المتاجر في {$city->name} - اكتشف المدن",
            'description' => "استعرض جميع فئات المتاجر المتاحة في {$city->name}. من المطاعم إلى الملابس والإلكترونيات.",
            'canonical' => route('city.categories.index', $city->slug),
        ];

        return view('city.categories', compact('city', 'categories', 'seoData'));
    }

    /**
     * Show category page with shops (alias for shopsByCategory)
     */
    public function categoryShops(City $city, Category $category)
    {
        return $this->shopsByCategory($city, $category);
    }

    /**
     * Search within a city - SMART SEARCH with category matching and suggestions
     */
    public function search(Request $request, City $city)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->route('city.landing', $city->slug);
        }

        // Get smart search results with category matching
        $searchResult = $this->cityDataService->searchShops($query, $city->id, [
            'include_products' => true,
            'include_services' => true
        ]);

        $shops = $searchResult['query']->paginate(20);
        $matchedCategory = $searchResult['matched_category'];
        
        // If no results found, get suggestion shops
        $suggestionShops = null;
        if ($shops->isEmpty()) {
            $suggestionShops = $this->cityDataService->getSuggestionShops($city->id, 3);
        }

        $seoData = [
            'title' => "نتائج البحث عن \"{$query}\" في {$city->name} - اكتشف المدن",
            'description' => "نتائج البحث عن \"{$query}\" في المتاجر المحلية في {$city->name}",
            'canonical' => route('city.search', $city->slug) . '?' . http_build_query(['q' => $query]),
            'noindex' => strlen($query) < 3,
        ];

        return view('city.search', compact('city', 'query', 'shops', 'matchedCategory', 'suggestionShops', 'seoData'));
    }

    /**
     * Generate XML sitemap for cities
     */
    public function sitemap()
    {
        $cities = Cache::remember('sitemap_cities', 86400, function () {
            return City::active()
                ->select(['slug', 'updated_at'])
                ->get();
        });

        return response()->view('sitemaps.cities', compact('cities'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Store city suggestion from users
     */
    public function storeSuggestion(Request $request)
    {
        $validated = $request->validate([
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

        try {
            // Create city suggestion in database
            \App\Models\CitySuggestion::create([
                'city_name' => $validated['city_name'],
                'phone' => $validated['phone'],
                'group_url' => $validated['group_url'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'شكراً لك! تم إرسال اقتراحك بنجاح وسنقوم بمراجعته قريباً.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ الاقتراح. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }
}
