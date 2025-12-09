<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::find(47);

// Force re-sync roles with admin guard
echo "Re-syncing roles with admin guard...\n";
$user->syncRoles(['news_reporter_'], 'admin');
app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

echo "User: " . $user->name . "\n";
echo "Default Guard Name: " . $user->getDefaultGuardName() . "\n";
echo "Roles: " . $user->roles()->get()->pluck('name')->implode(', ') . "\n";
echo "Role Guards: " . $user->roles()->get()->pluck('guard_name')->implode(', ') . "\n";
echo "\nPermission Checks:\n";
try {
    echo "hasPermissionTo('view-news', 'admin'): " . ($user->hasPermissionTo('view-news', 'admin') ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "hasPermissionTo('view-news', 'admin'): ERROR - " . $e->getMessage() . "\n";
}
try {
    echo "hasPermissionTo('view-news'): " . ($user->hasPermissionTo('view-news') ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "hasPermissionTo('view-news'): ERROR - " . $e->getMessage() . "\n";
}
echo "can('view-news'): " . ($user->can('view-news') ? 'YES' : 'NO') . "\n";
echo "Check if permission exists: " . (\Spatie\Permission\Models\Permission::where('name', 'view-news')->where('guard_name', 'admin')->exists() ? 'YES' : 'NO') . "\n";
echo "getAllPermissions contains view-news: " . ($user->getAllPermissions()->contains('name', 'view-news') ? 'YES' : 'NO') . "\n";

echo "\nAll Permissions (" . $user->getAllPermissions()->count() . " total):\n";
foreach ($user->getAllPermissions() as $perm) {
    echo "  - {$perm->name} (guard: {$perm->guard_name})\n";
}
