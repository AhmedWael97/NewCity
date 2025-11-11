# City Landing Page API - Sample Data & Testing Guide

## Sample Data Setup

### 1. Mark Shops as Featured

```sql
-- Mark some shops as featured with different priorities
UPDATE shops 
SET is_featured = 1, 
    featured_priority = 10, 
    featured_until = '2024-12-31 23:59:59'
WHERE city_id = 1 
LIMIT 5;

-- Add more featured shops with lower priority
UPDATE shops 
SET is_featured = 1, 
    featured_priority = 5, 
    featured_until = '2024-11-30 23:59:59'
WHERE city_id = 1 
AND is_featured = 0
LIMIT 3;
```

### 2. Add Sample City Banners

```sql
-- Insert promotional banners
INSERT INTO city_banners (city_id, title, description, image, link_type, link_url, start_date, end_date, priority, is_active, created_at, updated_at) VALUES
(1, 'Summer Sale 2024', 'Get up to 50% off on selected items', 'https://via.placeholder.com/800x400?text=Summer+Sale', 'internal', '/shops/summer-deals', '2024-06-01 00:00:00', '2024-08-31 23:59:59', 10, 1, NOW(), NOW()),
(1, 'New Shop Opening', 'Visit our newly opened shops in downtown', 'https://via.placeholder.com/800x400?text=New+Shops', 'internal', '/shops/new', '2024-11-01 00:00:00', NULL, 8, 1, NOW(), NOW()),
(1, 'Black Friday Coming Soon', 'Save the date for Black Friday mega deals', 'https://via.placeholder.com/800x400?text=Black+Friday', 'external', 'https://example.com/black-friday', '2024-11-15 00:00:00', '2024-11-30 23:59:59', 9, 1, NOW(), NOW());
```

### 3. Update City Theme Configuration

```sql
-- Add theme configuration to a city
UPDATE cities 
SET theme_config = JSON_OBJECT(
    'primary_color', '#FF5733',
    'secondary_color', '#33FF57',
    'accent_color', '#FFC300',
    'banner_style', 'carousel',
    'show_featured_section', true,
    'show_latest_section', true,
    'show_statistics', true,
    'featured_shops_limit', 10,
    'latest_shops_limit', 15,
    'category_display_style', 'grid'
)
WHERE id = 1;
```

## API Testing Examples

### Test 1: Featured Shops (Basic)
```bash
curl -X GET "http://localhost:8000/api/v1/cities/1/featured-shops" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "shops": [
      {
        "id": 1,
        "name": "Example Shop",
        "slug": "example-shop",
        "description": "A great shop",
        "rating": 4.5,
        "review_count": 120,
        "featured_until": "2024-12-31T23:59:59.000000Z",
        "category": {
          "id": 1,
          "name": "Restaurants",
          "icon": "restaurant-icon.png"
        }
      }
    ],
    "total": 8,
    "current_page": 1,
    "last_page": 1
  }
}
```

### Test 2: Featured Shops (With Pagination)
```bash
curl -X GET "http://localhost:8000/api/v1/cities/1/featured-shops?limit=3&page=1" \
  -H "Accept: application/json"
```

### Test 3: Latest Shops (Last 7 Days)
```bash
curl -X GET "http://localhost:8000/api/v1/cities/1/latest-shops?days=7&limit=10" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "shops": [
      {
        "id": 15,
        "name": "New Shop",
        "slug": "new-shop",
        "description": "Just opened",
        "category_id": 1,
        "city_id": 1,
        "rating": 0,
        "review_count": 0,
        "created_at": "2024-11-09T10:30:00.000000Z",
        "category": {
          "id": 1,
          "name": "Restaurants",
          "icon": "restaurant-icon.png"
        }
      }
    ],
    "total": 3,
    "current_page": 1,
    "last_page": 1
  }
}
```

### Test 4: City Statistics
```bash
curl -X GET "http://localhost:8000/api/v1/cities/1/statistics" \
  -H "Accept: application/json"
```

**Expected Response:**
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
    "featured_shops_count": 8
  }
}
```

### Test 5: City Banners
```bash
curl -X GET "http://localhost:8000/api/v1/cities/1/banners" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "banners": [
      {
        "id": 1,
        "title": "Summer Sale 2024",
        "description": "Get up to 50% off on selected items",
        "image": "https://via.placeholder.com/800x400?text=Summer+Sale",
        "link_type": "internal",
        "link_url": "/shops/summer-deals",
        "start_date": "2024-06-01T00:00:00.000000Z",
        "end_date": "2024-08-31T23:59:59.000000Z",
        "priority": 10
      }
    ]
  }
}
```

### Test 6: Enhanced City Details
```bash
curl -X GET "http://localhost:8000/api/v1/cities/1" \
  -H "Accept: application/json"
```

**Expected Response (Enhanced):**
```json
{
  "success": true,
  "data": {
    "city": {
      "id": 1,
      "name": "Cairo",
      "slug": "cairo",
      "country": "Egypt",
      "state": "Cairo Governorate",
      "is_active": true,
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
      "featured_shops_count": 8
    }
  }
}
```

## Testing with Postman

### Import Collection

Create a Postman collection with these requests:

1. **Get Featured Shops**
   - Method: GET
   - URL: `{{base_url}}/api/v1/cities/1/featured-shops`
   - Params: `limit=10`, `page=1`

2. **Get Latest Shops**
   - Method: GET
   - URL: `{{base_url}}/api/v1/cities/1/latest-shops`
   - Params: `days=30`, `limit=15`, `page=1`

3. **Get City Statistics**
   - Method: GET
   - URL: `{{base_url}}/api/v1/cities/1/statistics`

4. **Get City Banners**
   - Method: GET
   - URL: `{{base_url}}/api/v1/cities/1/banners`

5. **Get City Details**
   - Method: GET
   - URL: `{{base_url}}/api/v1/cities/1`

### Environment Variables
```json
{
  "base_url": "http://localhost:8000"
}
```

## Mobile App Testing Checklist

### Featured Shops Section
- [ ] Shops are ordered by priority (highest first)
- [ ] Only shows shops with valid featured_until dates
- [ ] Category information is included
- [ ] Rating and review count displayed
- [ ] Pagination works correctly
- [ ] Tap on shop navigates to shop details

### Latest Shops Section
- [ ] Shows shops from specified days (default 30)
- [ ] Newest shops appear first
- [ ] Category information included
- [ ] "New" badge displayed for recent shops
- [ ] Pagination works correctly
- [ ] Tap on shop navigates to shop details

### Statistics Widget
- [ ] All statistics display correctly
- [ ] Numbers format nicely (e.g., 1,250 reviews)
- [ ] Average rating shows 2 decimal places
- [ ] Stats update when cache expires

### Banners Carousel
- [ ] Only active banners shown
- [ ] Banners ordered by priority
- [ ] Images load correctly
- [ ] Auto-scroll works (3-5 seconds)
- [ ] Tap on banner navigates correctly (internal/external)
- [ ] Respects link_type (none = no action)

### Theme Application
- [ ] Primary color applied to main elements
- [ ] Secondary color applied to accents
- [ ] Banner style (carousel/static) respected
- [ ] Section visibility flags respected
- [ ] Layout matches theme configuration

## Performance Testing

### Cache Verification
```bash
# First request (no cache)
time curl "http://localhost:8000/api/v1/cities/1/statistics"

# Second request (from cache - should be faster)
time curl "http://localhost:8000/api/v1/cities/1/statistics"
```

### Load Testing with Apache Bench
```bash
# Test 1000 requests with 10 concurrent users
ab -n 1000 -c 10 http://localhost:8000/api/v1/cities/1/featured-shops

# Test statistics endpoint (should be fast due to caching)
ab -n 1000 -c 10 http://localhost:8000/api/v1/cities/1/statistics
```

### Expected Performance
- Featured Shops: < 200ms
- Latest Shops: < 200ms
- Statistics (cached): < 50ms
- Banners: < 100ms
- City Details: < 150ms

## Error Testing

### Test Invalid City
```bash
curl -X GET "http://localhost:8000/api/v1/cities/999999/featured-shops"
```

**Expected Response:**
```json
{
  "success": false,
  "message": "City not found"
}
```

### Test Invalid Parameters
```bash
# Invalid limit (should handle gracefully)
curl -X GET "http://localhost:8000/api/v1/cities/1/featured-shops?limit=-1"

# Invalid page (should handle gracefully)
curl -X GET "http://localhost:8000/api/v1/cities/1/featured-shops?page=0"
```

## Database Verification Queries

### Check Featured Shops
```sql
-- Count featured shops per city
SELECT city_id, COUNT(*) as featured_count
FROM shops
WHERE is_featured = 1
AND (featured_until IS NULL OR featured_until > NOW())
GROUP BY city_id;
```

### Check Banners
```sql
-- Count active banners per city
SELECT city_id, COUNT(*) as banner_count
FROM city_banners
WHERE is_active = 1
AND start_date <= NOW()
AND (end_date IS NULL OR end_date >= NOW())
GROUP BY city_id;
```

### Check Theme Configs
```sql
-- View cities with theme configuration
SELECT id, name, theme_config
FROM cities
WHERE theme_config IS NOT NULL;
```

## Troubleshooting

### Issue: No Featured Shops Returned
**Check:**
1. Are shops marked as featured? (`is_featured = 1`)
2. Is `featured_until` in the future or NULL?
3. Does city have active shops?

**Query:**
```sql
SELECT id, name, is_featured, featured_priority, featured_until
FROM shops
WHERE city_id = 1 AND is_featured = 1;
```

### Issue: Statistics Always Zero
**Check:**
1. Does city have shops?
2. Are relationships set correctly?
3. Is cache corrupted?

**Solution:**
```bash
# Clear cache
php artisan cache:clear
```

### Issue: Banners Not Showing
**Check:**
1. Are banners marked as active? (`is_active = 1`)
2. Is current date between `start_date` and `end_date`?
3. Does banner belong to correct city?

**Query:**
```sql
SELECT * FROM city_banners
WHERE city_id = 1
AND is_active = 1
AND start_date <= NOW()
AND (end_date IS NULL OR end_date >= NOW());
```

## Success Criteria

✅ All endpoints return 200 status  
✅ Featured shops ordered by priority  
✅ Latest shops show recent additions  
✅ Statistics accurate and cached  
✅ Banners filter by date correctly  
✅ Theme config returned properly  
✅ No N+1 query issues  
✅ Response times acceptable  
✅ Error handling works correctly  

---

**Status**: Ready for Testing  
**Sample Data**: Included above  
**Test Coverage**: Comprehensive  
