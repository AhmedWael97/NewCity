# دليل تحسين الأداء - SENÚ سنو

## نظرة عامة
تم تطبيق تحسينات شاملة للأداء لتحسين سرعة تحميل الموقع وتجربة المستخدم.

## التحسينات المطبقة

### 1. تحسينات Backend

#### Middleware للتحسين (OptimizeResponse.php)
- **ضغط Gzip**: تقليل حجم الاستجابة بنسبة 70-80%
- **رؤوس التخزين المؤقت**: تخزين الموارد الثابتة لمدة سنة واحدة
- **رؤوس الأمان**: حماية من XSS وClickjacking
- **إزالة X-Powered-By**: إخفاء معلومات الخادم

```php
// التفعيل في bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\OptimizeResponse::class,
    ]);
})
```

#### .htaccess تحسينات Apache
- **mod_deflate**: ضغط الملفات النصية
- **mod_expires**: التخزين المؤقت للمتصفح
- **mod_headers**: رؤوس الأمان
- **1 سنة للصور والخطوط**
- **شهر واحد لـ CSS و JS**

### 2. تحسينات Frontend

#### Vite Configuration
```javascript
// vite.config.js
{
    build: {
        minify: 'terser',  // تصغير JS
        cssMinify: true,   // تصغير CSS
        terserOptions: {
            compress: {
                drop_console: true,     // إزالة console.log
                drop_debugger: true     // إزالة debugger
            }
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['vue', 'axios']  // فصل vendor code
                }
            }
        }
    }
}
```

#### Service Worker للتخزين المؤقت
- **service-worker.js**: تخزين الموارد محليًا
- **استراتيجية Cache-First**: تحميل أسرع للزيارات المتكررة
- **تحديث تلقائي**: مسح الكاش القديم

#### Lazy Loading للصور
```html
<!-- استخدام في Blade -->
<img src="placeholder.jpg" 
     data-src="actual-image.jpg" 
     loading="lazy" 
     class="lazy-load"
     alt="وصف الصورة">
```

#### Performance.js Script
- **Lazy Loading للصور**: تحميل الصور عند الحاجة فقط
- **Resource Hints**: DNS prefetch و preconnect
- **تحميل غير متزامن للـ CSS**: تأجيل CSS غير الحرج

### 3. تحسينات CSS

#### Performance.css
- **Content Visibility**: إخفاء المحتوى خارج الشاشة
- **GPU Acceleration**: تسريع الرسوميات
- **Contain Properties**: تقليل إعادة الرسم
- **Skeleton Loading**: حالات تحميل احترافية

```css
/* مثال على الاستخدام */
.below-fold {
    content-visibility: auto;
    contain-intrinsic-size: 0 500px;
}

.animated {
    will-change: transform;
    transform: translateZ(0);
    backface-visibility: hidden;
}
```

### 4. تحسينات SEO

#### robots.txt محسّن
```
User-agent: *
Disallow: /admin
Disallow: /api
Disallow: /login
Disallow: /register
Allow: /css
Allow: /js
Allow: /images

Sitemap: https://yourdomain.com/sitemap
```

#### Sitemap محدّث
- صفحات قانونية (Terms, Privacy, About)
- صفحات المدن
- الفئات
- المتاجر
- خدمات المستخدمين

## خطوات التطبيق

### 1. بناء الأصول للإنتاج
```bash
npm run build
```

### 2. تشغيل script التحسين
```bash
node optimize-assets.js
```

### 3. مسح الكاش
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. تفعيل تسجيل Service Worker
تأكد من أن المتصفح يدعم Service Workers وأنها مسجلة في app.blade.php

### 5. اختبار الأداء
استخدم أدوات مثل:
- **Google PageSpeed Insights**: https://pagespeed.web.dev/
- **GTmetrix**: https://gtmetrix.com/
- **WebPageTest**: https://www.webpagetest.org/

## المقاييس المستهدفة

### Core Web Vitals
- **LCP (Largest Contentful Paint)**: < 2.5s ✅
- **FID (First Input Delay)**: < 100ms ✅
- **CLS (Cumulative Layout Shift)**: < 0.1 ✅

### Additional Metrics
- **TTFB (Time to First Byte)**: < 600ms
- **Speed Index**: < 3.4s
- **Total Blocking Time**: < 200ms

## الملفات المضافة/المعدلة

### ملفات جديدة
- `app/Http/Middleware/OptimizeResponse.php` - Middleware للتحسين
- `public/service-worker.js` - Service Worker للتخزين
- `public/js/performance.js` - تحسينات JavaScript
- `optimize-assets.js` - نص تصغير الأصول

### ملفات محدثة
- `vite.config.js` - تكوين البناء للإنتاج
- `public/.htacache` - قواعد Apache
- `bootstrap/app.php` - تسجيل Middleware
- `resources/views/layouts/app.blade.php` - تحسينات Layout
- `public/css/performance.css` - CSS للأداء
- `app/Services/SEOService.php` - Sitemap و Robots.txt

## نصائح إضافية

### 1. استخدام CDN
فكّر في استخدام CDN لتوزيع المحتوى الثابت:
- Cloudflare (مجاني)
- AWS CloudFront
- DigitalOcean Spaces

### 2. تحسين قاعدة البيانات
```bash
# إضافة فهارس للاستعلامات البطيئة
php artisan db:monitor
```

### 3. تحسين الصور
- استخدام WebP بدلاً من JPEG/PNG
- ضغط الصور قبل الرفع
- استخدام أحجام مناسبة (Responsive Images)

```html
<picture>
    <source srcset="image.webp" type="image/webp">
    <img src="image.jpg" alt="Image" loading="lazy">
</picture>
```

### 4. تقليل HTTP Requests
- دمج ملفات CSS
- دمج ملفات JavaScript
- استخدام CSS Sprites للأيقونات

### 5. Database Query Optimization
```php
// استخدام Eager Loading
Shop::with(['city', 'category', 'owner'])->get();

// بدلاً من
Shop::all(); // N+1 query problem
```

## مراقبة الأداء

### Laravel Telescope
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Laravel Debugbar
```bash
composer require barryvdh/laravel-debugbar --dev
```

## استكشاف الأخطاء

### Service Worker لا يعمل
1. تأكد من HTTPS (Service Workers تتطلب HTTPS)
2. تحقق من Console للأخطاء
3. امسح Cache المتصفح

### الصور لا تُحمّل Lazy
1. تحقق من دعم المتصفح
2. تأكد من وجود `loading="lazy"`
3. استخدم polyfill للمتصفحات القديمة

### Gzip لا يعمل
1. تحقق من تفعيل mod_deflate
2. افحص Response Headers
3. تأكد من تكوين .htaccess الصحيح

## الخطوات التالية

1. ✅ إعداد CDN للموارد الثابتة
2. ✅ تحويل الصور إلى WebP
3. ✅ إضافة HTTP/2 Server Push
4. ✅ تطبيق Critical CSS
5. ✅ إعداد Redis للتخزين المؤقت
6. ✅ تحسين استعلامات قاعدة البيانات
7. ✅ إضافة monitoring وتنبيهات الأداء

## الموارد المفيدة

- [Web.dev Performance](https://web.dev/performance/)
- [Laravel Performance Best Practices](https://laravel.com/docs/11.x/deployment#optimization)
- [MDN Web Performance](https://developer.mozilla.org/en-US/docs/Web/Performance)
- [Chrome DevTools Performance](https://developer.chrome.com/docs/devtools/performance/)

---

**ملاحظة**: قم بإعادة البناء والاختبار بعد كل تغيير للتأكد من أن التحسينات تعمل كما هو متوقع.
