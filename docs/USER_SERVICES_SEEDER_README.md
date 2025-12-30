# User Services Seeder - Documentation

## Overview
Successfully created a seeder that adds users with Arabic service listings to city ID 4.

## What Was Created

### Users (5 total)
All users belong to city ID 4 with `user_type: 'shop_owner'`

1. **محمد أحمد السباك** (mohammed.plumber@example.com)
2. **خالد عبدالله للتكييف** (khaled.ac@example.com)
3. **عمر حسن الكهربائي** (omar.electric@example.com)
4. **سعد محمود للنقليات** (saad.moving@example.com)
5. **فاطمة علي للتنظيف** (fatima.cleaning@example.com)

### Services (10 total - 2 per user)

1. **خدمات السباكة والصيانة المنزلية** - Plumbing services
2. **تركيب وصيانة التكييفات** - AC installation and maintenance
3. **أعمال الكهرباء والإنارة** - Electrical work
4. **نقل الأثاث والعفش** - Furniture moving
5. **خدمات التنظيف المنزلي الشامل** - Complete home cleaning
6. **صيانة وبرمجة الحاسوب** - Computer maintenance and programming
7. **تصليح وصيانة السيارات** - Car repair and maintenance
8. **تصميم وتنسيق الحدائق** - Garden design and landscaping
9. **دروس خصوصية في الرياضيات** - Private math tutoring
10. **تدريب اللياقة البدنية المنزلي** - Home fitness training

## Service Features

Each service includes:
- ✅ Full Arabic title and description
- ✅ Pricing information (price_from, price_to)
- ✅ Pricing type (hourly, fixed, negotiable)
- ✅ Contact information (phone, WhatsApp)
- ✅ Placeholder images (2 per service via picsum.photos)
- ✅ Availability schedule (Saturday-Friday)
- ✅ Service areas (city-wide, 50km radius)
- ✅ Experience years
- ✅ Active and verified status
- ✅ Auto-generated slugs

## Running the Seeder

```powershell
php artisan db:seed --class=UserServicesSeeder
```

## Testing the API

Get all services for city 4:
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/v1/cities/4/services?sort=latest&per_page=10" -Method GET
```

## File Location
`database/seeders/UserServicesSeeder.php`

## User Credentials
- Email: See list above (e.g., mohammed.plumber@example.com)
- Password: `password123` (for all users)

## Notes
- All services are linked to city ID: 4
- Services use placeholder images from picsum.photos
- All users are verified and active
- Service categories are randomly assigned from existing categories
- Images are external URLs (not stored locally)
