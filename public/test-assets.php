<?php
/**
 * Test Production Assets
 * 
 * This file helps you verify that production assets are correctly configured.
 * Upload to public folder and visit: https://senueg.com/test-assets.php
 * DELETE after testing!
 */

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار الملفات - Production Assets Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            direction: rtl;
        }
        .success { background: #10b981; color: white; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #ef4444; color: white; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #f59e0b; color: white; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #3b82f6; color: white; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: right; }
        th { background: #4b5563; color: white; }
        .check-icon { font-size: 24px; }
        .exists { color: #10b981; }
        .missing { color: #ef4444; }
    </style>
</head>
<body>
    <h1>🔍 اختبار ملفات Production Assets</h1>
    
    <?php
    $errors = [];
    $checks = [];
    
    // Check 1: Build folder exists
    $buildFolder = __DIR__ . '/build';
    $checks['Build Folder'] = [
        'path' => 'public/build/',
        'exists' => is_dir($buildFolder)
    ];
    
    // Check 2: Manifest file exists
    $manifestFile = $buildFolder . '/manifest.json';
    $checks['Manifest File'] = [
        'path' => 'public/build/manifest.json',
        'exists' => file_exists($manifestFile)
    ];
    
    // Check 3: Read manifest and check assets
    if (file_exists($manifestFile)) {
        $manifest = json_decode(file_get_contents($manifestFile), true);
        
        if ($manifest) {
            // Check CSS file
            if (isset($manifest['resources/css/app.css'])) {
                $cssFile = $manifest['resources/css/app.css']['file'];
                $cssPath = $buildFolder . '/' . $cssFile;
                $checks['CSS File'] = [
                    'path' => 'public/build/' . $cssFile,
                    'exists' => file_exists($cssPath),
                    'size' => file_exists($cssPath) ? filesize($cssPath) : 0
                ];
            }
            
            // Check JS file
            if (isset($manifest['resources/js/app.js'])) {
                $jsFile = $manifest['resources/js/app.js']['file'];
                $jsPath = $buildFolder . '/' . $jsFile;
                $checks['JS File'] = [
                    'path' => 'public/build/' . $jsFile,
                    'exists' => file_exists($jsPath),
                    'size' => file_exists($jsPath) ? filesize($jsPath) : 0
                ];
            }
        }
    }
    
    // Check 4: .env configuration
    $envFile = __DIR__ . '/../.env';
    $checks['.env File'] = [
        'path' => '.env',
        'exists' => file_exists($envFile)
    ];
    
    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        $isProduction = strpos($envContent, 'APP_ENV=production') !== false;
        $checks['APP_ENV Setting'] = [
            'path' => 'APP_ENV in .env',
            'exists' => $isProduction,
            'note' => $isProduction ? 'Set to production ✓' : 'NOT set to production ✗'
        ];
    }
    
    // Display results
    echo '<h2>📊 نتائج الفحص</h2>';
    echo '<table>';
    echo '<tr><th>العنصر</th><th>المسار</th><th>الحالة</th><th>معلومات</th></tr>';
    
    $allGood = true;
    foreach ($checks as $name => $check) {
        $status = $check['exists'] ? '<span class="exists">✅ موجود</span>' : '<span class="missing">❌ مفقود</span>';
        $info = '';
        
        if (isset($check['size'])) {
            $info = number_format($check['size'] / 1024, 2) . ' KB';
        }
        if (isset($check['note'])) {
            $info = $check['note'];
        }
        
        echo "<tr>";
        echo "<td><strong>{$name}</strong></td>";
        echo "<td><code>{$check['path']}</code></td>";
        echo "<td>{$status}</td>";
        echo "<td>{$info}</td>";
        echo "</tr>";
        
        if (!$check['exists']) {
            $allGood = false;
            $errors[] = $name . ' مفقود: ' . $check['path'];
        }
    }
    echo '</table>';
    
    // Summary
    if ($allGood) {
        echo '<div class="success">';
        echo '<h3>✅ جميع الفحوصات ناجحة!</h3>';
        echo '<p>جميع ملفات Production Assets موجودة وجاهزة.</p>';
        echo '</div>';
    } else {
        echo '<div class="error">';
        echo '<h3>❌ توجد ملفات مفقودة!</h3>';
        echo '<ul>';
        foreach ($errors as $error) {
            echo "<li>{$error}</li>";
        }
        echo '</ul>';
        echo '</div>';
        
        echo '<div class="warning">';
        echo '<h3>🔧 كيفية الإصلاح:</h3>';
        echo '<ol>';
        echo '<li>قم بتشغيل <code>npm run build</code> على جهازك المحلي</li>';
        echo '<li>ارفع مجلد <code>public/build/</code> كاملاً إلى السيرفر</li>';
        echo '<li>تأكد من أن ملف <code>.env</code> يحتوي على <code>APP_ENV=production</code></li>';
        echo '<li>قم بزيارة <code>clear-cache.php</code> لمسح الذاكرة المؤقتة</li>';
        echo '</ol>';
        echo '</div>';
    }
    
    // Show manifest content
    if (isset($manifest) && $manifest) {
        echo '<h2>📄 محتوى Manifest.json</h2>';
        echo '<pre>' . json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
    }
    
    // Environment info
    echo '<h2>ℹ️ معلومات البيئة</h2>';
    echo '<table>';
    echo '<tr><td><strong>PHP Version</strong></td><td>' . phpversion() . '</td></tr>';
    echo '<tr><td><strong>Document Root</strong></td><td>' . $_SERVER['DOCUMENT_ROOT'] . '</td></tr>';
    echo '<tr><td><strong>Script Path</strong></td><td>' . __FILE__ . '</td></tr>';
    echo '</table>';
    
    echo '<div class="warning">';
    echo '<h3>⚠️ تحذير أمني</h3>';
    echo '<p><strong>احذف هذا الملف بعد الانتهاء من الاختبار!</strong></p>';
    echo '<p>الملف: <code>public/test-assets.php</code></p>';
    echo '</div>';
    ?>
    
</body>
</html>
