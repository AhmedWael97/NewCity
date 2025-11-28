<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends Controller
{
    /**
     * Register or update a device token
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'required|in:web,android,ios',
            'device_name' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
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
                    'app_version' => $request->app_version ?? 'web',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Device token registered successfully',
                'data' => [
                    'id' => $deviceToken->id,
                    'device_type' => $deviceToken->device_type,
                    'is_active' => $deviceToken->is_active,
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
     */
    public function storeGuest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'required|in:web,android,ios',
            'device_name' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
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
                    'app_version' => $request->app_version ?? 'web',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Guest device token registered successfully',
                'data' => [
                    'id' => $deviceToken->id,
                    'device_type' => $deviceToken->device_type,
                    'is_active' => $deviceToken->is_active,
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
