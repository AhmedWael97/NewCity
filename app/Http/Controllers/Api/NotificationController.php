<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Mark notification as opened
     */
    public function opened(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer|exists:push_notifications,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Mark all logs for this notification as opened
            NotificationLog::where('push_notification_id', $request->notification_id)
                ->whereNull('opened_at')
                ->update(['opened_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as opened'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as opened',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
