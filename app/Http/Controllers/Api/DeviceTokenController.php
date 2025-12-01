<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Device Tokens",
 *     description="API endpoints for managing FCM device tokens and push notifications"
 * )
 */
class DeviceTokenController extends Controller
{
    /**
     * Register or update a device token for authenticated users
     * 
     * @OA\Post(
     *     path="/api/v1/device-tokens",
     *     summary="Register device token (authenticated)",
     *     description="Register or update FCM device token for push notifications. Requires authentication.",
     *     operationId="registerDeviceToken",
     *     tags={"Device Tokens"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Device token and device information",
     *         @OA\JsonContent(
     *             required={"device_token", "device_type"},
     *             @OA\Property(property="device_token", type="string", example="fcm_token_example_here", description="FCM token from Firebase"),
     *             @OA\Property(property="device_type", type="string", enum={"android", "ios", "web"}, example="android", description="Type of device"),
     *             @OA\Property(property="device_name", type="string", example="Samsung Galaxy S21", description="Device display name"),
     *             @OA\Property(property="os_version", type="string", example="Android 13", description="Operating system version"),
     *             @OA\Property(property="device_model", type="string", example="SM-G991B", description="Device model number"),
     *             @OA\Property(property="device_manufacturer", type="string", example="Samsung", description="Device manufacturer"),
     *             @OA\Property(property="device_id", type="string", example="unique-device-id-123", description="Unique device identifier"),
     *             @OA\Property(property="app_version", type="string", example="1.0.0", description="Application version"),
     *             @OA\Property(property="app_build_number", type="string", example="100", description="Application build number"),
     *             @OA\Property(property="language", type="string", example="ar", description="Preferred language code"),
     *             @OA\Property(property="timezone", type="string", example="Asia/Riyadh", description="Device timezone"),
     *             @OA\Property(property="notifications_enabled", type="boolean", example=true, description="Whether notifications are enabled"),
     *             @OA\Property(
     *                 property="device_metadata",
     *                 type="object",
     *                 description="Additional device metadata",
     *                 @OA\Property(property="screen_width", type="integer", example=1080),
     *                 @OA\Property(property="screen_height", type="integer", example=2400),
     *                 @OA\Property(property="ram", type="string", example="8GB"),
     *                 @OA\Property(property="storage", type="string", example="128GB")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device token registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Device token registered successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="device_type", type="string", example="android"),
     *                 @OA\Property(property="device_model", type="string", example="SM-G991B"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="notifications_enabled", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="device_token",
     *                     type="array",
     *                     @OA\Items(type="string", example="The device token field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to register device token"),
     *             @OA\Property(property="error", type="string", example="Error details here")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'required|in:web,android,ios',
            'device_name' => 'nullable|string|max:255',
            'os_version' => 'nullable|string|max:100',
            'device_model' => 'nullable|string|max:255',
            'device_manufacturer' => 'nullable|string|max:255',
            'device_id' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
            'app_build_number' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'notifications_enabled' => 'nullable|boolean',
            'device_metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();

            $deviceToken = DeviceToken::registerToken(
                $request->device_token,
                $userId,
                [
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name ?? $this->detectDeviceName($request),
                    'os_version' => $request->os_version,
                    'device_model' => $request->device_model,
                    'device_manufacturer' => $request->device_manufacturer,
                    'device_id' => $request->device_id,
                    'app_version' => $request->app_version ?? 'web',
                    'app_build_number' => $request->app_build_number,
                    'language' => $request->language ?? 'ar',
                    'timezone' => $request->timezone,
                    'notifications_enabled' => $request->notifications_enabled ?? true,
                    'ip_address' => $request->ip(),
                    'device_metadata' => $request->device_metadata,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Device token registered successfully',
                'data' => [
                    'id' => $deviceToken->id,
                    'device_type' => $deviceToken->device_type,
                    'device_model' => $deviceToken->device_model,
                    'is_active' => $deviceToken->is_active,
                    'notifications_enabled' => $deviceToken->notifications_enabled,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register device token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register device token for guest users (no authentication required)
     * 
     * @OA\Post(
     *     path="/api/v1/guest-device-tokens",
     *     summary="Register device token (guest)",
     *     description="Register FCM device token for non-authenticated users. No authentication required.",
     *     operationId="registerGuestDeviceToken",
     *     tags={"Device Tokens"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Device token and device information",
     *         @OA\JsonContent(
     *             required={"device_token", "device_type"},
     *             @OA\Property(property="device_token", type="string", example="fcm_token_example_here", description="FCM token from Firebase"),
     *             @OA\Property(property="device_type", type="string", enum={"android", "ios", "web"}, example="android", description="Type of device"),
     *             @OA\Property(property="device_name", type="string", example="Samsung Galaxy S21", description="Device display name"),
     *             @OA\Property(property="os_version", type="string", example="Android 13", description="Operating system version"),
     *             @OA\Property(property="device_model", type="string", example="SM-G991B", description="Device model number"),
     *             @OA\Property(property="device_manufacturer", type="string", example="Samsung", description="Device manufacturer"),
     *             @OA\Property(property="device_id", type="string", example="unique-device-id-123", description="Unique device identifier"),
     *             @OA\Property(property="app_version", type="string", example="1.0.0", description="Application version"),
     *             @OA\Property(property="app_build_number", type="string", example="100", description="Application build number"),
     *             @OA\Property(property="language", type="string", example="ar", description="Preferred language code"),
     *             @OA\Property(property="timezone", type="string", example="Asia/Riyadh", description="Device timezone"),
     *             @OA\Property(property="notifications_enabled", type="boolean", example=true, description="Whether notifications are enabled"),
     *             @OA\Property(
     *                 property="device_metadata",
     *                 type="object",
     *                 description="Additional device metadata",
     *                 example={"screen_width": 1080, "screen_height": 2400, "ram": "8GB"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Guest device token registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Guest device token registered successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=456),
     *                 @OA\Property(property="device_type", type="string", example="android"),
     *                 @OA\Property(property="device_model", type="string", example="SM-G991B"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="notifications_enabled", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="device_type",
     *                     type="array",
     *                     @OA\Items(type="string", example="The device type must be one of: web, android, ios.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to register device token"),
     *             @OA\Property(property="error", type="string", example="Error details here")
     *         )
     *     )
     * )
     */
    public function storeGuest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'required|in:web,android,ios',
            'device_name' => 'nullable|string|max:255',
            'os_version' => 'nullable|string|max:100',
            'device_model' => 'nullable|string|max:255',
            'device_manufacturer' => 'nullable|string|max:255',
            'device_id' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
            'app_build_number' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'notifications_enabled' => 'nullable|boolean',
            'device_metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Register token with null user_id for guest
            $deviceToken = DeviceToken::registerToken(
                $request->device_token,
                null, // Guest user (no user_id)
                [
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name ?? $this->detectDeviceName($request),
                    'os_version' => $request->os_version,
                    'device_model' => $request->device_model,
                    'device_manufacturer' => $request->device_manufacturer,
                    'device_id' => $request->device_id,
                    'app_version' => $request->app_version ?? 'web',
                    'app_build_number' => $request->app_build_number,
                    'language' => $request->language ?? 'ar',
                    'timezone' => $request->timezone,
                    'notifications_enabled' => $request->notifications_enabled ?? true,
                    'ip_address' => $request->ip(),
                    'device_metadata' => $request->device_metadata,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Guest device token registered successfully',
                'data' => [
                    'id' => $deviceToken->id,
                    'device_type' => $deviceToken->device_type,
                    'device_model' => $deviceToken->device_model,
                    'is_active' => $deviceToken->is_active,
                    'notifications_enabled' => $deviceToken->notifications_enabled,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register device token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a device token (logout/unregister)
     * 
     * @OA\Delete(
     *     path="/api/v1/device-tokens",
     *     summary="Remove device token",
     *     description="Unregister a device token. Used when user logs out or uninstalls the app.",
     *     operationId="deleteDeviceToken",
     *     tags={"Device Tokens"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Device token to remove",
     *         @OA\JsonContent(
     *             required={"device_token"},
     *             @OA\Property(property="device_token", type="string", example="fcm_token_example_here", description="FCM token to remove")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device token removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Device token removed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device token not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Device token not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="device_token",
     *                     type="array",
     *                     @OA\Items(type="string", example="The device token field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to remove device token"),
     *             @OA\Property(property="error", type="string", example="Error details here")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();

            $deleted = DeviceToken::where('user_id', $userId)
                ->where('device_token', $request->device_token)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Device token removed successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Device token not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove device token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all device tokens for current user
     * 
     * @OA\Get(
     *     path="/api/v1/device-tokens",
     *     summary="Get user's device tokens",
     *     description="Retrieve all registered devices for the authenticated user",
     *     operationId="getUserDeviceTokens",
     *     tags={"Device Tokens"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of user's device tokens",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=123),
     *                     @OA\Property(property="device_type", type="string", example="android"),
     *                     @OA\Property(property="device_name", type="string", example="Samsung Galaxy S21"),
     *                     @OA\Property(property="app_version", type="string", example="1.0.0"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="last_used_at", type="string", format="date-time", example="2025-12-02T10:30:00.000000Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-01T08:15:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to fetch device tokens"),
     *             @OA\Property(property="error", type="string", example="Error details here")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            $tokens = DeviceToken::where('user_id', $userId)
                ->orderBy('last_used_at', 'desc')
                ->get(['id', 'device_type', 'device_name', 'app_version', 'is_active', 'last_used_at', 'created_at']);

            return response()->json([
                'success' => true,
                'data' => $tokens
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch device tokens',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detect device name from user agent
     */
    protected function detectDeviceName(Request $request)
    {
        $userAgent = $request->userAgent();

        if (stripos($userAgent, 'Chrome') !== false) {
            return 'Chrome Browser';
        } elseif (stripos($userAgent, 'Firefox') !== false) {
            return 'Firefox Browser';
        } elseif (stripos($userAgent, 'Safari') !== false) {
            return 'Safari Browser';
        } elseif (stripos($userAgent, 'Edge') !== false) {
            return 'Edge Browser';
        } elseif (stripos($userAgent, 'Android') !== false) {
            return 'Android Device';
        } elseif (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            return 'iOS Device';
        }

        return 'Unknown Device';
    }
}
