<?php
/**
 * Cache Clear Script for Shared Hosting
 * 
 * Instructions:
 * 1. Change the password below (line 15)
 * 2. Upload this file to your public folder on the server
 * 3. Visit: https://senueg.com/clear-cache.php?pass=YOUR_PASSWORD
 * 4. DELETE THIS FILE after use for security!
 */

// Security: Set your own password (REQUIRED - CHANGE THIS!)
$password = 'SenuClearCache2024'; // ⚠️ CHANGE THIS PASSWORD!

// Check password
if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    http_response_code(403);
    die('❌ Access denied. Usage: clear-cache.php?pass=YOUR_PASSWORD');
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنظيف الذاكرة المؤقتة</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px;
            direction: rtl;
            text-align: right;
        }
        h1 { color: #2563eb; }
        .success { 
            background: #10b981; 
            color: white; 
            padding: 10px 15px; 
            border-radius: 5px; 
            margin: 10px 0;
        }
        .error { 
            background: #ef4444; 
            color: white; 
            padding: 10px 15px; 
            border-radius: 5px; 
            margin: 10px 0;
        }
        .warning { 
            background: #f59e0b; 
            color: white; 
            padding: 10px 15px; 
            border-radius: 5px; 
            margin: 10px 0;
            font-weight: bold;
        }
        .info { 
            background: #3b82f6; 
            color: white; 
            padding: 10px 15px; 
            border-radius: 5px; 
            margin: 10px 0;
        }
        pre { 
            background: #f3f4f6; 
            padding: 15px; 
            border-radius: 5px; 
            overflow-x: auto;
            direction: ltr;
            text-align: left;
        }
        .command { 
            background: #1f2937; 
            color: #10b981; 
            padding: 15px; 
            border-radius: 5px; 
            font-family: monospace;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>🧹 تنظيف الذاكرة المؤقتة - Laravel</h1>
    
    <?php
    require __DIR__.'/../vendor/autoload.php';

    try {
        $app = require_once __DIR__.'/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        
        echo '<div class="info">📦 جاري تنظيف الذاكرة المؤقتة...</div>';
        
        // Clear all optimizations
        echo '<div class="command">';
        $kernel->call('optimize:clear');
        echo "✅ php artisan optimize:clear - تم التنفيذ\n";
        echo '</div>';
        
        // Clear views
        echo '<div class="command">';
        $kernel->call('view:clear');
        echo "✅ php artisan view:clear - تم التنفيذ\n";
        echo '</div>';
        
        // Clear config
        echo '<div class="command">';
        $kernel->call('config:clear');
        echo "✅ php artisan config:clear - تم التنفيذ\n";
        echo '</div>';
        
        // Clear cache
        echo '<div class="command">';
        $kernel->call('cache:clear');
        echo "✅ php artisan cache:clear - تم التنفيذ\n";
        echo '</div>';
        
        // Clear routes
        echo '<div class="command">';
        $kernel->call('route:clear');
        echo "✅ php artisan route:clear - تم التنفيذ\n";
        echo '</div>';
        
        echo '<div class="success">✅ تم تنظيف جميع الذاكرة المؤقتة بنجاح!</div>';
        
        // Check environment
        $env = app()->environment();
        echo '<div class="info">🔧 البيئة الحالية: ' . strtoupper($env) . '</div>';
        
        if ($env !== 'production') {
            echo '<div class="warning">⚠️ تحذير: البيئة ليست production! تأكد من إعداد .env بشكل صحيح</div>';
        }
        
        echo '<div class="warning">⚠️ تحذير أمني: احذف هذا الملف الآن!</div>';
        echo '<div class="info">لحذف الملف، اذهب إلى مدير الملفات في cPanel واحذف: public/clear-cache.php</div>';
        
    } catch (Exception $e) {
        echo '<div class="error">❌ خطأ: ' . htmlspecialchars($e->getMessage()) . '</div>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
    ?>
    
    <hr style="margin: 30px 0;">
    
    <h2>📋 الخطوات التالية:</h2>
    <ol style="line-height: 2;">
        <li>✅ تم تنظيف الذاكرة المؤقتة</li>
        <li>🔄 قم بتحديث الصفحة الرئيسية (Ctrl+Shift+R)</li>
        <li>🗑️ <strong>احذف هذا الملف فوراً للأمان!</strong></li>
    </ol>
    
</body>
</html>
