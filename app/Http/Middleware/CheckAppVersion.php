<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AppSetting;

class CheckAppVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appVersion = $request->header('App-Version');

        if (!$appVersion) {
            return response()->json([
                'success' => false,
                'message' => 'App version is required',
            ], 400);
        }

        // Check version compatibility
        $versionCheck = AppSetting::checkVersion($appVersion);

        if ($versionCheck['force_update'] && !$versionCheck['is_compatible']) {
            return response()->json([
                'success' => false,
                'status' => 'update_required',
                'message' => AppSetting::get('update_message', 'Please update to continue'),
                'force_update' => true,
                'version_info' => [
                    'current_version' => $appVersion,
                    'min_version' => $versionCheck['min_version'],
                    'latest_version' => $versionCheck['latest_version'],
                ],
            ], 426); // 426 Upgrade Required
        }

        // Add version info to request for later use
        $request->merge(['app_version_check' => $versionCheck]);

        return $next($request);
    }
}
