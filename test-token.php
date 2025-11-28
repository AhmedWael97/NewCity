<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();

if ($user) {
    $token = $user->createToken('api-test-token');
    echo "Token created successfully!\n";
    echo "Token: " . $token->plainTextToken . "\n";
    echo "User: " . $user->name . " (ID: " . $user->id . ")\n";
} else {
    echo "No users found in database\n";
}
