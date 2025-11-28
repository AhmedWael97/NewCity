<?php

namespace App\Services;

use App\Models\PushNotification;
use App\Models\DeviceToken;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;

class NotificationService
{
    protected $serverKey;
    protected $projectId;
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    protected $fcmV1Url;
    protected $credentialsPath;

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key');
        $this->projectId = config('services.firebase.web.project_id');
        $this->credentialsPath = config('services.firebase.credentials');
        $this->fcmV1Url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * Get OAuth 2.0 access token using service account
     */
    protected function getAccessToken()
    {
        try {
            $credentialsFile = storage_path('app/firebase/service-account.json');
            
            if (!file_exists($credentialsFile)) {
                Log::error('Firebase service account file not found', ['path' => $credentialsFile]);
                return null;
            }

            $client = new GoogleClient();
            $client->setAuthConfig($credentialsFile);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            $token = $client->fetchAccessTokenWithAssertion();
            
            if (isset($token['access_token'])) {
                return $token['access_token'];
            }
            
            Log::error('Failed to get access token', ['token' => $token]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting Firebase access token', ['error' => $e->getMessage()]);
            return null;
        }
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

        // Send to each token individually (FCM v1 API doesn't support batch for web)
        foreach ($tokens as $deviceToken) {
            try {
                $result = $this->sendSingleNotification($deviceToken->device_token, $title, $body, $data);
                
                $sent++;
                
                if ($result['success']) {
                    $successCount++;
                    $status = 'sent';
                    $errorMessage = null;
                } else {
                    $failureCount++;
                    $status = 'failed';
                    $errorMessage = $result['error'] ?? 'Unknown error';
                    
                    // Deactivate invalid tokens
                    if (isset($result['error_code']) && in_array($result['error_code'], ['NOT_FOUND', 'INVALID_ARGUMENT', 'UNREGISTERED'])) {
                        $deviceToken->update(['is_active' => false]);
                        Log::info('Deactivated invalid device token', ['token_id' => $deviceToken->id]);
                    }
                }

                // Create notification log if push_notification_id exists in data
                if (isset($data['notification_id'])) {
                    NotificationLog::create([
                        'push_notification_id' => $data['notification_id'],
                        'device_token_id' => $deviceToken->id,
                        'status' => $status,
                        'error_message' => $errorMessage,
                    ]);
                }
            } catch (\Exception $e) {
                $sent++;
                $failureCount++;
                Log::error('FCM send exception', [
                    'token_id' => $deviceToken->id,
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
     * Send notification to a single device token using legacy API
     */
    protected function sendSingleNotification(string $deviceToken, string $title, string $body, array $data = [])
    {
        // Use legacy API with server key if available
        if (!empty($this->serverKey) && $this->serverKey !== 'your-firebase-server-key-here') {
            return $this->sendViaLegacyApi($deviceToken, $title, $body, $data);
        }
        
        // Otherwise use the web push directly through browser
        // For web notifications, we rely on the service worker and VAPID key
        // The actual sending happens through the browser's FCM connection
        return $this->sendViaWebPush($deviceToken, $title, $body, $data);
    }

    /**
     * Send via legacy FCM API (fallback to HTTP v1)
     */
    protected function sendViaLegacyApi(string $deviceToken, string $title, string $body, array $data = [])
    {
        // Try legacy API first
        $legacyPayload = [
            'to' => $deviceToken,
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
            ])->post($this->fcmUrl, $legacyPayload);

            $result = $response->json();

            // If legacy API returns 404, try HTTP v1 API
            if ($response->status() === 404) {
                Log::info('Legacy FCM API deprecated, trying HTTP v1 API');
                return $this->sendViaHttpV1Api($deviceToken, $title, $body, $data);
            }

            Log::info('FCM Response', [
                'status' => $response->status(),
                'body' => $result,
                'token_preview' => substr($deviceToken, 0, 20) . '...'
            ]);

            if ($response->successful() && isset($result['success']) && $result['success'] > 0) {
                return ['success' => true];
            } else {
                Log::error('FCM send failed', [
                    'status' => $response->status(),
                    'result' => $result,
                    'error' => $result['results'][0]['error'] ?? 'Unknown error'
                ]);
                return [
                    'success' => false,
                    'error' => $result['results'][0]['error'] ?? 'Unknown error',
                    'error_code' => $result['results'][0]['error'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Legacy FCM API error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send via FCM HTTP v1 API
     */
    protected function sendViaHttpV1Api(string $deviceToken, string $title, string $body, array $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                return ['success' => false, 'error' => 'Failed to get OAuth access token'];
            }

            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
            
            // Ensure all data values are strings as required by FCM HTTP v1 API
            $stringData = [];
            foreach ($data as $key => $value) {
                $stringData[$key] = (string) $value;
            }
            $stringData['click_action'] = $data['action_url'] ?? url('/');
            
            $message = [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $stringData,
                    'webpush' => [
                        'notification' => [
                            'icon' => url('/images/senu-logo.svg'),
                            'badge' => url('/images/senu-logo.svg'),
                        ],
                        'fcm_options' => [
                            'link' => $data['action_url'] ?? url('/'),
                        ],
                    ],
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $message);

            $result = $response->json();

            Log::info('FCM HTTP v1 Response', [
                'status' => $response->status(),
                'body' => $result,
                'token_preview' => substr($deviceToken, 0, 20) . '...'
            ]);

            if ($response->successful() && isset($result['name'])) {
                return ['success' => true];
            } else {
                Log::error('FCM HTTP v1 send failed', [
                    'status' => $response->status(),
                    'result' => $result,
                ]);
                return [
                    'success' => false,
                    'error' => $result['error']['message'] ?? 'Unknown error',
                    'error_code' => $result['error']['status'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error('FCM HTTP v1 API error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send via Web Push (for when server key is not available)
     * This stores the notification for the service worker to display
     */
    protected function sendViaWebPush(string $deviceToken, string $title, string $body, array $data = [])
    {
        // For web push without server key, we can't send from server
        // The notification will be triggered through the admin panel or scheduled jobs
        // and picked up by the service worker
        
        Log::warning('Server key not configured, notification stored but not pushed', [
            'token' => substr($deviceToken, 0, 20) . '...'
        ]);
        
        return [
            'success' => false,
            'error' => 'Firebase server key not configured. Please add FIREBASE_SERVER_KEY to .env file.'
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
