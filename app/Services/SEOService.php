<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class SEOService
{
    /**
     * Generate comprehensive SEO data for pages
     */
    public function generateSEOData($page, $data = [])
    {
        $baseUrl = config('app.url');
        $siteName = config('app.name', 'City Guide');
        
        switch ($page) {
            case 'homepage':
                return $this->homepageSEO($data);
            
            case 'city-landing':
                return $this->cityLandingSEO($data);
            
            case 'shop':
                return $this->shopSEO($data);
            
            case 'category':
                return $this->categorySEO($data);
            
            default:
                return $this->defaultSEO($data);
        }
    }
    
    /**
     * Homepage SEO
     */
    private function homepageSEO($data)
    {
        return [
            'title' => 'دليل المدن - اكتشف أفضل المتاجر والخدمات في مدينتك',
            'description' => 'دليل شامل للمتاجر والخدمات في جميع المدن. ابحث عن المتاجر، المطاعم، والخدمات القريبة منك بسهولة.',
            'keywords' => 'دليل المدن, متاجر, خدمات, مطاعم, تسوق, دليل تجاري',
            'canonical' => url('/'),
            'og_title' => 'دليل المدن - اكتشف مدينتك',
            'og_description' => 'دليل شامل للمتاجر والخدمات في جميع المدن',
            'og_image' => asset('images/og-homepage.jpg'),
            'og_type' => 'website',
            'schema' => $this->generateWebsiteSchema(),
            'robots' => 'index,follow',
            'hreflang' => 'ar',
        ];
    }
    
    /**
     * City Landing SEO
     */
    private function cityLandingSEO($data)
    {
        $city = $data['city'] ?? null;
        $cityName = $city ? $city->name : 'المدينة';
        
        return [
            'title' => "دليل {$cityName} - أفضل المتاجر والخدمات في {$cityName}",
            'description' => "اكتشف أفضل المتاجر والخدمات في {$cityName}. دليل شامل للمطاعم، المحلات، والخدمات المتنوعة في {$cityName}.",
            'keywords' => "{$cityName}, متاجر {$cityName}, خدمات {$cityName}, دليل {$cityName}, تسوق {$cityName}",
            'canonical' => url("/city/" . ($city->slug ?? 'city')),
            'og_title' => "دليل {$cityName} - اكتشف مدينتك",
            'og_description' => "أفضل المتاجر والخدمات في {$cityName}",
            'og_image' => $city && $city->image ? asset("storage/{$city->image}") : asset('images/og-city.jpg'),
            'og_type' => 'website',
            'schema' => $this->generateCitySchema($city),
            'robots' => 'index,follow',
            'hreflang' => 'ar',
        ];
    }
    
    /**
     * Shop SEO
     */
    private function shopSEO($data)
    {
        $shop = $data['shop'] ?? null;
        $shopName = $shop ? $shop->name : 'المتجر';
        $cityName = $shop && $shop->city ? $shop->city->name : '';
        
        return [
            'title' => "{$shopName}" . ($cityName ? " - {$cityName}" : '') . " | دليل المتاجر",
            'description' => $shop && $shop->description ? 
                substr($shop->description, 0, 160) : 
                "تعرف على {$shopName} واستعرض المنتجات والخدمات المتاحة",
            'keywords' => "{$shopName}, {$cityName}, متجر, خدمات, منتجات",
            'canonical' => url("/shop/" . ($shop->slug ?? 'shop')),
            'og_title' => $shopName,
            'og_description' => $shop && $shop->description ? substr($shop->description, 0, 200) : "تعرف على {$shopName}",
            'og_image' => $shop && $shop->getFirstImageAttribute() ? $shop->getFirstImageAttribute() : asset('images/og-shop.jpg'),
            'og_type' => 'business.business',
            'schema' => $this->generateShopSchema($shop),
            'robots' => 'index,follow',
            'hreflang' => 'ar',
        ];
    }
    
    /**
     * Category SEO
     */
    private function categorySEO($data)
    {
        $category = $data['category'] ?? null;
        $categoryName = $category ? $category->name : 'الفئة';
        
        return [
            'title' => "فئة {$categoryName} - دليل المتاجر والخدمات",
            'description' => "استعرض جميع المتاجر والخدمات في فئة {$categoryName}. اعثر على ما تبحث عنه بسهولة.",
            'keywords' => "{$categoryName}, متاجر {$categoryName}, خدمات {$categoryName}, دليل",
            'canonical' => url("/category/" . ($category->slug ?? 'category')),
            'og_title' => "فئة {$categoryName}",
            'og_description' => "استعرض جميع المتاجر في فئة {$categoryName}",
            'og_image' => asset('images/og-category.jpg'),
            'og_type' => 'website',
            'schema' => $this->generateCategorySchema($category),
            'robots' => 'index,follow',
            'hreflang' => 'ar',
        ];
    }
    
    /**
     * Default SEO
     */
    private function defaultSEO($data)
    {
        return [
            'title' => 'دليل المدن - اكتشف مدينتك',
            'description' => 'دليل شامل للمتاجر والخدمات في جميع المدن',
            'keywords' => 'دليل, متاجر, خدمات, مدن',
            'canonical' => url()->current(),
            'og_title' => 'دليل المدن',
            'og_description' => 'دليل شامل للمتاجر والخدمات',
            'og_image' => asset('images/og-default.jpg'),
            'og_type' => 'website',
            'robots' => 'index,follow',
            'hreflang' => 'ar',
        ];
    }
    
    /**
     * Generate Website Schema
     */
    private function generateWebsiteSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name', 'City Guide'),
            'url' => config('app.url'),
            'description' => 'دليل شامل للمتاجر والخدمات في جميع المدن',
            'inLanguage' => 'ar',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => config('app.url') . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string'
            ]
        ];
    }
    
    /**
     * Generate City Schema
     */
    private function generateCitySchema($city)
    {
        if (!$city) return null;
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'City',
            'name' => $city->name,
            'url' => url("/city/{$city->slug}"),
            'description' => $city->description ?? "دليل شامل لمدينة {$city->name}",
            'containedInPlace' => [
                '@type' => 'Country',
                'name' => 'مصر'
            ]
        ];
    }
    
    /**
     * Generate Shop Schema
     */
    private function generateShopSchema($shop)
    {
        if (!$shop) return null;
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $shop->name,
            'url' => url("/shop/{$shop->slug}"),
            'description' => $shop->description,
            'image' => $shop->getFirstImageAttribute(),
        ];
        
        if ($shop->phone) {
            $schema['telephone'] = $shop->phone;
        }
        
        if ($shop->address) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $shop->address,
                'addressLocality' => $shop->city ? $shop->city->name : '',
                'addressCountry' => 'EG'
            ];
        }
        
        if ($shop->category) {
            $schema['additionalType'] = $shop->category->name;
        }
        
        // Add opening hours if available
        if ($shop->opening_hours && is_array($shop->opening_hours)) {
            $openingHours = [];
            foreach ($shop->opening_hours as $day => $hours) {
                if (isset($hours['open']) && isset($hours['close'])) {
                    $openingHours[] = ucfirst($day) . ' ' . $hours['open'] . '-' . $hours['close'];
                }
            }
            if (!empty($openingHours)) {
                $schema['openingHours'] = $openingHours;
            }
        }
        
        return $schema;
    }
    
    /**
     * Generate Category Schema
     */
    private function generateCategorySchema($category)
    {
        if (!$category) return null;
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => "فئة {$category->name}",
            'url' => url("/category/{$category->slug}"),
            'description' => "جميع المتاجر في فئة {$category->name}",
            'mainEntity' => [
                '@type' => 'ItemList',
                'name' => "متاجر {$category->name}",
                'description' => "قائمة بجميع المتاجر في فئة {$category->name}"
            ]
        ];
    }
    
    /**
     * Generate Sitemap Data
     */
    public function generateSitemapData()
    {
        $urls = [];
        
        // Homepage
        $urls[] = [
            'url' => url('/'),
            'changefreq' => 'daily',
            'priority' => '1.0',
            'lastmod' => now()->toISOString()
        ];
        
        // Cities
        $cities = \App\Models\City::all();
        foreach ($cities as $city) {
            $urls[] = [
                'url' => url("/city/{$city->slug}"),
                'changefreq' => 'weekly',
                'priority' => '0.9',
                'lastmod' => $city->updated_at->toISOString()
            ];
        }
        
        // Categories
        $categories = \App\Models\Category::all();
        foreach ($categories as $category) {
            $urls[] = [
                'url' => url("/category/{$category->slug}"),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'lastmod' => $category->updated_at->toISOString()
            ];
        }
        
        // Shops
        $shops = \App\Models\Shop::where('is_active', true)->get();
        foreach ($shops as $shop) {
            $urls[] = [
                'url' => url("/shop/{$shop->slug}"),
                'changefreq' => 'weekly',
                'priority' => '0.7',
                'lastmod' => $shop->updated_at->toISOString()
            ];
        }
        
        return $urls;
    }
    
    /**
     * Generate robots.txt content
     */
    public function generateRobotsTxt()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Disallow: */search\n";
        $content .= "Disallow: */ajax\n";
        $content .= "\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
        
        return $content;
    }
}