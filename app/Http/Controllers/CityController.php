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
            'canonical' => route('city.show', $city->slug),
            'breadcrumbs' => [
                ['name' => 'الرئيسية', 'url' =>url('/')],
                ['name' => 'المدن', 'url' => route('cities.index')],
                ['name' => $city->name, 'url' => route('city.show', $city->slug)],
            ]
        ];

        return view('city.show', compact('city', 'stats', 'featuredShops', 'popularCategories', 'nearbyCities', 'seoData'));
    }

    /**
     * Show all shops in a city
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

        $shops = $this->cityDataService->getCityShops($city->id, $filters)
            ->paginate(20);

        $categories = $this->cityDataService->getPopularCategories($city->id);

        $seoData = [
            'title' => "جميع المتاجر في {$city->name} - اكتشف المدن",
            'description' => "استعرض جميع المتاجر المعتمدة في {$city->name}. ابحث، قارن، واقرأ التقييمات لأفضل المتاجر المحلية.",
            'keywords' => "متاجر {$city->name}, دليل متاجر {$city->name}, تسوق {$city->name}",
            'canonical' => route('city.shops.index', $city->slug),
            'breadcrumbs' => [
                ['name' => 'الرئيسية', 'url' =>url('/')],
                ['name' => $city->name, 'url' => route('city.show', $city->slug)],
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
     * Search within a city
     */
    public function search(Request $request, City $city)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->route('city.show', $city->slug);
        }

        $shops = $this->cityDataService->searchShops($query, $city->id, [
            'include_products' => true,
            'include_services' => true
        ])->paginate(20);

        $seoData = [
            'title' => "نتائج البحث عن \"{$query}\" في {$city->name} - اكتشف المدن",
            'description' => "نتائج البحث عن \"{$query}\" في المتاجر المحلية في {$city->name}",
            'canonical' => route('city.search', $city->slug) . '?' . http_build_query(['q' => $query]),
            'noindex' => strlen($query) < 3,
        ];

        return view('city.search', compact('city', 'query', 'shops', 'seoData'));
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
}
