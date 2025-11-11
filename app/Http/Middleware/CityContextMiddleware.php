<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\City;
use Symfony\Component\HttpFoundation\Response;

class CityContextMiddleware
{
    /**
     * Handle an incoming request and set city context.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $selectedCity = null;
        $cityContext = [
            'selected_city' => null,
            'selected_city_id' => null,
            'selected_city_name' => null,
            'is_city_selected' => false,
            'should_show_modal' => false,
        ];

        // Check for city in URL path (for future city-specific URLs)
        $citySlug = $this->extractCityFromUrl($request);
        
        if ($citySlug) {
            // City from URL takes priority
            $selectedCity = $this->getCityBySlug($citySlug);
            if ($selectedCity) {
                $this->updateSession($selectedCity);
            }
        } elseif (session('selected_city')) {
            // Fall back to session
            $selectedCity = $this->getCityBySlug(session('selected_city'));
        }

        if ($selectedCity) {
            $cityContext = [
                'selected_city' => $selectedCity,
                'selected_city_id' => $selectedCity->id,
                'selected_city_name' => $selectedCity->name,
                'selected_city_slug' => $selectedCity->slug,
                'is_city_selected' => true,
                'should_show_modal' => false,
            ];
        } else {
            // Determine if we should show the modal
            $cityContext['should_show_modal'] = $this->shouldShowCityModal($request);
        }

        // Share city context with all views
        View::share('cityContext', $cityContext);

        // Add city context to request for controllers
        $request->merge(['cityContext' => $cityContext]);

        return $next($request);
    }

    /**
     * Extract city slug from URL if city-specific routing is used
     */
    private function extractCityFromUrl(Request $request): ?string
    {
        $path = $request->path();
        
        // Pattern: city/{citySlug}/... or just {citySlug} if subdomain routing
        if (preg_match('/^city\/([a-zA-Z0-9\-]+)/', $path, $matches)) {
            return $matches[1];
        }

        // Check for subdomain-based city routing (future implementation)
        $host = $request->getHost();
        if (preg_match('/^([a-zA-Z0-9\-]+)\./', $host, $matches)) {
            $subdomain = $matches[1];
            if ($subdomain !== 'www' && $subdomain !== 'api' && $subdomain !== 'admin') {
                return $subdomain;
            }
        }

        return null;
    }

    /**
     * Get city by slug with caching
     */
    private function getCityBySlug(string $slug): ?City
    {
        return Cache::remember(
            "city_context_{$slug}", 
            3600, 
            fn() => City::where('slug', $slug)
                ->where('is_active', true)
                ->select(['id', 'name', 'slug', 'governorate', 'image'])
                ->first()
        );
    }

    /**
     * Update session with selected city
     */
    private function updateSession(City $city): void
    {
        session([
            'selected_city' => $city->slug,
            'selected_city_name' => $city->name,
            'selected_city_id' => $city->id,
        ]);
        session()->forget('city_selection_skipped');
    }

    /**
     * Determine if city selection modal should be shown
     */
    private function shouldShowCityModal(Request $request): bool
    {
        // Don't show modal for:
        // - AJAX requests
        // - API routes
        // - Admin routes
        // - Already skipped users
        // - Routes that don't need city context

        if ($request->ajax() || 
            $request->is('api/*') || 
            $request->is('admin/*') ||
            $request->is('shop-owner/*') ||
            session('city_selection_skipped')) {
            return false;
        }

        // Routes that require city selection
        $routesRequiringCity = [
            '/',
            'search',
            'categories',
            'shops',
            'city/*',
            'category/*',
        ];

        foreach ($routesRequiringCity as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}