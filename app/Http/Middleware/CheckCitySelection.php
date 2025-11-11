<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCitySelection
{
    /**
     * Handle an incoming request.
     * 
     * Redirect to city selection page if no city is selected
     * Skip redirect for certain routes
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Routes that should be accessible without city selection
        $allowedRoutes = [
            'select.city.page',
            'home',
            'set.city',
            'clear.city.session',
            'test.city.session',
            'clear.session',
            'login',
            'register',
            'password.*',
            'api.*',
        ];

        // Check if current route is allowed
        $currentRoute = $request->route() ? $request->route()->getName() : null;
        
        // Allow if no route name (API routes, etc.)
        if (!$currentRoute) {
            return $next($request);
        }
        foreach ($allowedRoutes as $pattern) {
            if (fnmatch($pattern, $currentRoute)) {
                return $next($request);
            }
        }

        // Check if city is selected in session
        if (!session()->has('selected_city')) {
            // Redirect to city selection page using URL instead of route name
            return redirect('/select-city')
                ->with('info', 'الرجاء اختيار مدينتك للمتابعة');
        }

        return $next($request);
    }
}
