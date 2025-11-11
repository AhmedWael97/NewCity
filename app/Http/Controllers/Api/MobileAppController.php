<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\DeviceToken;
use App\Models\NotificationLog;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;

class MobileAppController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Get app configuration
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfig()
    {
        $settings = AppSetting::getAll();

        return response()->json([
            'success' => true,
            'data' => [
                'app_name' => $settings['app_name'] ?? 'City App',
                'app_icon_url' => $settings['app_icon_url'] ? url('storage/' . $settings['app_icon_url']) : null,
                'app_logo_url' => $settings['app_logo_url'] ? url('storage/' . $settings['app_logo_url']) : null,
                'maintenance_mode' => $settings['maintenance_mode'] ?? false,
                'maintenance_message' => $settings['maintenance_message'] ?? 'App is under maintenance',
                'force_update' => $settings['force_update'] ?? false,
                'min_app_version' => $settings['min_app_version'] ?? '1.0.0',
                'latest_app_version' => $settings['latest_app_version'] ?? '1.0.0',
                'update_message' => $settings['update_message'] ?? 'Please update to continue',
                'android_app_url' => $settings['android_app_url'] ?? null,
                'ios_app_url' => $settings['ios_app_url'] ?? null,
                'api_status' => $settings['api_status'] ?? 'active',
            ],
        ]);
    }

    /**
     * Check app status
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'app_version' => 'required|string',
            'platform' => 'required|in:android,ios',
        ]);

        $settings = AppSetting::getAll();
        $appVersion = $validated['app_version'];

        // Check if in maintenance mode
        if ($settings['maintenance_mode'] ?? false) {
            return response()->json([
                'success' => true,
                'status' => 'maintenance',
                'message' => $settings['maintenance_message'] ?? 'App is under maintenance',
                'can_access' => false,
            ]);
        }

        // Check version compatibility
        $versionCheck = AppSetting::checkVersion($appVersion);

        if (!$versionCheck['is_compatible']) {
            return response()->json([
                'success' => true,
                'status' => 'update_required',
                'message' => $settings['update_message'] ?? 'Please update to continue',
                'force_update' => true,
                'can_access' => false,
                'version_info' => [
                    'current_version' => $appVersion,
                    'min_version' => $versionCheck['min_version'],
                    'latest_version' => $versionCheck['latest_version'],
                    'store_url' => $validated['platform'] === 'ios' 
                        ? ($settings['ios_app_url'] ?? null)
                        : ($settings['android_app_url'] ?? null),
                ],
            ]);
        }

        if ($versionCheck['needs_update']) {
            return response()->json([
                'success' => true,
                'status' => 'update_available',
                'message' => 'A new version is available',
                'force_update' => $settings['force_update'] ?? false,
                'can_access' => true,
                'version_info' => [
                    'current_version' => $appVersion,
                    'latest_version' => $versionCheck['latest_version'],
                    'store_url' => $validated['platform'] === 'ios' 
                        ? ($settings['ios_app_url'] ?? null)
                        : ($settings['android_app_url'] ?? null),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'active',
            'message' => 'App is up to date',
            'can_access' => true,
        ]);
    }

    /**
     * Register device token for push notifications
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerDevice(Request $request)
    {
        $validated = $request->validate([
            'device_token' => 'required|string',
            'device_type' => 'required|in:ios,android',
            'device_name' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:20',
        ]);

        $userId = auth('sanctum')->check() ? auth('sanctum')->user()?->id : null;

        $deviceToken = DeviceToken::registerToken(
            $validated['device_token'],
            $userId,
            [
                'device_type' => $validated['device_type'],
                'device_name' => $validated['device_name'] ?? null,
                'app_version' => $validated['app_version'] ?? null,
            ]
        );

        // Subscribe to general topic
        $this->firebaseService->subscribeToTopic($validated['device_token'], 'all_users');

        // Subscribe to platform-specific topic
        $this->firebaseService->subscribeToTopic($validated['device_token'], $validated['device_type']);

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully',
            'data' => [
                'device_id' => $deviceToken->id,
            ],
        ]);
    }

    /**
     * Update device token
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDevice(Request $request)
    {
        $validated = $request->validate([
            'device_token' => 'required|string',
            'app_version' => 'nullable|string|max:20',
        ]);

        $deviceToken = DeviceToken::where('device_token', $validated['device_token'])->first();

        if (!$deviceToken) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $deviceToken->update([
            'app_version' => $validated['app_version'] ?? $deviceToken->app_version,
            'last_used_at' => now(),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device updated successfully',
        ]);
    }

    /**
     * Unregister device token
     * @return \Illuminate\Http\JsonResponse
     */
    public function unregisterDevice(Request $request)
    {
        $validated = $request->validate([
            'device_token' => 'required|string',
        ]);

        $deviceToken = DeviceToken::where('device_token', $validated['device_token'])->first();

        if ($deviceToken) {
            $deviceToken->update(['is_active' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Device unregistered successfully',
        ]);
    }

    /**
     * Mark notification as opened
     * @return \Illuminate\Http\JsonResponse
     */
    public function notificationOpened(Request $request)
    {
        $validated = $request->validate([
            'notification_id' => 'required|integer|exists:push_notifications,id',
            'device_token' => 'required|string',
        ]);

        $deviceToken = DeviceToken::where('device_token', $validated['device_token'])->first();

        if (!$deviceToken) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $log = NotificationLog::where('push_notification_id', $validated['notification_id'])
            ->where('device_token_id', $deviceToken->id)
            ->first();

        if ($log) {
            $log->markAsOpened();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as opened',
        ]);
    }

    /**
     * Get app health status
     * @return \Illuminate\Http\JsonResponse
     */
    public function health()
    {
        return response()->json([
            'success' => true,
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
