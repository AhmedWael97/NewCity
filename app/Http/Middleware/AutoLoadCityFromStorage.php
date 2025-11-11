<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\City;

class AutoLoadCityFromStorage
{
    /**
     * Handle an incoming request.
     * 
     * If user visits select-city page but already has city in session,
     * redirect them directly to city landing page
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only run on select-city page
        if ($request->route()->getName() === 'select.city.page') {
            
            // Check if city is already selected in session
            if (session()->has('selected_city')) {
                $citySlug = session('selected_city');
                return redirect()->route('city.landing', ['city' => $citySlug]);
            }
            
            // Check if city slug is in cookie/localStorage (will be handled by JavaScript)
            // Server can't access localStorage, so we'll use a cookie as fallback
            if ($request->cookie('selected_city_slug')) {
                $citySlug = $request->cookie('selected_city_slug');
                
                // Verify city exists and is active
                $city = City::where('slug', $citySlug)
                    ->where('is_active', true)
                    ->first(['id', 'name', 'slug']);
                
                if ($city) {
                    // Set session
                    session([
                        'selected_city' => $city->slug,
                        'selected_city_name' => $city->name,
                        'selected_city_id' => $city->id
                    ]);
                    
                    return redirect()->route('city.landing', ['city' => $city->slug]);
                }
            }
        }

        return $next($request);
    }
}
