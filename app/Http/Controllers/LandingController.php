<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Shop;
use App\Models\Category;
use App\Models\News;
use App\Services\CityDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    protected $cityDataService;

    public function __construct(CityDataService $cityDataService)
    {
        $this->cityDataService = $cityDataService;
    }

    /**
     * Display the landing page with cached city data - OPTIMIZED
     */
    public function index(Request $request)
    {
        $cityContext = $request->get('cityContext', []);
        $selectedCity = $cityContext['selected_city'] ?? null;
        
        // For authenticated users, check their preferred city
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->preferred_city_id && !$selectedCity) {
                // Redirect to user's preferred city landing page
                $preferredCity = Cache::remember("user_city_{$user->preferred_city_id}", 3600, function () use ($user) {
                    return City::where('id', $user->preferred_city_id)
                               ->where('is_active', true)
                               ->select(['id', 'name', 'slug'])
                               ->first();
                });
                
                if ($preferredCity) {
                    session([
                        'selected_city' => $preferredCity->slug,
                        'selected_city_name' => $preferredCity->name,
                        'selected_city_id' => $preferredCity->id
                    ]);
                    
                    return redirect()->route('city.landing', ['city' => $preferredCity->slug]);
                }
            }
        }

        // Load lightweight cities data (just for display, not for modal)
        // Modal will load its own cities via AJAX
        $cities = Cache::remember('landing_cities_basic', 1800, function () {
            return City::select(['id', 'name', 'slug', 'image'])
                ->where('is_active', true)
                ->withCount(['shops as active_shops_count' => function ($query) {
                    $query->where('is_active', true)->where('is_verified', true);
                }])
                ->orderByDesc('active_shops_count')
                ->limit(20) // Limit to top 20 cities for homepage display
                ->get();
        });
        
        // Get city-specific statistics (cached)
        $stats = Cache::remember('landing_stats_' . ($selectedCity?->id ?? 'all'), 1800, function () use ($selectedCity) {
            return $this->cityDataService->getCityStats($selectedCity?->id);
        });
        
        // Get featured shops for the selected city or globally (cached)
        $featuredShops = Cache::remember('landing_featured_' . ($selectedCity?->id ?? 'all'), 1800, function () use ($selectedCity) {
            return $this->cityDataService->getFeaturedShops($selectedCity?->id, 6);
        });
        
        // Get popular categories (cached)
        $popularCategories = Cache::remember('landing_categories_' . ($selectedCity?->id ?? 'all'), 1800, function () use ($selectedCity) {
            return $this->cityDataService->getPopularCategories($selectedCity?->id, 8);
        });

        // Get a sample shop for hero section (cached)
        $sampleShop = Cache::remember('sample_shop_' . ($selectedCity?->slug ?? 'all'), 3600, function () use ($selectedCity) {
            $query = Shop::select(['id', 'name', 'city_id'])
                ->with('city:id,name')
                ->where('is_active', true)
                ->where('is_verified', true);
                
            if ($selectedCity) {
                $query->where('city_id', $selectedCity->id);
            }
            
            return $query->inRandomOrder()->first();
        });

        // SEO data - make it dynamic based on selected city
        $seoData = $this->generateSeoData($selectedCity);
        
        return view('welcome', compact(
            'cities', 
            'stats', 
            'seoData', 
            'sampleShop', 
            'featuredShops', 
            'popularCategories',
            'cityContext'
        ));
    }    /**
     * Generate dynamic SEO data based on selected city
     */
    private function generateSeoData(?City $selectedCity = null): array
    {
        if ($selectedCity) {
            return [
                'title' => "اكتشف أفضل المتاجر والخدمات في {$selectedCity->name} - منصة اكتشف المدن",
                'description' => "تصفح مئات المتاجر المحلية في {$selectedCity->name}. اكتشف أفضل العروض والخدمات، واقرأ تقييمات العملاء، واحصل على أفضل الصفقات في {$selectedCity->name}.",
                'keywords' => "متاجر {$selectedCity->name}, تسوق {$selectedCity->name}, دليل المتاجر {$selectedCity->name}, خدمات {$selectedCity->name}, عروض {$selectedCity->name}",
                'og_image' => $selectedCity->image ? asset('storage/' . $selectedCity->image) : asset('images/og-discover-cities.jpg'),
                'canonical' => route('city.landing', ['city' => $selectedCity->slug]),
                'city_name' => $selectedCity->name,
                'breadcrumbs' => [
                    ['name' => 'الرئيسية', 'url' => route('home')],
                    ['name' => $selectedCity->name, 'url' => route('city.landing', ['city' => $selectedCity->slug])],
                ]
            ];
        }

        return [
            'title' => 'اكتشف المدن - منصة استكشاف المتاجر المحلية في مصر',
            'description' => 'اكتشف أفضل المتاجر والخدمات المحلية في مدينتك. ابحث، اقرأ التقييمات، واحصل على أفضل العروض من آلاف المتاجر المعتمدة في جميع أنحاء جمهورية مصر العربية.',
            'keywords' => 'متاجر مصرية، تسوق محلي، دليل المتاجر، القاهرة، الإسكندرية، الجيزة، العاصمة الإدارية، المدن الجديدة، تقييمات المتاجر، عروض وخصومات',
            'og_image' => asset('images/og-discover-cities.jpg'),
            'canonical' => url('/'),
            'breadcrumbs' => [
                ['name' => 'الرئيسية', 'url' => route('home')]
            ]
        ];
    }

    /**
     * Get city data for AJAX requests
     */
    public function getCityData($citySlug)
    {
        $city = Cache::remember("city_data_{$citySlug}", 1800, function () use ($citySlug) {
            return City::where('slug', $citySlug)
                ->where('is_active', true)
                ->withCount(['shops as active_shops_count' => function ($query) {
                    $query->where('is_active', true)->where('is_verified', true);
                }])
                ->first();
        });

        if (!$city) {
            return response()->json(['error' => 'المدينة غير موجودة'], 404);
        }

        return response()->json([
            'id' => $city->id,
            'name' => $city->name,
            'slug' => $city->slug,
            'shops_count' => $city->active_shops_count,
            'image' => $city->image ? asset('storage/' . $city->image) : asset('images/default-city.jpg'),
        ]);
    }

    /**
     * Enhanced search with city context
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $cityContext = $request->get('cityContext', []);
        $selectedCityId = $cityContext['selected_city_id'] ?? null;
        $cityId = $request->get('city', $selectedCityId);
        $category = $request->get('category', '');
        
        if (empty($query)) {
            return redirect()->route('home');
        }

        // Use service for search
        $shops = $this->cityDataService->searchShops($query, $selectedCityId, [
            'include_products' => true,
            'include_services' => true
        ])->paginate(20);

        // Get search suggestions
        $suggestions = $this->getSearchSuggestions($query, $selectedCityId);

        // Get all cities for filter dropdown
        $cities = City::active()
            ->select(['id', 'name', 'slug'])
            ->orderBy('name')
            ->get();

        $selectedCity = $cityContext['selected_city'] ?? null;
        $seoData = [
            'title' => "نتائج البحث عن \"{$query}\"" . ($selectedCity ? " في {$selectedCity->name}" : '') . " - اكتشف المدن",
            'description' => "اكتشف المتاجر والخدمات المتعلقة بـ \"{$query}\"" . ($selectedCity ? " في {$selectedCity->name}" : '') . ". تصفح النتائج واقرأ التقييمات.",
            'keywords' => $query . ($selectedCity ? ", {$selectedCity->name}" : '') . ", متاجر, تسوق, بحث, دليل المتاجر",
            'canonical' => request()->url() . '?' . http_build_query($request->only(['q'])),
            'noindex' => strlen($query) < 3, // Don't index very short searches
        ];

        // Prepare stats for the view
        $stats = [
            'total_results' => $shops->total(),
            'city_filter' => $selectedCity?->name ?? null,
            'category_filter' => $category ?: null,
        ];

        // Alias shops as results for the view
        $results = $shops;

        return view('search-results', compact('shops', 'query', 'suggestions', 'seoData', 'cityContext', 'stats', 'cities', 'cityId', 'category', 'results'));
    }

    /**
     * Get search suggestions for autocomplete and other helper methods
     */
    private function getSearchSuggestions(string $query, ?int $cityId = null): array
    {
        $cacheKey = "search_suggestions_{$query}_" . ($cityId ?? 'all');
        
        return Cache::remember($cacheKey, 300, function () use ($query, $cityId) {
            $suggestions = [];
            
            // Shop suggestions
            $shopQuery = Shop::where('is_active', true)
                ->where('is_verified', true)
                ->where('name', 'LIKE', "%{$query}%");
                
            if ($cityId) {
                $shopQuery->where('city_id', $cityId);
            }
            
            $suggestions['shops'] = $shopQuery->limit(5)->pluck('name');
            
            // Category suggestions
            $suggestions['categories'] = Category::where('is_active', true)
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->pluck('name');
                
            return $suggestions;
        });
    }

    /**
     * Get search suggestions for autocomplete (API endpoint)
     */
    public function searchSuggestions(Request $request)
    {
        $term = $request->input('term');
        $cityContext = $request->get('cityContext', []);
        $cityId = $cityContext['selected_city_id'] ?? null;
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $suggestions = $this->getSearchSuggestions($term, $cityId);
        return response()->json($suggestions);
    }

    /**
     * Show city selection modal
     */
    public function selectCity()
    {
        $cities = City::active()
            ->withCount(['shops' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('shops_count', 'desc')
            ->get();

        return view('select-city', compact('cities'));
    }

    /**
     * Set selected city in session - OPTIMIZED VERSION
     */
    public function setCity(Request $request)
    {
        // Quick validation
        $validated = $request->validate([
            'city_slug' => 'required|string|max:255'
        ]);

        // Get city with minimal data needed - OPTIMIZED QUERY with caching
        $city = Cache::remember("city_quick_{$validated['city_slug']}", 3600, function () use ($validated) {
            return City::where('slug', $validated['city_slug'])
                       ->where('is_active', true)
                       ->select(['id', 'name', 'slug'])
                       ->first();
        });
                   
        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'المدينة المطلوبة غير موجودة'
            ], 404);
        }
        
        // Store in session quickly
        session([
            'selected_city' => $city->slug,
            'selected_city_name' => $city->name,
            'selected_city_id' => $city->id
        ]);
        
        // Save to user preferences if authenticated (non-blocking)
        if (auth()->check()) {
            try {
                auth()->user()->update([
                    'preferred_city_id' => $city->id,
                ]);
            } catch (\Exception $e) {
                // Log but don't fail the request
                \Log::error('Failed to update user city preference: ' . $e->getMessage());
            }
        }
        
        // Clear skip flag
        session()->forget('city_selection_skipped');

        return response()->json([
            'success' => true,
            'message' => "تم اختيار {$city->name} بنجاح",
            'redirect' => route('city.landing', ['city' => $city->slug]),
            'city' => [
                'name' => $city->name,
                'slug' => $city->slug,
                'id' => $city->id
            ]
        ]);
    }

    /**
     * Skip city selection - user wants to browse all cities
     */
    public function skipCitySelection(Request $request)
    {
        session(['city_selection_skipped' => true]);
        session()->forget(['selected_city', 'selected_city_name', 'selected_city_id']);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تخطي اختيار المدينة'
        ]);
    }

    /**
     * Show city-specific landing page
     */
    public function cityLanding(City $city)
    {
        // Update session to selected city
        session([
            'selected_city' => $city->slug,
            'selected_city_name' => $city->name,
            'selected_city_id' => $city->id
        ]);

        // Get city-specific data
        $cityContext = [
            'selected_city' => $city,
            'selected_city_id' => $city->id,
            'selected_city_name' => $city->name,
            'selected_city_slug' => $city->slug,
            'is_city_selected' => true,
            'should_show_modal' => false,
        ];

        // Get city statistics
        $stats = $this->cityDataService->getCityStats($city->id) ?? [
            'total_shops' => 0,
            'active_shops' => 0,
            'featured_shops' => 0,
            'total_categories' => 0,
        ];

        // Get total shops count for this city
        $totalShopsCount = Cache::remember("city_shops_count_{$city->slug}", 3600, function () use ($city) {
            return Shop::where('city_id', $city->id)
                ->where('is_active', true)
                ->where('is_verified', true)
                ->count();
        });
        
        // Get only 6 shops for landing page display (featured and top-rated)
        $cacheKey = "city_landing_shops_{$city->slug}";
        $lastCheck = Cache::get($cacheKey . '_last_check');
        $latestShopUpdate = Shop::where('city_id', $city->id)->max('updated_at');
        
        if ($lastCheck && $latestShopUpdate && $latestShopUpdate > $lastCheck) {
            Cache::forget($cacheKey);
        }
        
        $shops = Cache::remember($cacheKey, 3600, function () use ($city) {
            return Shop::where('city_id', $city->id)
                ->where('is_active', true)
                ->where('is_verified', true)
                ->with(['category:id,name,icon,slug', 'city:id,name,slug'])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->orderByDesc('is_featured')
                ->orderByDesc('ratings_avg_rating')
                ->limit(12)
                ->get();
        });
        
        Cache::put($cacheKey . '_last_check', now(), 3600);
        
        // Get categories with their shops for this city (for sidebar)
        $categoryCacheKey = "city_categories_shops_{$city->slug}";
        $lastCategoryCheck = Cache::get($categoryCacheKey . '_last_check');
        
        if ($lastCategoryCheck && $latestShopUpdate && $latestShopUpdate > $lastCategoryCheck) {
            Cache::forget($categoryCacheKey);
        }
        
        $categoriesWithShops = Cache::remember($categoryCacheKey, 3600, function () use ($city) {
            return Category::whereHas('shops', function ($query) use ($city) {
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
        });
        
        Cache::put($categoryCacheKey . '_last_check', now(), 3600);

        // Get service categories with services for this city
        $serviceCacheKey = "city_service_categories_{$city->slug}";
        $lastServiceCheck = Cache::get($serviceCacheKey . '_last_check');
        $latestServiceUpdate = \App\Models\UserService::where('city_id', $city->id)->max('updated_at');
        
        if ($lastServiceCheck && $latestServiceUpdate && $latestServiceUpdate > $lastServiceCheck) {
            Cache::forget($serviceCacheKey);
        }
        
        $serviceCategoriesWithServices = Cache::remember($serviceCacheKey, 3600, function () use ($city) {
            return \App\Models\ServiceCategory::whereHas('userServices', function ($query) use ($city) {
                $query->where('city_id', $city->id)
                      ->where('is_active', true);
            })
            ->withCount(['userServices as services_count' => function ($query) use ($city) {
                $query->where('city_id', $city->id)
                      ->where('is_active', true);
            }])
            ->with(['userServices' => function ($query) use ($city) {
                $query->where('city_id', $city->id)
                      ->where('is_active', true)
                      ->with('user')
                      ->latest()
                      ->take(4);
            }])
            ->orderByDesc('services_count')
            ->limit(3)
            ->get();
        });
        
        Cache::put($serviceCacheKey . '_last_check', now(), 3600);

        // Get all cities for modal
        $cities = $this->cityDataService->getCitiesForSelection();

        // Generate SEO data for this city
        $seoData = [
            'title' => "اكتشف أفضل المتاجر في {$city->name} - منصة اكتشف المدن",
            'description' => "استعرض {$stats['total_shops']} متجر في {$city->name}. اكتشف أفضل المطاعم، المتاجر، والخدمات مع تقييمات العملاء الحقيقية.",
            'keywords' => "متاجر {$city->name}, تسوق {$city->name}, دليل {$city->name}, خدمات {$city->name}",
            'canonical' => route('city.landing', ['city' => $city->slug]),
        ];

        // Get latest news (4 items, prioritize city-specific news)
        $latestNews = Cache::remember("city_latest_news_{$city->slug}", 1800, function () use ($city) {
            return News::with(['category'])
                ->active()
                ->where(function($query) use ($city) {
                    $query->where('city_id', $city->id)
                          ->orWhereNull('city_id');
                })
                ->latest()
                ->limit(4)
                ->get();
        });

        return view('city-landing', compact(
            'city',
            'cityContext',
            'stats',
            'shops',
            'totalShopsCount',
            'categoriesWithShops',
            'serviceCategoriesWithServices',
            'cities',
            'seoData',
            'latestNews'
        ));
    }

    /**
     * Show city services page
     */
    public function cityServices(Request $request, City $city)
    {
        // Update session to selected city
        session([
            'selected_city' => $city->slug,
            'selected_city_name' => $city->name,
            'selected_city_id' => $city->id
        ]);

        // Get city-specific data
        $cityContext = [
            'selected_city' => $city,
            'selected_city_id' => $city->id,
            'selected_city_name' => $city->name,
            'selected_city_slug' => $city->slug,
            'is_city_selected' => true,
            'should_show_modal' => false,
        ];

        // Build query for services
        $query = \App\Models\UserService::query()
            ->with(['user', 'serviceCategory'])
            ->where('city_id', $city->id)
            ->where('is_active', true)
            ->where('is_verified', true);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('service_category_id', $request->category);
        }

        // Pricing type filter
        if ($request->filled('pricing_type')) {
            $query->where('pricing_type', $request->pricing_type);
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'featured':
                $query->orderByRaw('is_featured DESC, featured_until DESC NULLS LAST');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Get paginated services
        $services = $query->paginate(24);

        // Get service categories for filter
        $serviceCategories = \App\Models\ServiceCategory::whereHas('userServices', function ($q) use ($city) {
            $q->where('city_id', $city->id)
              ->where('is_active', true)
              ->where('is_verified', true);
        })
        ->withCount(['userServices' => function ($q) use ($city) {
            $q->where('city_id', $city->id)
              ->where('is_active', true)
              ->where('is_verified', true);
        }])
        ->where('is_active', true)
        ->orderBy('name_ar')
        ->get();

        // Get all cities for modal
        $cities = $this->cityDataService->getCitiesForSelection();

        // Generate SEO data
        $seoData = [
            'title' => "خدمات محلية في {$city->name} - منصة اكتشف المدن",
            'description' => "اكتشف أفضل الخدمات المحلية في {$city->name}. سباكة، كهرباء، نجارة، صيانة وأكثر من مقدمي خدمات موثوقين.",
            'keywords' => "خدمات {$city->name}, سباكة {$city->name}, كهرباء {$city->name}, صيانة {$city->name}",
            'canonical' => route('city.services', ['city' => $city->slug]),
        ];

        return view('city.services', compact(
            'city',
            'cityContext',
            'services',
            'serviceCategories',
            'cities',
            'seoData'
        ));
    }

    /**
     * Change city (show modal again)
     */
    public function changeCity()
    {
        // Clear city selection
        session()->forget(['selected_city', 'selected_city_name', 'selected_city_id']);
        session()->flash('show_city_modal', true);

        return redirect()->route('landing');
    }
}