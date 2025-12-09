<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppSettingResource;
use App\Http\Resources\PushNotificationResource;
use App\Http\Resources\DeviceTokenResource;
use App\Models\AppSetting;
use App\Models\PushNotification;
use App\Models\DeviceToken;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Admin - App Settings",
 *     description="Admin endpoints for managing mobile app settings and configuration"
 * )
 */
class AppSettingsController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/app-settings",
     *     summary="Get all app settings",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="App settings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="settings", type="object"),
     *                 @OA\Property(property="stats", type="object",
     *                     @OA\Property(property="total_devices", type="integer"),
     *                     @OA\Property(property="active_devices", type="integer"),
     *                     @OA\Property(property="ios_devices", type="integer"),
     *                     @OA\Property(property="android_devices", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
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

        $stats = [
            'total_devices' => DeviceToken::count(),
            'active_devices' => DeviceToken::where('is_active', true)->count(),
            'ios_devices' => DeviceToken::where('device_type', 'ios')->where('is_active', true)->count(),
            'android_devices' => DeviceToken::where('device_type', 'android')->where('is_active', true)->count(),
            'total_notifications' => PushNotification::count(),
            'pending_notifications' => PushNotification::where('status', 'pending')->count(),
            'sent_notifications' => PushNotification::where('status', 'sent')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => $settings,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/admin/app-settings",
     *     summary="Update app settings",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="app_name", type="string"),
     *             @OA\Property(property="maintenance_mode", type="boolean"),
     *             @OA\Property(property="maintenance_message", type="string"),
     *             @OA\Property(property="force_update", type="boolean"),
     *             @OA\Property(property="min_app_version", type="string"),
     *             @OA\Property(property="latest_app_version", type="string"),
     *             @OA\Property(property="update_message", type="string"),
     *             @OA\Property(property="android_app_url", type="string"),
     *             @OA\Property(property="ios_app_url", type="string"),
     *             @OA\Property(property="api_status", type="string", enum={"active", "limited", "disabled"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Settings updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Settings updated successfully")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'maintenance_mode' => 'nullable|boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'force_update' => 'nullable|boolean',
            'min_app_version' => 'nullable|string|max:20',
            'latest_app_version' => 'nullable|string|max:20',
            'update_message' => 'nullable|string|max:500',
            'android_app_url' => 'nullable|url',
            'ios_app_url' => 'nullable|url',
            'api_status' => 'nullable|in:active,limited,disabled',
            'firebase_enabled' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            $type = 'string';
            if (in_array($key, ['maintenance_mode', 'force_update', 'firebase_enabled'])) {
                $type = 'boolean';
            }

            AppSetting::set($key, $value, $type);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => AppSetting::getAll()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/app-settings/upload-icon",
     *     summary="Upload app icon",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="icon", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Icon uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="icon_url", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function uploadIcon(Request $request)
    {
        $request->validate([
            'icon' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $oldIcon = AppSetting::get('app_icon_url');
        if ($oldIcon && Storage::disk('public')->exists($oldIcon)) {
            Storage::disk('public')->delete($oldIcon);
        }

        $icon = $request->file('icon');
        $iconName = 'app_icon_' . time() . '.' . $icon->getClientOriginalExtension();
        $iconPath = $icon->storeAs('app-settings', $iconName, 'public');
        
        AppSetting::set('app_icon_url', $iconPath, 'string');

        return response()->json([
            'success' => true,
            'message' => 'App icon uploaded successfully',
            'data' => [
                'icon_url' => url('storage/' . $iconPath)
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/app-settings/upload-logo",
     *     summary="Upload app logo",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="logo", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logo uploaded successfully"
     *     )
     * )
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $oldLogo = AppSetting::get('app_logo_url');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        $logo = $request->file('logo');
        $logoName = 'app_logo_' . time() . '.' . $logo->getClientOriginalExtension();
        $logoPath = $logo->storeAs('app-settings', $logoName, 'public');
        
        AppSetting::set('app_logo_url', $logoPath, 'string');

        return response()->json([
            'success' => true,
            'message' => 'App logo uploaded successfully',
            'data' => [
                'logo_url' => url('storage/' . $logoPath)
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/app-settings/notifications",
     *     summary="Get push notifications list",
     *     tags={"Admin - App Settings"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string", enum={"pending", "sent", "failed"})
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by type",
     *         @OA\Schema(type="string", enum={"general", "alert", "promo", "update"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notifications list retrieved successfully"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function notifications(Request $request)
    {
        $isAuthenticated = auth('sanctum')->check();
        
        $query = PushNotification::query();
        
        // For guests: only show general notifications that have been sent
        if (!$isAuthenticated) {
            $query->where('status', 'sent')
                  ->where('type', 'general');
        } else {
            // For authenticated admins: show all with filters and relations
            $query->with('creator');
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = $isAuthenticated ? [
            'total' => PushNotification::count(),
            'pending' => PushNotification::where('status', 'pending')->count(),
            'sent' => PushNotification::where('status', 'sent')->count(),
            'failed' => PushNotification::where('status', 'failed')->count(),
        ] : null;

        $response = [
            'success' => true,
            'data' => [
                'notifications' => PushNotificationResource::collection($notifications),
                'pagination' => [
                    'total' => $notifications->total(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                ]
            ]
        ];

        if ($isAuthenticated && $stats) {
            $response['data']['stats'] = $stats;
        }

        return response()->json($response);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/app-settings/notifications",
     *     summary="Create and send push notification",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "body", "type", "target"},
     *             @OA\Property(property="title", type="string", maxLength=255),
     *             @OA\Property(property="body", type="string", maxLength=1000),
     *             @OA\Property(property="type", type="string", enum={"general", "alert", "promo", "update"}),
     *             @OA\Property(property="target", type="string", enum={"all", "users", "cities", "shop_owners", "regular_users"}),
     *             @OA\Property(property="target_ids", type="array", @OA\Items(type="integer")),
     *             @OA\Property(property="action_url", type="string"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time"),
     *             @OA\Property(property="send_now", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Notification created successfully"
     *     )
     * )
     */
    public function createNotification(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'type' => 'required|in:general,alert,promo,update',
            'target' => 'required|in:all,users,cities,shop_owners,regular_users',
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'integer',
            'action_url' => 'nullable|string|max:500',
            'scheduled_at' => 'nullable|date|after:now',
            'send_now' => 'nullable|boolean',
        ]);

        $notification = PushNotification::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'type' => $validated['type'],
            'target' => $validated['target'],
            'target_ids' => $validated['target_ids'] ?? null,
            'action_url' => $validated['action_url'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'created_by' => auth('sanctum')->id(),
            'status' => 'pending',
        ]);

        // Send immediately if requested
        if ($request->boolean('send_now') && !$validated['scheduled_at']) {
            $result = $this->notificationService->sendPushNotification($notification);
            
            return response()->json([
                'success' => true,
                'message' => "Notification sent to {$result['success_count']} devices",
                'data' => [
                    'notification' => new PushNotificationResource($notification),
                    'send_result' => [
                        'success_count' => $result['success_count'] ?? 0,
                        'failure_count' => $result['failure_count'] ?? 0,
                    ]
                ]
            ], 201);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully',
            'data' => new PushNotificationResource($notification)
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/app-settings/notifications/{notification}/send",
     *     summary="Send a pending notification",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="notification",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification sent successfully"
     *     ),
     *     @OA\Response(response=400, description="Notification cannot be sent")
     * )
     */
    public function sendNotification(PushNotification $notification)
    {
        if ($notification->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending notifications can be sent'
            ], 400);
        }

        $result = $this->notificationService->sendPushNotification($notification);

        return response()->json([
            'success' => true,
            'message' => "Notification sent to {$result['success_count']} devices",
            'data' => [
                'send_result' => [
                    'success_count' => $result['success_count'] ?? 0,
                    'failure_count' => $result['failure_count'] ?? 0,
                ]
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/app-settings/notifications/{notification}",
     *     summary="Delete a notification",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="notification",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification deleted successfully"
     *     )
     * )
     */
    public function deleteNotification(PushNotification $notification)
    {
        if ($notification->status === 'sending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete notification that is currently sending'
            ], 400);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/app-settings/devices",
     *     summary="Get registered devices list",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="Filter by device type",
     *         @OA\Schema(type="string", enum={"ios", "android"})
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Devices list retrieved successfully"
     *     )
     * )
     */
    public function devices(Request $request)
    {
        $query = DeviceToken::with('user');

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $devices = $query->orderBy('last_used_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => [
                'devices' => DeviceTokenResource::collection($devices),
                'pagination' => [
                    'total' => $devices->total(),
                    'current_page' => $devices->currentPage(),
                    'last_page' => $devices->lastPage(),
                    'per_page' => $devices->perPage(),
                ]
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/app-settings/test-notification",
     *     summary="Send a test notification",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="body", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Test notification sent successfully"
     *     )
     * )
     */
    public function testNotification(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
        ]);

        $result = $this->notificationService->sendTestNotification(
            auth('sanctum')->id()
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to send test notification'
        ], 500);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/app-settings/statistics",
     *     summary="Get app statistics",
     *     tags={"Admin - App Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="devices", type="object"),
     *                 @OA\Property(property="notifications", type="object"),
     *                 @OA\Property(property="app_status", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function statistics()
    {
        $settings = AppSetting::getAll();

        $stats = [
            'devices' => [
                'total' => DeviceToken::count(),
                'active' => DeviceToken::where('is_active', true)->count(),
                'ios' => DeviceToken::where('device_type', 'ios')->where('is_active', true)->count(),
                'android' => DeviceToken::where('device_type', 'android')->where('is_active', true)->count(),
                'inactive' => DeviceToken::where('is_active', false)->count(),
                'today' => DeviceToken::whereDate('created_at', today())->count(),
                'this_week' => DeviceToken::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => DeviceToken::whereMonth('created_at', now()->month)->count(),
            ],
            'notifications' => [
                'total' => PushNotification::count(),
                'pending' => PushNotification::where('status', 'pending')->count(),
                'sent' => PushNotification::where('status', 'sent')->count(),
                'failed' => PushNotification::where('status', 'failed')->count(),
                'scheduled' => PushNotification::where('status', 'pending')->whereNotNull('scheduled_at')->count(),
                'today' => PushNotification::whereDate('created_at', today())->count(),
            ],
            'app_status' => [
                'maintenance_mode' => $settings['maintenance_mode'] ?? false,
                'force_update' => $settings['force_update'] ?? false,
                'api_status' => $settings['api_status'] ?? 'active',
                'firebase_enabled' => $settings['firebase_enabled'] ?? false,
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
