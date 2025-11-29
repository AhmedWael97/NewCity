<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="App Settings",
 *     description="Public endpoints for mobile app to fetch configuration and settings"
 * )
 */
class AppSettingsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/app-settings",
     *     summary="Get app settings for mobile application",
     *     description="Public endpoint for mobile app to fetch current configuration, maintenance status, and force update settings",
     *     tags={"App Settings"},
     *     @OA\Response(
     *         response=200,
     *         description="App settings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="app_name", type="string", example="City Services"),
     *                 @OA\Property(property="app_version", type="string", example="1.0.0"),
     *                 @OA\Property(property="maintenance_mode", type="boolean", example=false),
     *                 @OA\Property(property="maintenance_message", type="string", example="We are currently performing maintenance. Please check back soon."),
     *                 @OA\Property(property="force_update", type="boolean", example=false),
     *                 @OA\Property(property="min_app_version", type="string", example="1.0.0"),
     *                 @OA\Property(property="update_message", type="string", example="A new version is available. Please update to continue."),
     *                 @OA\Property(property="update_url_ios", type="string", example="https://apps.apple.com/app/id123456789"),
     *                 @OA\Property(property="update_url_android", type="string", example="https://play.google.com/store/apps/details?id=com.example.app"),
     *                 @OA\Property(property="support_email", type="string", example="support@example.com"),
     *                 @OA\Property(property="support_phone", type="string", example="+1234567890"),
     *                 @OA\Property(property="privacy_policy_url", type="string", example="https://example.com/privacy"),
     *                 @OA\Property(property="terms_of_service_url", type="string", example="https://example.com/terms"),
     *                 @OA\Property(property="app_icon_url", type="string", example="https://example.com/storage/app-settings/icon.png"),
     *                 @OA\Property(property="app_logo_url", type="string", example="https://example.com/storage/app-settings/logo.png"),
     *                 @OA\Property(property="features", type="object",
     *                     @OA\Property(property="enable_notifications", type="boolean", example=true),
     *                     @OA\Property(property="enable_chat", type="boolean", example=true),
     *                     @OA\Property(property="enable_location", type="boolean", example=true),
     *                     @OA\Property(property="enable_analytics", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Service unavailable - Maintenance mode",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="We are currently performing maintenance. Please check back soon."),
     *             @OA\Property(property="maintenance_mode", type="boolean", example=true)
     *         )
     *     )
     * )
     */
    public function index()
    {
        $settings = AppSetting::getAll();
        
        // Add full URLs for images
        if (isset($settings['app_icon_url'])) {
            $settings['app_icon_url'] = $settings['app_icon_url'] 
                ? url('storage/' . $settings['app_icon_url']) 
                : null;
        }
        
        if (isset($settings['app_logo_url'])) {
            $settings['app_logo_url'] = $settings['app_logo_url'] 
                ? url('storage/' . $settings['app_logo_url']) 
                : null;
        }

        // Group feature flags
        $features = [
            'enable_notifications' => $settings['enable_notifications'] ?? true,
            'enable_chat' => $settings['enable_chat'] ?? true,
            'enable_location' => $settings['enable_location'] ?? true,
            'enable_analytics' => $settings['enable_analytics'] ?? true,
        ];

        // Remove feature flags from main settings
        unset($settings['enable_notifications'], $settings['enable_chat'], 
              $settings['enable_location'], $settings['enable_analytics']);

        // Add features group
        $settings['features'] = $features;

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/app-settings/check-update",
     *     summary="Check if app update is required",
     *     description="Check if the mobile app version needs to be updated",
     *     tags={"App Settings"},
     *     @OA\Parameter(
     *         name="version",
     *         in="query",
     *         required=true,
     *         description="Current app version",
     *         @OA\Schema(type="string", example="1.0.0")
     *     ),
     *     @OA\Parameter(
     *         name="platform",
     *         in="query",
     *         required=true,
     *         description="Mobile platform",
     *         @OA\Schema(type="string", enum={"ios", "android"}, example="android")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Update check completed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="force_update", type="boolean", example=false),
     *                 @OA\Property(property="update_required", type="boolean", example=false),
     *                 @OA\Property(property="current_version", type="string", example="1.0.0"),
     *                 @OA\Property(property="min_version", type="string", example="1.0.0"),
     *                 @OA\Property(property="latest_version", type="string", example="1.2.0"),
     *                 @OA\Property(property="update_message", type="string", example="A new version is available"),
     *                 @OA\Property(property="update_url", type="string", example="https://play.google.com/store/apps/details?id=com.example.app")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Missing parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Version and platform are required")
     *         )
     *     )
     * )
     */
    public function checkUpdate(Request $request)
    {
        $request->validate([
            'version' => 'required|string',
            'platform' => 'required|in:ios,android'
        ]);

        $currentVersion = $request->version;
        $platform = $request->platform;

        $settings = AppSetting::getAll();
        $forceUpdate = $settings['force_update'] ?? false;
        $minVersion = $settings['min_app_version'] ?? '1.0.0';
        $latestVersion = $settings['app_version'] ?? '1.0.0';
        
        $updateRequired = version_compare($currentVersion, $minVersion, '<');
        $updateAvailable = version_compare($currentVersion, $latestVersion, '<');

        $updateUrl = $platform === 'ios' 
            ? ($settings['update_url_ios'] ?? null)
            : ($settings['update_url_android'] ?? null);

        return response()->json([
            'success' => true,
            'data' => [
                'force_update' => $forceUpdate && $updateRequired,
                'update_required' => $updateRequired,
                'update_available' => $updateAvailable,
                'current_version' => $currentVersion,
                'min_version' => $minVersion,
                'latest_version' => $latestVersion,
                'update_message' => $settings['update_message'] ?? 'A new version is available',
                'update_url' => $updateUrl
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/app-settings/maintenance-status",
     *     summary="Check maintenance status",
     *     description="Check if the app is in maintenance mode",
     *     tags={"App Settings"},
     *     @OA\Response(
     *         response=200,
     *         description="Maintenance status retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="maintenance_mode", type="boolean", example=false),
     *                 @OA\Property(property="maintenance_message", type="string", example="We are currently performing maintenance")
     *             )
     *         )
     *     )
     * )
     */
    public function maintenanceStatus()
    {
        $maintenanceMode = AppSetting::get('maintenance_mode', false);
        $maintenanceMessage = AppSetting::get('maintenance_message', 'We are currently performing maintenance. Please check back soon.');

        return response()->json([
            'success' => true,
            'data' => [
                'maintenance_mode' => $maintenanceMode,
                'maintenance_message' => $maintenanceMessage
            ]
        ]);
    }
}
