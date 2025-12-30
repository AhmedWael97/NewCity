# City Landing Page API - Implementation Complete

## Overview
This document describes the implementation of city landing page personalization features for the mobile application. These APIs enable the mobile app to display city-specific content including featured shops, latest shops, statistics, promotional banners, and custom theming.

## Implemented Features

### 1. Database Schema Changes

#### Shops Table Enhancements
Added featured shop functionality:
- `is_featured` (boolean): Whether the shop is featured
- `featured_priority` (integer): Display priority (higher = shown first)
- `featured_until` (datetime): Featured status expiration date
- `idx_featured_shops` (index): Composite index on (city_id, is_featured, featured_priority)

#### Cities Table Enhancements
Added theming and caching:
- `theme_config` (json): Custom theme configuration per city
- `featured_shops_count` (integer): Cached count of featured shops

#### City Banners Table (New)
Stores promotional banners per city:
- `id` (primary key)
- `city_id` (foreign key to cities)
- `title` (string): Banner title
- `description` (text): Banner description
- `image` (string): Banner image URL
- `link_type` (enum: 'internal', 'external', 'none'): Type of link
- `link_url` (string, nullable): Target URL
- `start_date` (datetime): Banner activation date
- `end_date` (datetime, nullable): Banner expiration date
- `priority` (integer): Display order (higher = shown first)
- `is_active` (boolean): Active status
- `idx_city_banners_active` (index): Composite index on (city_id, is_active, start_date, end_date, priority)

### 2. Models Created/Updated

#### CityBanner Model
**Location:** `app/Models/CityBanner.php`

**Relationships:**
- `belongsTo(City::class)`: Belongs to a city

**Scopes:**
- `active()`: Filters active banners within date range
- `forCity($cityId)`: Filters banners for specific city

**Static Methods:**
- `getActiveBannersForCity($cityId)`: Returns active banners ordered by priority

**Helper Methods:**
- `isActive()`: Checks if banner is currently active

#### Shop Model Updates
**Location:** `app/Models/Shop.php`

**New Fields:**
- Added `is_featured`, `featured_priority`, `featured_until` to fillable and casts

**New Scopes:**
- `activeFeatured()`: Returns featured shops with valid dates, ordered by priority DESC
- `latest($days = 30)`: Returns shops created in the last N days
- `forCity($cityId)`: Filters shops by city

**Helper Methods:**
- `isFeatured()`: Checks if shop is currently featured

#### City Model Updates
**Location:** `app/Models/City.php`

**New Fields:**
- Added `theme_config` (array cast) and `featured_shops_count` (integer cast)

**New Relationships:**
- `banners()`: hasMany relationship to CityBanner
- `activeBanners()`: hasMany relationship with date filtering and priority ordering

### 3. API Endpoints

All endpoints are prefixed with `/api/v1/cities/{city}/`

#### 3.1 Featured Shops
**GET** `/api/v1/cities/{city}/featured-shops`

Returns paginated list of featured shops for a city.

**Query Parameters:**
- `limit` (integer, default: 10): Number of shops per page
- `page` (integer, default: 1): Page number

**Response:**
```json
{
  "success": true,
  "data": {
    "shops": [
      {
        "id": 1,
        "name": "Shop Name",
        "slug": "shop-slug",
        "description": "Shop description",
        "rating": 4.5,
        "review_count": 120,
        "featured_until": "2024-12-31T23:59:59Z",
        "category": {
          "id": 1,
          "name": "Category Name",
          "icon": "icon-url"
        },
        "city": {
          "id": 1,
          "name": "City Name"
        }
      }
    ],
    "total": 25,
    "current_page": 1,
    "last_page": 3
  }
}
```

**Features:**
- Only returns shops with `is_featured = true`
- Filters by `featured_until` date (must be null or in future)
- Ordered by `featured_priority DESC` (highest priority first)
- Includes category and city relationships
- Paginated results

#### 3.2 Latest Shops
**GET** `/api/v1/cities/{city}/latest-shops`

Returns paginated list of recently added shops.

**Query Parameters:**
- `limit` (integer, default: 15): Number of shops per page
- `days` (integer, default: 30): Number of days to look back
- `page` (integer, default: 1): Page number

**Response:**
```json
{
  "success": true,
  "data": {
    "shops": [
      {
        "id": 2,
        "name": "New Shop",
        "slug": "new-shop",
        "description": "New shop description",
        "category_id": 1,
        "city_id": 1,
        "rating": 4.2,
        "review_count": 15,
        "created_at": "2024-11-10T10:30:00Z",
        "category": {
          "id": 1,
          "name": "Category Name",
          "icon": "icon-url"
        }
      }
    ],
    "total": 42,
    "current_page": 1,
    "last_page": 3
  }
}
```

**Features:**
- Returns shops created within last N days
- Ordered by `created_at DESC` (newest first)
- Includes category relationship
- Paginated results

#### 3.3 City Statistics
**GET** `/api/v1/cities/{city}/statistics`

Returns aggregated statistics for a city.

**Response:**
```json
{
  "success": true,
  "data": {
    "total_shops": 150,
    "active_shops": 142,
    "total_categories": 25,
    "total_reviews": 1250,
    "average_rating": 4.35,
    "new_shops_this_month": 8,
    "featured_shops_count": 12
  }
}
```

**Features:**
- Cached for 1 hour (3600 seconds)
- Cache key: `city_statistics_{city_id}`
- Includes comprehensive statistics:
  - Total shops count
  - Active shops count
  - Unique categories count
  - Total reviews sum
  - Average rating (rounded to 2 decimals)
  - New shops added this month
  - Featured shops count

#### 3.4 City Banners
**GET** `/api/v1/cities/{city}/banners`

Returns active promotional banners for a city.

**Response:**
```json
{
  "success": true,
  "data": {
    "banners": [
      {
        "id": 1,
        "title": "Summer Sale 2024",
        "description": "Get up to 50% off on selected items",
        "image": "https://example.com/banner.jpg",
        "link_type": "internal",
        "link_url": "/shops/summer-deals",
        "start_date": "2024-06-01T00:00:00Z",
        "end_date": "2024-08-31T23:59:59Z",
        "priority": 10
      }
    ]
  }
}
```

**Features:**
- Only returns active banners (`is_active = true`)
- Filters by date range (current date between start_date and end_date)
- Ordered by `priority DESC` (highest priority first)
- Link types: 'internal', 'external', 'none'

#### 3.5 Enhanced City Details
**GET** `/api/v1/cities/{city}`

Enhanced to include theme configuration and basic statistics.

**New Fields in Response:**
```json
{
  "success": true,
  "data": {
    "city": {
      "id": 1,
      "name": "City Name",
      "slug": "city-slug",
      "theme_config": {
        "primary_color": "#FF5733",
        "secondary_color": "#33FF57",
        "banner_style": "carousel",
        "show_featured_section": true
      }
    },
    "categories": [...],
    "shops_count": 142,
    "theme_config": {
      "primary_color": "#FF5733",
      "secondary_color": "#33FF57",
      "banner_style": "carousel",
      "show_featured_section": true
    },
    "statistics": {
      "total_shops": 150,
      "active_shops": 142,
      "featured_shops_count": 12
    }
  }
}
```

### 4. Routes
**Location:** `routes/api.php`

Added the following routes under `/api/v1/cities/{city}/`:
```php
Route::get('/cities/{city}/featured-shops', [CityController::class, 'featuredShops']);
Route::get('/cities/{city}/latest-shops', [CityController::class, 'latestShops']);
Route::get('/cities/{city}/statistics', [CityController::class, 'statistics']);
Route::get('/cities/{city}/banners', [CityController::class, 'banners']);
```

### 5. Swagger Documentation

Complete OpenAPI/Swagger documentation has been generated for all endpoints with:
- Full request parameter descriptions
- Complete response schemas
- Example values
- Error responses

**Access Swagger UI:** `http://your-domain/api/documentation`

## Performance Optimizations

### 1. Database Indexes
- `idx_featured_shops` on shops table: Optimizes featured shop queries
- `idx_city_banners_active` on city_banners table: Optimizes banner queries

### 2. Caching
- **City Statistics:** Cached for 1 hour (reduce database load)
- **City Selection Modal:** Cached for 30 minutes (existing)

### 3. Query Optimization
- Uses eager loading with `with()` to prevent N+1 queries
- Selects only necessary columns where possible
- Uses database-level filtering and sorting

### 4. Pagination
- All list endpoints support pagination
- Configurable page size via `limit` parameter

## Theme Configuration

The `theme_config` JSON field on cities table allows per-city customization:

**Example Theme Config:**
```json
{
  "primary_color": "#FF5733",
  "secondary_color": "#33FF57",
  "accent_color": "#FFC300",
  "banner_style": "carousel",
  "show_featured_section": true,
  "show_latest_section": true,
  "show_statistics": true,
  "featured_shops_limit": 10,
  "latest_shops_limit": 15,
  "category_display_style": "grid"
}
```

## Mobile App Integration Guide

### 1. City Landing Page Layout

```
┌─────────────────────────────┐
│    City Header & Theme      │
├─────────────────────────────┤
│  Promotional Banners        │ ← GET /cities/{city}/banners
│  (Carousel/Slider)          │
├─────────────────────────────┤
│  City Statistics Widget     │ ← GET /cities/{city}/statistics
├─────────────────────────────┤
│  Featured Shops Section     │ ← GET /cities/{city}/featured-shops
│  (Horizontal Scroll)        │
├─────────────────────────────┤
│  Latest Shops Section       │ ← GET /cities/{city}/latest-shops
│  (Vertical List)            │
├─────────────────────────────┤
│  Categories Grid            │ ← GET /cities/{city} (existing)
└─────────────────────────────┘
```

### 2. Recommended Loading Strategy

1. **Initial Load:** Fetch city details with theme config
   ```
   GET /api/v1/cities/{city}
   ```

2. **Parallel Requests:** Load sections in parallel
   ```
   GET /api/v1/cities/{city}/banners
   GET /api/v1/cities/{city}/featured-shops?limit=10
   GET /api/v1/cities/{city}/latest-shops?limit=15&days=30
   GET /api/v1/cities/{city}/statistics
   ```

3. **Apply Theme:** Use `theme_config` to style the page

4. **Lazy Loading:** Load more shops on scroll
   ```
   GET /api/v1/cities/{city}/featured-shops?page=2
   GET /api/v1/cities/{city}/latest-shops?page=2
   ```

### 3. Caching Strategy (Mobile App)

- **Theme Config:** Cache indefinitely, refresh on app start
- **Banners:** Cache for 1 hour
- **Statistics:** Cache for 1 hour
- **Featured Shops:** Cache for 30 minutes
- **Latest Shops:** Cache for 15 minutes

### 4. Error Handling

All endpoints return consistent error format:
```json
{
  "success": false,
  "message": "Error message"
}
```

**Common Error Codes:**
- `404`: City not found
- `500`: Server error

## Admin Panel Features (To Be Implemented)

### 1. Featured Shops Management
- Mark/unmark shops as featured
- Set featured priority (1-100)
- Set featured expiration date
- Bulk feature/unfeature operations

### 2. City Banners Management
- Create/edit/delete banners per city
- Upload banner images
- Set banner schedule (start/end dates)
- Set banner priority
- Preview banner on mobile

### 3. City Theme Configuration
- Visual theme editor per city
- Color picker for primary/secondary colors
- Toggle section visibility
- Preview theme changes

### 4. Analytics Dashboard
- Track banner click-through rates
- Monitor featured shop performance
- View city statistics trends

## Testing

### Manual Testing Checklist

- [ ] Featured shops endpoint returns correct data
- [ ] Latest shops respects `days` parameter
- [ ] Statistics are accurate and cached
- [ ] Banners filter by date range correctly
- [ ] City details include theme config
- [ ] All endpoints handle invalid city ID/slug
- [ ] Pagination works correctly
- [ ] Swagger documentation is accurate

### API Testing Examples

**Test Featured Shops:**
```bash
curl -X GET "http://localhost:8000/api/v1/cities/cairo/featured-shops?limit=5&page=1"
```

**Test Latest Shops:**
```bash
curl -X GET "http://localhost:8000/api/v1/cities/cairo/latest-shops?days=7&limit=10"
```

**Test Statistics:**
```bash
curl -X GET "http://localhost:8000/api/v1/cities/cairo/statistics"
```

**Test Banners:**
```bash
curl -X GET "http://localhost:8000/api/v1/cities/cairo/banners"
```

## Migration Commands

All migrations have been run successfully:
```bash
php artisan migrate
```

**Migration Files:**
1. `2025_11_10_155843_add_featured_columns_to_shops_table.php`
2. `2025_11_10_155920_add_theme_config_to_cities_table.php`
3. `2025_11_10_155925_create_city_banners_table.php`

## File Changes Summary

### New Files Created (1)
- `app/Models/CityBanner.php`

### Files Modified (4)
- `app/Models/Shop.php` (added featured fields and scopes)
- `app/Models/City.php` (added theme config and banner relationships)
- `app/Http/Controllers/Api/CityController.php` (added 4 new endpoints)
- `routes/api.php` (added 4 new routes)

### Database Migrations (3)
- `database/migrations/2025_11_10_155843_add_featured_columns_to_shops_table.php`
- `database/migrations/2025_11_10_155920_add_theme_config_to_cities_table.php`
- `database/migrations/2025_11_10_155925_create_city_banners_table.php`

## Next Steps (Recommended)

1. **Admin Panel Implementation**
   - Create `AdminCityBannerController` with CRUD operations
   - Add featured shop management to `AdminShopController`
   - Create views for banner and theme management

2. **Seeder Creation**
   - Create `CityBannerSeeder` for sample data
   - Add featured shops to existing seeders

3. **Image Storage**
   - Configure storage for banner images
   - Add image upload/validation

4. **Analytics**
   - Add banner impression/click tracking
   - Track featured shop click-through rates

5. **Automated Testing**
   - Create feature tests for all endpoints
   - Add unit tests for model scopes

## API Documentation

Complete Swagger/OpenAPI documentation is available at:
- **URL:** `http://your-domain/api/documentation`
- **Format:** JSON and YAML
- **Location:** `storage/api-docs/api-docs.json`

## Support & Questions

For questions or issues with the city landing page APIs:
1. Check this documentation first
2. Review Swagger documentation at `/api/documentation`
3. Check the implementation code in `app/Http/Controllers/Api/CityController.php`
4. Review model scopes in `app/Models/Shop.php` and `app/Models/City.php`

---

**Implementation Date:** November 10, 2025
**Status:** ✅ Complete and Ready for Use
**Swagger Documentation:** ✅ Generated
**Database Migrations:** ✅ Run Successfully
