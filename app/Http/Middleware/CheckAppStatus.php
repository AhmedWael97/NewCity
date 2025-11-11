<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AppSetting;

class CheckAppStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if app is in maintenance mode
        if (AppSetting::isMaintenanceMode()) {
            return response()->json([
                'success' => false,
                'status' => 'maintenance',
                'message' => AppSetting::get('maintenance_message', 'App is under maintenance'),
                'can_access' => false,
            ], 503);
        }

        // Check API status
        $apiStatus = AppSetting::get('api_status', 'active');
        
        if ($apiStatus === 'disabled') {
            return response()->json([
                'success' => false,
                'status' => 'disabled',
                'message' => 'API is currently disabled',
                'can_access' => false,
            ], 503);
        }

        if ($apiStatus === 'limited') {
            // Add rate limiting or other restrictions here if needed
            // For now, just allow access but could be extended
        }

        return $next($request);
    }
}
