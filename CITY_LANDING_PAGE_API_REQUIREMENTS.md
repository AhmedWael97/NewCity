# City Landing Page - API Requirements

## Overview
This document outlines the required API endpoints for implementing a personalized city-based landing page experience.

## Required API Endpoints

### 1. Featured Shops Endpoint
**Purpose:** Get featured/promoted shops for a specific city to display in the landing page carousel.

**Endpoint:** `GET /api/v1/cities/{city_id}/featured-shops`

**Query Parameters:**
- `limit` (optional, default: 10) - Number of featured shops to return
- `page` (optional, default: 1) - Page number for pagination

**Response Format:**
```json
{
  "success": true,
  "data": {
    "shops": [
      {
        "id": 1,
        "name": "Shop Name",
        "slug": "shop-name",
        "description": "Shop description",
        "category": {
          "id": 1,
          "name": "Category Name",
          "icon": "🏪"
        },
        "rating": 4.5,
        "review_count": 120,
        "is_featured": true,
        "is_verified": true,
        "featured_image": "https://example.com/image.jpg",
        "featured_until": "2025-12-31T23:59:59Z"
      }
    ],
    "total": 25,
    "current_page": 1,
    "last_page": 3
  }
}
```

**Backend Implementation Notes:**
- Filter shops by `city_id` and `is_featured = true`
- Order by `featured_priority DESC, created_at DESC`
- Include only active and verified shops
- Consider adding a `featured_until` timestamp to auto-expire featured status

---

### 2. Latest Shops Endpoint
**Purpose:** Get recently added shops in a specific city.

**Endpoint:** `GET /api/v1/cities/{city_id}/latest-shops`

**Query Parameters:**
- `limit` (optional, default: 15) - Number of shops to return
- `page` (optional, default: 1) - Page number
- `days` (optional, default: 30) - Return shops added in the last N days

**Response Format:**
```json
{
  "success": true,
  "data": {
    "shops": [
      {
        "id": 1,
        "name": "Shop Name",
        "slug": "shop-name",
        "description": "Short description",
        "category": {
          "id": 1,
          "name": "Category Name"
        },
        "rating": 4.2,
        "review_count": 45,
        "created_at": "2025-11-05T10:30:00Z"
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 4
  }
}
```

**Backend Implementation Notes:**
- Filter by `city_id` and `created_at >= NOW() - {days} days`
- Order by `created_at DESC`
- Include only active shops
- Return basic shop information (no need for full details)

---

### 3. City Statistics Endpoint
**Purpose:** Get aggregated statistics for a city's landing page.

**Endpoint:** `GET /api/v1/cities/{city_id}/statistics`

**Response Format:**
```json
{
  "success": true,
  "data": {
    "total_shops": 1250,
    "active_shops": 1180,
    "total_categories": 45,
    "total_reviews": 15420,
    "average_rating": 4.3,
    "new_shops_this_month": 23,
    "featured_shops_count": 15
  }
}
```

**Backend Implementation Notes:**
- Cache these statistics (update hourly or daily)
- Count only active and verified shops
- Include shops added in the current month for `new_shops_this_month`

---

### 4. City Details with Theme
**Endpoint:** `GET /api/v1/cities/{city_id}`

**Enhanced Response (add to existing):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Cairo",
    "name_ar": "القاهرة",
    "slug": "cairo",
    // ... existing fields ...
    "theme_config": {
      "primary_color": "#667eea",
      "secondary_color": "#764ba2",
      "accent_color": "#f093fb",
      "background_style": "gradient",
      "font_family": "Cairo",
      "hero_image": "https://example.com/cairo-hero.jpg",
      "custom_css": null,
      "enable_custom_styling": false
    },
    "statistics": {
      "total_shops": 1250,
      "active_shops": 1180
    }
  }
}
```

**Backend Implementation Notes:**
- Add `theme_config` JSON column to `cities` table
- Allow admins to customize landing page appearance per city
- Return shop count statistics with city details

---

### 5. City Banner/Announcements (Optional)
**Purpose:** Display city-specific announcements or promotions on the landing page.

**Endpoint:** `GET /api/v1/cities/{city_id}/banners`

**Response Format:**
```json
{
  "success": true,
  "data": {
    "banners": [
      {
        "id": 1,
        "title": "عروض الجمعة البيضاء",
        "description": "خصومات تصل إلى 50% على جميع المتاجر",
        "image": "https://example.com/banner.jpg",
        "link_type": "internal", // or "external"
        "link_url": "/shops?promotion=black-friday",
        "start_date": "2025-11-20T00:00:00Z",
        "end_date": "2025-11-30T23:59:59Z",
        "priority": 1
      }
    ]
  }
}
```

**Backend Implementation Notes:**
- New `city_banners` table
- Fields: `id`, `city_id`, `title`, `description`, `image`, `link_type`, `link_url`, `start_date`, `end_date`, `priority`, `is_active`
- Filter by current date and `is_active = true`
- Order by `priority ASC`

---

## Database Schema Updates

### Cities Table Additions
```sql
ALTER TABLE cities ADD COLUMN theme_config JSON NULL;
ALTER TABLE cities ADD COLUMN featured_shops_count INT DEFAULT 0;
```

### Shops Table Additions  
```sql
ALTER TABLE shops ADD COLUMN is_featured BOOLEAN DEFAULT false;
ALTER TABLE shops ADD COLUMN featured_priority INT DEFAULT 0;
ALTER TABLE shops ADD COLUMN featured_until TIMESTAMP NULL;
ALTER TABLE shops ADD INDEX idx_featured (city_id, is_featured, featured_priority);
```

### New City Banners Table
```sql
CREATE TABLE city_banners (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  city_id BIGINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  image VARCHAR(500),
  link_type ENUM('internal', 'external', 'none') DEFAULT 'none',
  link_url VARCHAR(500),
  start_date TIMESTAMP NOT NULL,
  end_date TIMESTAMP NOT NULL,
  priority INT DEFAULT 0,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
  INDEX idx_city_active_dates (city_id, is_active, start_date, end_date)
);
```

---

## Implementation Priority

### Phase 1 (Required for MVP)
1. ✅ Existing `/shops` endpoint with `city_id` filter (Already implemented)
2. 🔴 Add `is_featured` filter to `/shops` endpoint
3. 🔴 Create `/cities/{city_id}/featured-shops` endpoint
4. 🔴 Create `/cities/{city_id}/latest-shops` endpoint

### Phase 2 (Enhanced Experience)
1. 🔴 City statistics endpoint
2. 🔴 Theme configuration in city details
3. 🔴 City-specific banners/announcements

### Phase 3 (Future Enhancements)
1. 🔴 Trending shops based on views/reviews
2. 🔴 Personalized recommendations
3. 🔴 Category-specific featured sections
4. 🔴 Seasonal promotions management

---

## Mobile App Changes (Completed)

### ✅ Implemented Features:
1. **StorageService** - Persistent city selection using SharedPreferences
2. **CityProvider** - Enhanced with city persistence methods:
   - `setSelectedCity()` - Save city selection
   - `loadSelectedCity()` - Load saved city
   - `hasSelectedCity()` - Check if city exists
3. **CitySelectionScreen** - First-time city selection with search and list
4. **SplashScreen** - Check city selection and route appropriately
5. **CityLandingPage** - Display city-specific content:
   - Featured shops carousel
   - Latest shops list
   - City header with theme
   - Pull-to-refresh
   - Change city option
6. **HomeScreen** - Wrapper that shows CityLandingPage

### Current Behavior:
1. User opens app → Splash screen checks for selected city
2. If no city → Show CitySelectionScreen
3. User selects city → Saved to SharedPreferences
4. Navigate to MainNavigator → HomeScreen shows CityLandingPage
5. Next app open → Auto-load saved city and go directly to landing page
6. User can change city anytime via location button in app bar

---

## Testing Checklist for Backend Developers

- [ ] Create featured shops endpoint
- [ ] Add `is_featured` column to shops table
- [ ] Update shop admin panel to manage featured status
- [ ] Test featured shops response with pagination
- [ ] Create latest shops endpoint with date filtering
- [ ] Add city statistics caching mechanism
- [ ] Test city theme configuration (optional)
- [ ] Create city banners management (optional)
- [ ] Update API documentation
- [ ] Test all endpoints with real data

---

## Notes
- For now, the mobile app uses existing `/shops` endpoint and displays first few shops as "featured"
- Implementing proper featured shops endpoint will improve user experience significantly
- City theme configuration is optional but recommended for better branding
- Consider rate limiting on statistics endpoint if not cached
