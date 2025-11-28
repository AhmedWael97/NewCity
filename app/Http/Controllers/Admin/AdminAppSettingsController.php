<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\PushNotification;
use App\Models\DeviceToken;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminAppSettingsController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display app settings
     */
    public function index()
    {
        $settings = AppSetting::all()->keyBy('key');
        
        $stats = [
            'total_devices' => DeviceToken::count(),
            'active_devices' => DeviceToken::where('is_active', true)->count(),
            'ios_devices' => DeviceToken::where('device_type', 'ios')->where('is_active', true)->count(),
            'android_devices' => DeviceToken::where('device_type', 'android')->where('is_active', true)->count(),
            'total_notifications' => PushNotification::count(),
            'pending_notifications' => PushNotification::where('status', 'pending')->count(),
        ];

        return view('admin.app-settings.index', compact('settings', 'stats'));
    }

    /**
     * Update app settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_icon' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'app_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'force_update' => 'boolean',
            'min_app_version' => 'nullable|string|max:20',
            'latest_app_version' => 'nullable|string|max:20',
            'update_message' => 'nullable|string|max:500',
            'android_app_url' => 'nullable|url',
            'ios_app_url' => 'nullable|url',
            'api_status' => 'required|in:active,limited,disabled',
            'firebase_enabled' => 'boolean',
            'firebase_server_key' => 'nullable|string',
        ]);

        // Handle app icon upload
        if ($request->hasFile('app_icon')) {
            $oldIcon = AppSetting::get('app_icon_url');
            if ($oldIcon && Storage::disk('public')->exists($oldIcon)) {
                Storage::disk('public')->delete($oldIcon);
            }

            $icon = $request->file('app_icon');
            $iconName = 'app_icon_' . time() . '.' . $icon->getClientOriginalExtension();
            $iconPath = $icon->storeAs('app-settings', $iconName, 'public');
            AppSetting::set('app_icon_url', $iconPath, 'string');
        }

        // Handle app logo upload
        if ($request->hasFile('app_logo')) {
            $oldLogo = AppSetting::get('app_logo_url');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $logo = $request->file('app_logo');
            $logoName = 'app_logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('app-settings', $logoName, 'public');
            AppSetting::set('app_logo_url', $logoPath, 'string');
        }

        // Update settings
        foreach ($validated as $key => $value) {
            if (in_array($key, ['app_icon', 'app_logo'])) {
                continue; // Already handled above
            }

            $type = 'string';
            if (in_array($key, ['maintenance_mode', 'force_update', 'firebase_enabled'])) {
                $type = 'boolean';
                $value = $value ? 'true' : 'false';
            }

            AppSetting::set($key, $value, $type);
        }

        return redirect()->route('admin.app-settings.index')
            ->with('success', 'App settings updated successfully');
    }

    /**
     * Notifications index
     */
    public function notifications(Request $request)
    {
        $query = PushNotification::with('creator');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => PushNotification::count(),
            'pending' => PushNotification::where('status', 'pending')->count(),
            'sent' => PushNotification::where('status', 'sent')->count(),
            'failed' => PushNotification::where('status', 'failed')->count(),
        ];

        return view('admin.app-settings.notifications', compact('notifications', 'stats'));
    }

    /**
     * Create notification form
     */
    public function createNotification()
    {
        $activeDevicesCount = DeviceToken::where('is_active', true)->count();
        
        return view('admin.app-settings.create-notification', compact('activeDevicesCount'));
    }

    /**
     * Store new notification
     */
    public function storeNotification(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'type' => 'required|in:general,alert,promo,update',
            'target' => 'required|in:all,specific_users,city',
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action_url' => 'nullable|string|max:500',
            'scheduled_at' => 'nullable|date|after:now',
            'send_now' => 'boolean',
        ]);

        // Handle image upload
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'notification_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('notifications', $imageName, 'public');
            $imageUrl = Storage::url($imagePath);
        }

        $notification = PushNotification::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'type' => $validated['type'],
            'target' => $validated['target'],
            'target_ids' => $validated['target_ids'] ?? null,
            'image_url' => $imageUrl,
            'action_url' => $validated['action_url'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'created_by' => auth('admin')->user()?->id,
            'status' => 'pending',
        ]);

        // Send immediately if requested
        if ($request->boolean('send_now') && !$validated['scheduled_at']) {
            $result = $this->notificationService->sendPushNotification($notification);
            
            if ($result['success'] && $result['success_count'] > 0) {
                return redirect()->route('admin.app-settings.notifications')
                    ->with('success', "تم إرسال الإشعار بنجاح إلى {$result['success_count']} جهاز");
            } elseif ($result['sent'] > 0 && $result['failure_count'] > 0) {
                return redirect()->route('admin.app-settings.notifications')
                    ->with('warning', "تم إرسال الإشعار إلى {$result['success_count']} جهاز، فشل {$result['failure_count']}");
            } else {
                return redirect()->route('admin.app-settings.notifications')
                    ->with('error', 'فشل إرسال الإشعار: ' . ($result['message'] ?? 'لا توجد أجهزة نشطة مسجلة'));
            }
        }

        return redirect()->route('admin.app-settings.notifications')
            ->with('success', 'تم إنشاء الإشعار بنجاح');
    }

    /**
     * Send notification now
     */
    public function sendNotification(PushNotification $notification)
    {
        if ($notification->status !== 'pending') {
            return back()->with('error', 'Only pending notifications can be sent');
        }

        $this->notificationService->sendPushNotification($notification);

        return back()->with('success', 'Notification sent successfully');
    }

    /**
     * Delete notification
     */
    public function deleteNotification(PushNotification $notification)
    {
        if ($notification->status === 'sending') {
            return back()->with('error', 'Cannot delete notification that is currently sending');
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted successfully');
    }

    /**
     * Device tokens management
     */
    public function devices(Request $request)
    {
        $query = DeviceToken::with('user');

        // Filter by device type
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $devices = $query->orderBy('last_used_at', 'desc')->paginate(50);

        return view('admin.app-settings.devices', compact('devices'));
    }

    /**
     * Test notification
     */
    public function testNotification(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
        ]);

        $result = $this->notificationService->sendTestNotification(auth('admin')->id());

        if ($result['success']) {
            return response()->json(['success' => true, 'message' => 'Test notification sent successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send test notification'], 500);
    }
}
