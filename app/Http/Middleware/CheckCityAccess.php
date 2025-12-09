<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCityAccess
{
    /**
     * Handle an incoming request.
     * Check if city_manager has access to the requested city
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Super admin and admin have access to everything
        if ($user && ($user->hasRole('super_admin') || $user->hasRole('admin'))) {
            return $next($request);
        }

        // City manager needs to check city access
        if ($user && $user->hasRole('city_manager')) {
            // Get city_id from route parameter or request
            $cityId = $request->route('city') 
                ?? $request->route('cityId')
                ?? $request->input('city_id')
                ?? $request->city_id;

            // If checking a model that has city_id relationship
            $modelWithCity = null;
            
            // Check shop
            if ($shop = $request->route('shop')) {
                $modelWithCity = is_object($shop) ? $shop : \App\Models\Shop::find($shop);
                $cityId = $modelWithCity?->city_id;
            }
            
            // Check news
            if ($news = $request->route('news')) {
                $modelWithCity = is_object($news) ? $news : \App\Models\News::find($news);
                $cityId = $modelWithCity?->city_id;
            }
            
            // Check banner
            if ($banner = $request->route('cityBanner')) {
                $modelWithCity = is_object($banner) ? $banner : \App\Models\CityBanner::find($banner);
                $cityId = $modelWithCity?->city_id;
            }

            // If city_id is found, check if user has access
            if ($cityId) {
                $assignedCities = $user->assigned_city_ids ?? [];
                
                if (!in_array($cityId, $assignedCities)) {
                    abort(403, 'You do not have access to this city.');
                }
            }
        }

        return $next($request);
    }
}
