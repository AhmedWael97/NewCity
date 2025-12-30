# City Landing Page API - Quick Reference

## Base URL
```
/api/v1/cities/{city}/
```

## New Endpoints

### 1. Featured Shops
```http
GET /api/v1/cities/{city}/featured-shops?limit=10&page=1
```
Returns paginated featured shops with priority ordering.

### 2. Latest Shops
```http
GET /api/v1/cities/{city}/latest-shops?limit=15&days=30&page=1
```
Returns recently added shops (last N days).

### 3. City Statistics
```http
GET /api/v1/cities/{city}/statistics
```
Returns aggregated statistics (cached 1 hour).

### 4. City Banners
```http
GET /api/v1/cities/{city}/banners
```
Returns active promotional banners.

### 5. Enhanced City Details
```http
GET /api/v1/cities/{city}
```
Now includes `theme_config` and basic statistics.

## Quick Test Commands

Replace `{city}` with a city ID or slug (e.g., `1` or `cairo`):

```bash
# Featured Shops
curl http://localhost:8000/api/v1/cities/1/featured-shops

# Latest Shops
curl http://localhost:8000/api/v1/cities/1/latest-shops?days=7

# Statistics
curl http://localhost:8000/api/v1/cities/1/statistics

# Banners
curl http://localhost:8000/api/v1/cities/1/banners

# City Details (enhanced)
curl http://localhost:8000/api/v1/cities/1
```

## Response Format

All endpoints return:
```json
{
  "success": true,
  "data": { ... }
}
```

## Database Schema

### New Columns on `shops` table:
- `is_featured` (boolean)
- `featured_priority` (integer)
- `featured_until` (datetime)

### New Columns on `cities` table:
- `theme_config` (json)
- `featured_shops_count` (integer)

### New Table: `city_banners`
- Complete banner management system

## Model Scopes

### Shop Model
```php
Shop::activeFeatured()    // Featured shops with valid dates
Shop::latest($days)       // Shops from last N days
Shop::forCity($cityId)    // Shops in specific city
```

### CityBanner Model
```php
CityBanner::active()           // Active banners
CityBanner::forCity($cityId)   // Banners for city
```

## Features

✅ Paginated results  
✅ Caching (statistics: 1 hour)  
✅ Database indexes for performance  
✅ Swagger documentation  
✅ Eager loading (no N+1 queries)  
✅ Theme configuration per city  
✅ Promotional banners system  

## Swagger Documentation

View complete API documentation:
```
http://localhost:8000/api/documentation
```

## Status

**✅ Implementation Complete**
- All migrations run successfully
- All models created/updated
- All endpoints implemented
- Swagger documentation generated
- Routes registered

**📋 Next Steps (Optional)**
- Admin panel for featured shops
- Admin panel for city banners
- Banner image upload functionality
- Analytics tracking
