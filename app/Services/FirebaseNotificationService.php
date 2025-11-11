<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\PushNotification;
use App\Models\NotificationLog;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $serverKey;
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        // Get Firebase server key from environment or settings
        $this->serverKey = env('FIREBASE_SERVER_KEY') ?? AppSetting::get('firebase_server_key');
    }

    /**
     * Send notification to specific device tokens
     */
    public function sendToTokens(array $tokens, array $notification, array $data = [])
    {
        if (empty($tokens)) {
            return ['success' => 0, 'failure' => 0, 'results' => []];
        }

        $results = [];
        $successCount = 0;
        $failureCount = 0;

        // Firebase supports up to 1000 tokens per request, but we'll batch in 500s for safety
        $batches = array_chunk($tokens, 500);

        foreach ($batches as $batch) {
            $payload = [
                'registration_ids' => $batch,
                'notification' => [
                    'title' => $notification['title'] ?? '',
                    'body' => $notification['body'] ?? '',
                    'sound' => 'default',
                    'badge' => '1',
                ],
                'data' => $data,
                'priority' => 'high',
            ];

            if (isset($notification['image'])) {
                $payload['notification']['image'] = $notification['image'];
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type' => 'application/json',
                ])->post($this->fcmUrl, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $successCount += $responseData['success'] ?? 0;
                    $failureCount += $responseData['failure'] ?? 0;
                    $results[] = $responseData;

                    // Handle invalid tokens
                    if (isset($responseData['results'])) {
                        $this->handleInvalidTokens($batch, $responseData['results']);
                    }
                } else {
                    $failureCount += count($batch);
                    Log::error('Firebase notification failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount += count($batch);
                Log::error('Firebase notification exception', [
                    'message' => $e->getMessage(),
                    'batch_size' => count($batch),
                ]);
            }
        }

        return [
            'success' => $successCount,
            'failure' => $failureCount,
            'results' => $results,
        ];
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic($topic, array $notification, array $data = [])
    {
        $payload = [
            'to' => '/topics/' . $topic,
            'notification' => [
                'title' => $notification['title'] ?? '',
                'body' => $notification['body'] ?? '',
                'sound' => 'default',
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        if (isset($notification['image'])) {
            $payload['notification']['image'] = $notification['image'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Firebase topic notification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Firebase topic notification exception', [
                'message' => $e->getMessage(),
                'topic' => $topic,
            ]);
            return null;
        }
    }

    /**
     * Send push notification from database
     */
    public function sendPushNotification(PushNotification $pushNotification)
    {
        if (!AppSetting::get('firebase_enabled', false)) {
            Log::warning('Firebase notifications are disabled');
            return false;
        }

        $pushNotification->markAsSending();

        // Get target device tokens
        $deviceTokens = $this->getTargetTokens($pushNotification);

        if ($deviceTokens->isEmpty()) {
            $pushNotification->markAsFailed();
            return false;
        }

        $tokens = $deviceTokens->pluck('device_token')->toArray();

        $notification = [
            'title' => $pushNotification->title,
            'body' => $pushNotification->body,
        ];

        if ($pushNotification->image_url) {
            $notification['image'] = $pushNotification->image_url;
        }

        $data = array_merge(
            $pushNotification->data ?? [],
            [
                'notification_id' => $pushNotification->id,
                'type' => $pushNotification->type,
                'action_url' => $pushNotification->action_url,
            ]
        );

        $result = $this->sendToTokens($tokens, $notification, $data);

        // Log each notification attempt
        foreach ($deviceTokens as $index => $deviceToken) {
            NotificationLog::create([
                'push_notification_id' => $pushNotification->id,
                'device_token_id' => $deviceToken->id,
                'status' => 'sent',
            ]);
        }

        // Update push notification status
        $pushNotification->markAsSent(
            count($tokens),
            $result['success'],
            $result['failure']
        );

        return true;
    }

    /**
     * Get target device tokens based on push notification settings
     */
    protected function getTargetTokens(PushNotification $pushNotification)
    {
        $query = DeviceToken::where('is_active', true);

        switch ($pushNotification->target) {
            case 'specific_users':
                if (!empty($pushNotification->target_ids)) {
                    $query->whereIn('user_id', $pushNotification->target_ids);
                }
                break;

            case 'city':
                if (!empty($pushNotification->target_ids)) {
                    $query->whereHas('user', function ($q) use ($pushNotification) {
                        $q->whereIn('preferred_city_id', $pushNotification->target_ids);
                    });
                }
                break;

            case 'all':
            default:
                // No additional filtering
                break;
        }

        return $query->get();
    }

    /**
     * Handle invalid/expired tokens
     */
    protected function handleInvalidTokens(array $tokens, array $results)
    {
        foreach ($results as $index => $result) {
            if (isset($result['error']) && in_array($result['error'], ['InvalidRegistration', 'NotRegistered'])) {
                $token = $tokens[$index] ?? null;
                if ($token) {
                    DeviceToken::where('device_token', $token)->update(['is_active' => false]);
                }
            }
        }
    }

    /**
     * Subscribe device to topic
     */
    public function subscribeToTopic($token, $topic)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
            ])->post('https://iid.googleapis.com/iid/v1/' . $token . '/rel/topics/' . $topic);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to subscribe to topic', [
                'token' => $token,
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Unsubscribe device from topic
     */
    public function unsubscribeFromTopic($token, $topic)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
            ])->delete('https://iid.googleapis.com/iid/v1/' . $token . '/rel/topics/' . $topic);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe from topic', [
                'token' => $token,
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
