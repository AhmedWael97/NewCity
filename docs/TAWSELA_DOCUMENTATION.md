# نظام في طريقك - Fe-Tare2k Carpooling System

## نظرة عامة
نظام في طريقك هو نظام مشاركة الرحلات الذي يسمح للمستخدمين بإضافة رحلاتهم ومشاركتها مع آخرين، مما يساعد في توفير المصاريف وحماية البيئة.

## المميزات الرئيسية

### 1. إضافة الرحلات
- تفاصيل السيارة (الموديل، السنة، اللون)
- عدد المقاعد المتاحة
- نقطة البداية (مع تكامل خرائط جوجل)
- نقاط التوقف الاختيارية
- الوجهة النهائية
- نوع التسعير (ثابت أو قابل للتفاوض)
- وحدة السعر (للشخص أو للرحلة)
- موعد المغادرة

### 2. البحث عن الرحلات
- البحث بالمدينة
- البحث بالموقع (نقطة البداية والوجهة)
- البحث بالمسافة القصوى
- فلترة حسب المقاعد المتاحة
- ترتيب النتائج (الأحدث، الأقرب موعداً، الأرخص)

### 3. طلبات الانضمام
- إرسال طلب للانضمام إلى رحلة
- تحديد نقطة الصعود
- تحديد نقطة النزول (اختياري)
- عدد الركاب
- عرض سعر مخصص (للرحلات القابلة للتفاوض)
- رسالة للسائق

### 4. إدارة الطلبات
- قبول أو رفض الطلبات
- عرض معلومات الراكب
- إلغاء الطلب

### 5. نظام المراسلة
- محادثات بين السائق والركاب
- إشعارات الرسائل الجديدة
- تحديث تلقائي للمحادثات

## هيكل قاعدة البيانات

### جدول tawsela_rides
```sql
- id
- user_id (السائق)
- city_id
- car_model
- car_year
- car_color
- available_seats
- start_latitude
- start_longitude
- start_address
- destination_latitude
- destination_longitude
- destination_address
- stop_points (JSON)
- price
- price_type (fixed/negotiable)
- price_unit (per_person/per_trip)
- departure_time
- notes
- status (pending/active/completed/cancelled)
- views_count
- requests_count
- timestamps
- soft_deletes
```

### جدول tawsela_requests
```sql
- id
- ride_id
- user_id (الراكب)
- pickup_latitude
- pickup_longitude
- pickup_address
- dropoff_latitude
- dropoff_longitude
- dropoff_address
- passengers_count
- offered_price
- message
- status (pending/accepted/rejected/cancelled)
- timestamps
```

### جدول tawsela_messages
```sql
- id
- ride_id
- request_id
- sender_id
- receiver_id
- message
- is_read
- timestamps
```

## API Endpoints

### Public Endpoints

#### GET /api/v1/fe-tare2k/rides
الحصول على قائمة الرحلات المتاحة

**Parameters:**
- `city_id` (optional) - Filter by city
- `start_lat` (optional) - Start location latitude
- `start_lng` (optional) - Start location longitude
- `dest_lat` (optional) - Destination latitude
- `dest_lng` (optional) - Destination longitude
- `max_distance` (optional, default: 10) - Maximum distance in km

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "user": { "id": 1, "name": "أحمد محمد", "phone": "01234567890", "avatar": "..." },
        "city": { "id": 1, "name": "القاهرة" },
        "car_model": "تويوتا كورولا",
        "car_year": 2020,
        "car_color": "أبيض",
        "available_seats": 3,
        "remaining_seats": 2,
        "start_address": "...",
        "destination_address": "...",
        "stop_points": [...],
        "price": 50,
        "price_type": "fixed",
        "price_unit": "per_person",
        "departure_time": "2025-12-30 10:00:00",
        "status": "active",
        "notes": "..."
      }
    ],
    "current_page": 1,
    "total": 10
  }
}
```

#### GET /api/v1/fe-tare2k/rides/{id}
الحصول على تفاصيل رحلة معينة

### Authenticated Endpoints (require authentication)

#### POST /api/v1/fe-tare2k/rides
إضافة رحلة جديدة

**Request Body:**
```json
{
  "city_id": 1,
  "car_model": "تويوتا كورولا",
  "car_year": 2020,
  "car_color": "أبيض",
  "available_seats": 3,
  "start_latitude": 30.0444,
  "start_longitude": 31.2357,
  "start_address": "...",
  "destination_latitude": 31.2001,
  "destination_longitude": 29.9187,
  "destination_address": "...",
  "stop_points": [
    {
      "latitude": 30.5,
      "longitude": 31.0,
      "address": "..."
    }
  ],
  "price": 50,
  "price_type": "fixed",
  "price_unit": "per_person",
  "departure_time": "2025-12-30 10:00:00",
  "notes": "ممنوع التدخين"
}
```

#### PUT /api/v1/fe-tare2k/rides/{id}
تحديث رحلة

#### DELETE /api/v1/fe-tare2k/rides/{id}
حذف رحلة

#### GET /api/v1/fe-tare2k/my-rides
الحصول على رحلات المستخدم الحالي

#### POST /api/v1/fe-tare2k/rides/{id}/request
إرسال طلب للانضمام إلى رحلة

**Request Body:**
```json
{
  "pickup_latitude": 30.0444,
  "pickup_longitude": 31.2357,
  "pickup_address": "...",
  "dropoff_latitude": 31.0,
  "dropoff_longitude": 30.5,
  "dropoff_address": "...",
  "passengers_count": 2,
  "offered_price": 45,
  "message": "أود الانضمام إلى رحلتك"
}
```

#### GET /api/v1/fe-tare2k/my-requests
الحصول على طلبات المستخدم الحالي

#### GET /api/v1/fe-tare2k/rides/{id}/requests
الحصول على طلبات رحلة معينة (للسائق فقط)

#### POST /api/v1/fe-tare2k/requests/{id}/accept
قبول طلب انضمام

#### POST /api/v1/fe-tare2k/requests/{id}/reject
رفض طلب انضمام

#### POST /api/v1/fe-tare2k/requests/{id}/cancel
إلغاء طلب انضمام

#### GET /api/v1/fe-tare2k/messages
الحصول على رسائل المستخدم

**Parameters:**
- `ride_id` (optional) - Filter by ride
- `user_id` (optional) - Filter by conversation with user

#### POST /api/v1/fe-tare2k/messages
إرسال رسالة

**Request Body:**
```json
{
  "ride_id": 1,
  "receiver_id": 2,
  "request_id": 1,
  "message": "مرحباً، هل يمكنني الانضمام؟"
}
```

#### GET /api/v1/fe-tare2k/conversations
الحصول على قائمة المحادثات

## Web Routes

### Public Routes
- `GET /fe-tare2k` - عرض قائمة الرحلات
- `GET /fe-tare2k/{id}` - عرض تفاصيل رحلة

### Authenticated Routes
- `GET /fe-tare2k/create` - صفحة إضافة رحلة جديدة
- `GET /fe-tare2k/my-rides` - رحلاتي
- `GET /fe-tare2k/my-requests` - طلباتي
- `GET /fe-tare2k/messages` - المراسلات

## Models

### TawselaRide
**Relationships:**
- `belongsTo(User)` - السائق
- `belongsTo(City)` - المدينة
- `hasMany(TawselaRequest)` - الطلبات
- `hasMany(TawselaMessage)` - الرسائل

**Scopes:**
- `active()` - الرحلات النشطة
- `upcoming()` - الرحلات القادمة
- `inCity($cityId)` - الرحلات في مدينة محددة

**Methods:**
- `incrementViews()` - زيادة عدد المشاهدات
- `incrementRequests()` - زيادة عدد الطلبات
- `hasAvailableSeats()` - التحقق من توفر مقاعد
- `getRemainingSeats()` - الحصول على عدد المقاعد المتبقية
- `static calculateDistance()` - حساب المسافة بين نقطتين
- `static searchNearby()` - البحث عن رحلات قريبة

### TawselaRequest
**Relationships:**
- `belongsTo(TawselaRide)` - الرحلة
- `belongsTo(User)` - الراكب
- `hasMany(TawselaMessage)` - الرسائل

**Scopes:**
- `pending()` - الطلبات قيد الانتظار
- `accepted()` - الطلبات المقبولة

**Methods:**
- `accept()` - قبول الطلب
- `reject()` - رفض الطلب
- `cancel()` - إلغاء الطلب

### TawselaMessage
**Relationships:**
- `belongsTo(TawselaRide)` - الرحلة
- `belongsTo(TawselaRequest)` - الطلب
- `belongsTo(User, 'sender_id')` - المرسل
- `belongsTo(User, 'receiver_id')` - المستقبل

**Scopes:**
- `unread()` - الرسائل غير المقروءة
- `forUser($userId)` - رسائل مستخدم معين

**Methods:**
- `markAsRead()` - تعليم كمقروء

## Frontend Components

### Views (Blade Templates)
1. **index.blade.php** - قائمة الرحلات والبحث
2. **create.blade.php** - نموذج إضافة رحلة جديدة
3. **show.blade.php** - تفاصيل الرحلة وطلب الانضمام
4. **my-rides.blade.php** - رحلاتي
5. **my-requests.blade.php** - طلباتي
6. **messages.blade.php** - نظام المراسلة

### Features
- تكامل كامل مع Google Maps API
- Auto-complete للعناوين
- عرض المسار على الخريطة
- تحديث تلقائي للرسائل
- واجهة عربية كاملة
- تصميم متجاوب

## التثبيت والإعداد

### 1. تشغيل Migrations
```bash
php artisan migrate
```

### 2. إعداد Google Maps API
تأكد من إضافة مفتاح Google Maps API في config/services.php:
```php
'google_maps' => [
    'key' => env('GOOGLE_MAPS_KEY'),
],
```

### 3. إضافة المفتاح في .env
```
GOOGLE_MAPS_KEY=your_api_key_here
```

## الأمان

- جميع endpoints المحمية تتطلب authentication
- التحقق من ملكية الرحلات قبل التعديل/الحذف
- التحقق من ملكية الطلبات قبل الإلغاء
- Validation شامل لجميع المدخلات
- CSRF Protection على جميع النماذج
- Soft Deletes للرحلات

## الإشعارات المستقبلية

يمكن إضافة إشعارات عبر FCM للأحداث التالية:
- طلب انضمام جديد
- قبول/رفض طلب
- رسالة جديدة
- تقترب الرحلة من موعدها

## الملاحظات

- جميع النصوص باللغة العربية
- التصميم متوافق مع باقي تصميم الموقع
- استخدام نفس نمط الـ API endpoints الموجودة
- التكامل مع نظام الـ authentication الحالي
- استخدام Sanctum للـ API authentication

## الدعم والتطوير

للمزيد من المعلومات أو الدعم، يرجى التواصل مع فريق التطوير.

---
تم التطوير بواسطة: GitHub Copilot
تاريخ الإنشاء: 30 ديسمبر 2025
تاريخ التحديث: 3 يناير 2026 - تغيير الاسم من توصيلة إلى في طريقك (fe-tare2k)
