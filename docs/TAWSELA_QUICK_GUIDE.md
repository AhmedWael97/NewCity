# دليل سريع - نظام توصيلة

## 📋 ملخص سريع

نظام توصيلة (Tawsela) هو نظام مشاركة الرحلات الذي تم تطويره بالكامل باللغة العربية، يسمح للمستخدمين بإضافة رحلاتهم والبحث عن رحلات متاحة.

## 🚀 الوصول السريع

### روابط الصفحات
- **قائمة الرحلات:** `/tawsela`
- **إضافة رحلة:** `/tawsela/create` (يتطلب تسجيل دخول)
- **رحلاتي:** `/tawsela/my-rides` (يتطلب تسجيل دخول)
- **طلباتي:** `/tawsela/my-requests` (يتطلب تسجيل دخول)
- **الرسائل:** `/tawsela/messages` (يتطلب تسجيل دخول)

### API Endpoints الرئيسية
- **GET** `/api/v1/tawsela/rides` - قائمة الرحلات
- **POST** `/api/v1/tawsela/rides` - إضافة رحلة (Auth)
- **POST** `/api/v1/tawsela/rides/{id}/request` - طلب انضمام (Auth)
- **GET** `/api/v1/tawsela/messages` - الرسائل (Auth)
- **POST** `/api/v1/tawsela/messages` - إرسال رسالة (Auth)

## 📊 الملفات المضافة

### Database Migrations
```
database/migrations/
├── 2025_12_30_000001_create_tawsela_rides_table.php
├── 2025_12_30_000002_create_tawsela_requests_table.php
└── 2025_12_30_000003_create_tawsela_messages_table.php
```

### Models
```
app/Models/
├── TawselaRide.php
├── TawselaRequest.php
└── TawselaMessage.php
```

### Controllers
```
app/Http/Controllers/
├── Api/TawselaController.php      (API)
└── TawselaController.php          (Web)
```

### Views
```
resources/views/tawsela/
├── index.blade.php         (قائمة الرحلات)
├── create.blade.php        (إضافة رحلة)
├── show.blade.php          (تفاصيل الرحلة)
├── my-rides.blade.php      (رحلاتي)
├── my-requests.blade.php   (طلباتي)
└── messages.blade.php      (المراسلات)
```

### Routes
- ✅ Web routes added to `routes/web.php`
- ✅ API routes added to `routes/api.php`

## 🔑 المميزات الرئيسية

### ✅ إضافة الرحلات
- معلومات السيارة الكاملة
- تكامل مع Google Maps
- نقاط توقف متعددة
- خيارات تسعير مرنة

### ✅ البحث
- بحث جغرافي ذكي
- فلترة متقدمة
- ترتيب النتائج

### ✅ نظام الطلبات
- إرسال طلبات الانضمام
- قبول/رفض الطلبات
- إدارة المقاعد تلقائياً

### ✅ المراسلة
- محادثات مباشرة
- تحديث تلقائي
- إشعارات الرسائل الجديدة

## 🎨 التصميم

- واجهة عربية بالكامل ✅
- متوافق مع تصميم الموقع الحالي ✅
- متجاوب مع جميع الأجهزة ✅
- استخدام Bootstrap وFontAwesome ✅

## 🔐 الأمان

- Authentication مطلوب للعمليات الحساسة ✅
- CSRF Protection ✅
- Authorization للرحلات والطلبات ✅
- Validation شامل ✅
- Soft Deletes ✅

## 📱 التكامل

### Google Maps API
تأكد من إضافة المفتاح في `.env`:
```
GOOGLE_MAPS_KEY=your_key_here
```

### Authentication
النظام متكامل مع Sanctum الموجود في المشروع

## 🔄 سير العمل

### 1. السائق
```
1. يسجل الدخول
2. يضيف رحلة جديدة (/tawsela/create)
3. يستقبل طلبات الانضمام
4. يقبل أو يرفض الطلبات
5. يتواصل مع الركاب عبر الرسائل
```

### 2. الراكب
```
1. يبحث عن رحلة مناسبة (/tawsela)
2. يعرض تفاصيل الرحلة
3. يرسل طلب انضمام
4. ينتظر الموافقة
5. يتواصل مع السائق عند القبول
```

## 📊 قاعدة البيانات

### الجداول المضافة
1. **tawsela_rides** - معلومات الرحلات
2. **tawsela_requests** - طلبات الانضمام
3. **tawsela_messages** - الرسائل

### العلاقات
- Ride → User (many-to-one)
- Ride → City (many-to-one)
- Ride → Requests (one-to-many)
- Ride → Messages (one-to-many)
- Request → User (many-to-one)
- Request → Ride (many-to-one)

## 🧪 الاختبار

### اختبار سريع
1. قم بتشغيل المشروع
2. افتح `/tawsela`
3. سجل دخول
4. أضف رحلة جديدة
5. ابحث عن الرحلة
6. أرسل طلب انضمام
7. اختبر نظام المراسلة

## 🎯 نقاط مهمة

### تكامل API
- جميع endpoints موثقة بـ Swagger
- Response format موحد
- Error handling شامل

### Frontend
- JavaScript خالص (بدون Vue/React)
- Fetch API للاتصال بالـ API
- Auto-refresh للرسائل

### Backend
- Laravel 11
- RESTful API
- Resource Controllers
- Eloquent ORM

## 🔧 الصيانة

### إضافة إشعارات (مستقبلاً)
يمكن إضافة إشعارات FCM في:
- `TawselaRequest::boot()` → عند طلب جديد
- `TawselaRequest::accept()` → عند قبول الطلب
- `TawselaMessage::boot()` → عند رسالة جديدة

### التحسينات المستقبلية
- نظام تقييمات
- تاريخ الرحلات
- إحصائيات
- تصدير البيانات
- تطبيق موبايل (API جاهز)

## 📚 المراجع

- [توثيق كامل](./TAWSELA_DOCUMENTATION.md)
- [Laravel Docs](https://laravel.com/docs)
- [Google Maps API](https://developers.google.com/maps)

## ⚠️ ملاحظات مهمة

1. **Google Maps Key:** تأكد من تفعيل Places API و Directions API
2. **Database:** تم تشغيل migrations بنجاح
3. **Routes:** جميع routes مضافة ومختبرة
4. **Arabic:** جميع النصوص بالعربية
5. **Style:** متوافق مع تصميم الموقع

## ✅ قائمة التحقق

- [x] Database migrations created and run
- [x] Models with relationships
- [x] API Controllers with all endpoints
- [x] Web Controllers for views
- [x] API routes registered
- [x] Web routes registered
- [x] Frontend views (6 pages)
- [x] Google Maps integration
- [x] Search functionality
- [x] Request system
- [x] Messaging system
- [x] Arabic interface
- [x] Documentation

## 🎉 الخلاصة

تم تطوير نظام توصيلة بالكامل وهو جاهز للاستخدام! النظام يتضمن:
- ✅ Backend كامل (Models, Controllers, API)
- ✅ Frontend كامل (Views, JavaScript)
- ✅ Database (Migrations, Relations)
- ✅ Integration (Google Maps, Authentication)
- ✅ Documentation (Arabic)

**جاهز للإطلاق! 🚀**
