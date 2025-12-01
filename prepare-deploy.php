#!/usr/bin/env php
<?php

/**
 * Quick Deploy Script
 * This script helps you prepare files for deployment to shared hosting
 */

echo "🚀 Preparing files for deployment...\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    die("❌ Error: Please run this script from the Laravel root directory.\n");
}

// Step 1: Check if build folder exists
echo "📦 Checking build assets...\n";
if (!file_exists('public/build/manifest.json')) {
    die("❌ Error: Build files not found. Please run 'npm run build' first.\n");
}
echo "   ✅ Build files found\n\n";

// Step 2: Show files that need to be uploaded
echo "📂 Files that MUST be uploaded to server:\n";
echo "   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "   1. public/build/ (entire folder)\n";
echo "   2. resources/views/layouts/app.blade.php\n";
echo "   3. .env (make sure APP_ENV=production)\n";
echo "   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Step 3: Read manifest and show current build files
$manifest = json_decode(file_get_contents('public/build/manifest.json'), true);
echo "📝 Current build assets:\n";
foreach ($manifest as $file => $details) {
    echo "   ✓ " . $details['file'] . "\n";
}
echo "\n";

// Step 4: Check .env file
echo "🔍 Checking environment configuration...\n";
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    if (strpos($env, 'APP_ENV=production') !== false) {
        echo "   ✅ .env is set to production (make sure server .env is also production)\n";
    } else {
        echo "   ⚠️  Local .env is not set to production (this is OK for local development)\n";
        echo "   ⚠️  IMPORTANT: Make sure your SERVER .env has APP_ENV=production\n";
    }
} else {
    echo "   ⚠️  .env file not found\n";
}
echo "\n";

// Step 5: Show deployment checklist
echo "✅ Pre-deployment Checklist:\n";
echo "   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "   [ ] Run 'npm run build' (completed)\n";
echo "   [ ] Upload public/build/ folder via FTP\n";
echo "   [ ] Upload resources/views/ folder if modified\n";
echo "   [ ] Verify server .env has APP_ENV=production\n";
echo "   [ ] Clear cache on server (visit clear-cache.php)\n";
echo "   [ ] Test website with Ctrl+Shift+R\n";
echo "   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Step 6: Offer to create cache clear file
echo "💡 Would you like to create a cache-clear.php file? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) === 'y' || strtolower($line) === 'yes') {
    $clearCacheContent = <<<'PHP'
<?php
/**
 * Cache Clear Script for Shared Hosting
 * 
 * Upload this file to your public folder and visit it once: 
 * https://yoursite.com/clear-cache.php
 * 
 * DELETE THIS FILE AFTER USE for security!
 */

// Prevent unauthorized access (optional - set your own password)
$password = 'your_secret_password_here'; // CHANGE THIS!
if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    die('Access denied. Usage: clear-cache.php?pass=your_secret_password_here');
}

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "<h2>Clearing Laravel Cache...</h2>";
    echo "<pre>";
    
    $kernel->call('optimize:clear');
    echo "✅ Optimizations cleared\n";
    
    $kernel->call('view:clear');
    echo "✅ Views cleared\n";
    
    $kernel->call('config:clear');
    echo "✅ Config cleared\n";
    
    $kernel->call('cache:clear');
    echo "✅ Application cache cleared\n";
    
    $kernel->call('route:clear');
    echo "✅ Routes cleared\n";
    
    echo "\n✅ All caches cleared successfully!\n";
    echo "\n⚠️  IMPORTANT: Delete this file now for security!\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
PHP;

    file_put_contents('public/clear-cache.php', $clearCacheContent);
    echo "\n✅ Created public/clear-cache.php\n";
    echo "   1. Upload this file to your server's public folder\n";
    echo "   2. Change the password in the file (line 11)\n";
    echo "   3. Visit: https://senueg.com/clear-cache.php?pass=your_secret_password_here\n";
    echo "   4. DELETE the file after use for security!\n\n";
}

echo "🎉 Deployment preparation complete!\n";
echo "📤 Now upload the files to your server via FTP or cPanel.\n\n";
