<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CitySelection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has selected a city or skipped selection
        $selectedCity = session('selected_city');
        $hasSkipped = session('city_selection_skipped');
        
        // Skip modal for API routes, AJAX requests, and specific pages
        if ($request->ajax() || 
            $request->is('api/*') || 
            $request->is('select-city') || 
            $request->is('set-city') ||
            $request->is('change-city') ||
            $request->wantsJson()) {
            return $next($request);
        }
        
        // If no city selected and user hasn't skipped, show modal
        if (!$selectedCity && !$hasSkipped) {
            // Add a flag to show the modal
            session()->flash('show_city_modal', true);
        }
        
        return $next($request);
    }
}
