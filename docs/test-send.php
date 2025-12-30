<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PushNotification;
use App\Services\NotificationService;

echo "=== Testing Notification Send ===\n\n";

// Create a test notification
$notification = PushNotification::create([
    'title' => 'Test Notification',
    'body' => 'This is a test notification to all users including guests',
    'type' => 'general',
    'target' => 'all',
    'target_ids' => null,
    'status' => 'pending',
    'created_by' => 1,
]);

echo "Created notification ID: {$notification->id}\n";
echo "Target: {$notification->target}\n\n";

// Send the notification
$service = app(NotificationService::class);
echo "Sending notification...\n";

$result = $service->sendPushNotification($notification);

echo "\n=== Results ===\n";
echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
echo "Sent: {$result['sent']}\n";
echo "Success Count: {$result['success_count']}\n";
echo "Failure Count: {$result['failure_count']}\n";

if (isset($result['message'])) {
    echo "Message: {$result['message']}\n";
}

// Refresh and show notification status
$notification->refresh();
echo "\nNotification Status: {$notification->status}\n";
echo "Sent Count: {$notification->sent_count}\n";
echo "Success Count: {$notification->success_count}\n";
echo "Failure Count: {$notification->failure_count}\n";

echo "\nDone!\n";
