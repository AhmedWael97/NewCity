<?php

namespace App\Http\Middleware;

use App\Services\UserTrackingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    protected $tracking;

    public function __construct(UserTrackingService $tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful page loads
        if ($response->isSuccessful() && $request->isMethod('GET')) {
            $pageTitle = $this->extractPageTitle($request);
            
            $this->tracking->trackPageView($pageTitle, [
                'event_data' => [
                    'route_name' => $request->route()?->getName(),
                    'parameters' => $request->route()?->parameters(),
                ],
            ]);
        }

        return $response;
    }

    /**
     * Extract page title from request
     */
    protected function extractPageTitle(Request $request): string
    {
        $routeName = $request->route()?->getName();
        
        // Map route names to readable titles
        $titleMap = [
            'home' => 'الصفحة الرئيسية',
            'city.landing' => 'مدينة',
            'city.shops' => 'متاجر المدينة',
            'city.category-shops' => 'متاجر حسب الفئة',
            'city.featured-shops' => 'المتاجر المميزة',
            'shop.show' => 'صفحة المتجر',
            'search' => 'البحث',
            'search.results' => 'نتائج البحث',
        ];

        return $titleMap[$routeName] ?? $request->path();
    }
}
