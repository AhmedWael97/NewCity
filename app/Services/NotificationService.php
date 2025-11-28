<?php

namespace App\Services;

use App\Models\PushNotification;
use App\Models\DeviceToken;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $serverKey;
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key');
    }

    /**
     * Send notification to specific users
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = [])
    {
        $tokens = DeviceToken::getActiveTokens($userIds);
        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(string $title, string $body, array $data = [])
    {
        $tokens = DeviceToken::where('is_active', true)->get();
        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send notification using Push Notification model
     */
    public function sendPushNotification(PushNotification $notification)
    {
        $notification->markAsSending();

        try {
            // Get target tokens based on notification target
            $tokens = $this->getTargetTokens($notification);

            if ($tokens->isEmpty()) {
                $notification->markAsFailed();
                return [
                    'success' => false,
                    'message' => 'No active device tokens found',
                    'sent' => 0,
                    'success_count' => 0,
                    'failure_count' => 0
                ];
            }

            // Send notifications in batches
            $result = $this->sendToTokens(
                $tokens,
                $notification->title,
                $notification->body,
                array_merge($notification->data ?? [], [
                    'notification_id' => $notification->id,
                    'action_url' => $notification->action_url,
                    'image_url' => $notification->image_url,
                ])
            );

            // Update notification status
            $notification->markAsSent(
                $result['sent'],
                $result['success_count'],
                $result['failure_count']
            );

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send push notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            $notification->markAsFailed();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'sent' => 0,
                'success_count' => 0,
                'failure_count' => 0
            ];
        }
    }

    /**
     * Send notifications to device tokens
     */
    protected function sendToTokens($tokens, string $title, string $body, array $data = [])
    {
        $sent = 0;
        $successCount = 0;
        $failureCount = 0;
        $batchSize = 500; // FCM allows up to 1000 tokens per request

        // Split tokens into batches
        foreach ($tokens->chunk($batchSize) as $tokenBatch) {
            $deviceTokens = $tokenBatch->pluck('device_token')->toArray();

            $payload = [
                'registration_ids' => $deviceTokens,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => url('/images/senu-logo.svg'),
                    'badge' => url('/images/senu-logo.svg'),
                    'click_action' => $data['action_url'] ?? url('/'),
                ],
                'data' => $data,
                'priority' => 'high',
                'content_available' => true,
            ];

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type' => 'application/json',
                ])->post($this->fcmUrl, $payload);

                $result = $response->json();

                if ($response->successful()) {
                    $sent += count($deviceTokens);
                    $successCount += $result['success'] ?? 0;
                    $failureCount += $result['failure'] ?? 0;

                    // Log individual results
                    if (isset($result['results'])) {
                        foreach ($result['results'] as $index => $singleResult) {
                            $deviceToken = $tokenBatch[$index];
                            
                            $status = isset($singleResult['message_id']) ? 'sent' : 'failed';
                            $errorMessage = $singleResult['error'] ?? null;

                            // Create notification log if push_notification_id exists in data
                            if (isset($data['notification_id'])) {
                                NotificationLog::create([
                                    'push_notification_id' => $data['notification_id'],
                                    'device_token_id' => $deviceToken->id,
                                    'status' => $status,
                                    'error_message' => $errorMessage,
                                ]);
                            }

                            // Deactivate invalid tokens
                            if (in_array($errorMessage, ['InvalidRegistration', 'NotRegistered'])) {
                                $deviceToken->update(['is_active' => false]);
                            }
                        }
                    }
                } else {
                    $failureCount += count($deviceTokens);
                    Log::error('FCM batch send failed', [
                        'status' => $response->status(),
                        'response' => $result
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount += count($deviceTokens);
                Log::error('FCM send exception', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => true,
            'sent' => $sent,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
        ];
    }

    /**
     * Get target tokens based on notification settings
     */
    protected function getTargetTokens(PushNotification $notification)
    {
        $query = DeviceToken::where('is_active', true);

        switch ($notification->target) {
            case 'all':
                // All users
                break;

            case 'users':
                // Specific users
                if (!empty($notification->target_ids)) {
                    $query->whereIn('user_id', $notification->target_ids);
                }
                break;

            case 'cities':
                // Users in specific cities
                if (!empty($notification->target_ids)) {
                    $query->whereHas('user', function ($q) use ($notification) {
                        $q->whereHas('shops', function ($shopQuery) use ($notification) {
                            $shopQuery->whereIn('city_id', $notification->target_ids);
                        });
                    });
                }
                break;

            case 'shop_owners':
                // Shop owners only
                $query->whereHas('user', function ($q) {
                    $q->whereHas('shops');
                });
                break;

            case 'regular_users':
                // Regular users (non-shop owners)
                $query->whereHas('user', function ($q) {
                    $q->whereDoesntHave('shops');
                });
                break;

            default:
                // No users
                return collect([]);
        }

        return $query->get();
    }

    /**
     * Test notification to admin
     */
    public function sendTestNotification($adminUserId)
    {
        return $this->sendToUsers(
            [$adminUserId],
            'Test Notification',
            'This is a test notification from SENÚ سنو',
            ['type' => 'test', 'timestamp' => now()->toIso8601String()]
        );
    }
}
