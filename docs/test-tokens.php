<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DeviceToken;

echo "=== Device Token Analysis ===\n\n";

// Check active tokens
$activeTokens = DeviceToken::where('is_active', true)->get();
echo "Active Tokens Count: " . $activeTokens->count() . "\n\n";

if ($activeTokens->count() > 0) {
    foreach ($activeTokens as $token) {
        echo "Token ID: {$token->id}\n";
        echo "User ID: " . ($token->user_id ?? 'Guest') . "\n";
        echo "Device Type: {$token->device_type}\n";
        echo "Device Name: {$token->device_name}\n";
        echo "Token: " . substr($token->device_token, 0, 30) . "...\n";
        echo "Is Active: " . ($token->is_active ? 'Yes' : 'No') . "\n";
        echo "Last Used: {$token->last_used_at}\n";
        echo "---\n";
    }
}

// Test notification service target logic
echo "\n=== Testing Notification Target Logic ===\n\n";

$targetAll = DeviceToken::where('is_active', true)->get();
echo "Target 'all': " . $targetAll->count() . " tokens\n";

$targetUsers = DeviceToken::where('is_active', true)
    ->whereNotNull('user_id')
    ->get();
echo "Target 'users' (authenticated): " . $targetUsers->count() . " tokens\n";

$targetGuests = DeviceToken::where('is_active', true)
    ->whereNull('user_id')
    ->get();
echo "Target 'guests': " . $targetGuests->count() . " tokens\n";

echo "\nDone!\n";
