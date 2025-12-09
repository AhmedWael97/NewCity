<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class SetPermissionGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        // Set the permission guard to match the authentication guard
        if ($guard) {
            Config::set('permission.defaults.guard', $guard);
            app()[\Spatie\Permission\PermissionRegistrar::class]->setPermissionsTeamId(null);
            app()[\Spatie\Permission\PermissionRegistrar::class]->cacheKey = 'spatie.permission.cache.' . $guard;
        }
        
        return $next($request);
    }
}
