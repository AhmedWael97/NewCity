# News API Documentation

## Overview
Complete REST API implementation for News/Articles management in the City application. This API provides endpoints for retrieving news articles, categories, and filtering by various criteria.

## Implementation Date
November 29, 2025

---

## Files Created

### 1. Controller
**Location:** `app/Http/Controllers/Api/NewsController.php`

Implements the following methods:
- `index()` - Get paginated news list with filters
- `show($slug)` - Get single news article by slug
- `categories()` - Get all active news categories
- `latest()` - Get latest news articles
- `featured()` - Get most viewed/featured news
- `byCategory($slug)` - Get news by category slug
- `byCity($cityId)` - Get news by city ID

### 2. Resources
**Location:** `app/Http/Resources/`

- `NewsResource.php` - Transform news model to API response format
- `NewsCategoryResource.php` - Transform news category model to API response format

### 3. Routes
**Location:** `routes/api.php`

All routes are public (no authentication required) under `/api/v1/news` prefix.

---

## API Endpoints

### Base URL
```
/api/v1/news
```

### 1. Get News List
```
GET /api/v1/news
```

**Query Parameters:**
- `page` (integer, default: 1) - Page number
- `per_page` (integer, default: 15) - Items per page
- `category_id` (integer) - Filter by category ID
- `city_id` (integer) - Filter by city ID
- `search` (string) - Search in title, description, content
- `sort` (string: latest|popular|oldest, default: latest) - Sort order

**Response:**
```json
{
  "success": true,
  "data": {
    "news": [
      {
        "id": 1,
        "title": "News Title",
        "slug": "news-title",
        "description": "Short description",
        "content": "Full article content",
        "excerpt": "Auto-generated excerpt...",
        "thumbnail_url": "https://domain.com/storage/news/thumbnails/image.jpg",
        "images_url": [
          "https://domain.com/storage/news/images/image1.jpg",
          "https://domain.com/storage/news/images/image2.jpg"
        ],
        "views_count": 150,
        "reading_time": 5,
        "published_at": "2025-11-29T10:00:00.000000Z",
        "category": {
          "id": 1,
          "name": "Technology",
          "slug": "technology"
        },
        "city": {
          "id": 1,
          "name": "New York",
          "slug": "new-york"
        },
        "created_at": "2025-11-29T09:00:00.000000Z",
        "updated_at": "2025-11-29T09:00:00.000000Z"
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 4,
    "per_page": 15
  }
}
```

---

### 2. Get Single News Article
```
GET /api/v1/news/{slug}
```

**Path Parameters:**
- `slug` (required) - News article slug

**Features:**
- Automatically increments view count
- Returns related news articles (up to 4 from same category)

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "News Title",
    "slug": "news-title",
    "description": "Short description",
    "content": "Full article content HTML...",
    "excerpt": "Auto-generated excerpt...",
    "thumbnail_url": "https://domain.com/storage/news/thumbnails/image.jpg",
    "images_url": [
      "https://domain.com/storage/news/images/image1.jpg"
    ],
    "views_count": 151,
    "reading_time": 5,
    "published_at": "2025-11-29T10:00:00.000000Z",
    "category": {
      "id": 1,
      "name": "Technology",
      "slug": "technology"
    },
    "city": {
      "id": 1,
      "name": "New York",
      "slug": "new-york"
    },
    "related_news": [
      {
        "id": 2,
        "title": "Related Article 1",
        "slug": "related-article-1",
        "description": "Description...",
        "thumbnail_url": "https://domain.com/storage/news/thumbnails/image2.jpg",
        "published_at": "2025-11-28T10:00:00.000000Z",
        "views_count": 100,
        "reading_time": 3
      }
    ],
    "created_at": "2025-11-29T09:00:00.000000Z",
    "updated_at": "2025-11-29T09:00:00.000000Z"
  }
}
```

---

### 3. Get News Categories
```
GET /api/v1/news/categories/list
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Technology",
      "slug": "technology",
      "is_active": true,
      "order": 1,
      "news_count": 25,
      "created_at": "2025-11-29T09:00:00.000000Z",
      "updated_at": "2025-11-29T09:00:00.000000Z"
    }
  ]
}
```

---

### 4. Get Latest News
```
GET /api/v1/news/latest
```

**Query Parameters:**
- `limit` (integer, default: 10) - Number of articles to return
- `city_id` (integer) - Filter by city ID

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Latest News",
      "slug": "latest-news",
      "description": "Description...",
      "thumbnail_url": "https://domain.com/storage/news/thumbnails/image.jpg",
      "published_at": "2025-11-29T10:00:00.000000Z"
    }
  ]
}
```

---

### 5. Get Featured News
```
GET /api/v1/news/featured
```

**Query Parameters:**
- `limit` (integer, default: 5) - Number of articles to return
- `city_id` (integer) - Filter by city ID

**Features:**
- Returns most viewed news articles

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Most Popular News",
      "slug": "most-popular-news",
      "thumbnail_url": "https://domain.com/storage/news/thumbnails/image.jpg",
      "views_count": 5000
    }
  ]
}
```

---

### 6. Get News by Category
```
GET /api/v1/news/category/{slug}
```

**Path Parameters:**
- `slug` (required) - Category slug

**Query Parameters:**
- `page` (integer, default: 1) - Page number
- `per_page` (integer, default: 15) - Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "category": {
      "id": 1,
      "name": "Technology",
      "slug": "technology"
    },
    "news": [
      {
        "id": 1,
        "title": "Tech News",
        "slug": "tech-news"
      }
    ],
    "total": 25,
    "current_page": 1,
    "last_page": 2,
    "per_page": 15
  }
}
```

---

### 7. Get News by City
```
GET /api/v1/cities/{city_id}/news
```

**Path Parameters:**
- `city_id` (required) - City ID

**Query Parameters:**
- `page` (integer, default: 1) - Page number
- `per_page` (integer, default: 15) - Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "news": [
      {
        "id": 1,
        "title": "City News",
        "slug": "city-news"
      }
    ],
    "total": 15,
    "current_page": 1,
    "last_page": 1,
    "per_page": 15
  }
}
```

---

## Features

### 1. **Automatic View Tracking**
- Each time a news article is viewed via the API, the view count is incremented
- No authentication required

### 2. **Smart Filtering**
- Filter by category
- Filter by city
- Full-text search across title, description, and content
- Multiple sort options (latest, popular, oldest)

### 3. **Related Content**
- Single news article endpoint returns related articles from the same category
- Automatically limited to 4 related articles

### 4. **Computed Attributes**
- `excerpt` - Auto-generated 150-character excerpt from content
- `reading_time` - Estimated reading time in minutes (based on 200 words/minute)
- `thumbnail_url` - Full URL to thumbnail image
- `images_url` - Array of full URLs to additional images

### 5. **Response Consistency**
All endpoints follow a consistent response format:
```json
{
  "success": true,
  "data": { ... }
}
```

### 6. **Pagination**
List endpoints support pagination with customizable `per_page` parameter.

---

## Database Schema

### News Table (`news`)
- `id` - Primary key
- `title` - Article title
- `slug` - URL-friendly slug (auto-generated)
- `description` - Short description/summary
- `content` - Full article content (HTML)
- `thumbnail` - Thumbnail image path
- `images` - Additional images (JSON array)
- `category_id` - Foreign key to news_categories
- `city_id` - Foreign key to cities (nullable)
- `is_active` - Published status
- `published_at` - Publication date/time
- `views_count` - View counter
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

### News Categories Table (`news_categories`)
- `id` - Primary key
- `name` - Category name
- `slug` - URL-friendly slug (auto-generated)
- `is_active` - Active status
- `order` - Display order
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

---

## Swagger/OpenAPI Documentation

All endpoints are fully documented with Swagger annotations including:
- Complete parameter descriptions
- Response schemas
- Example values
- Error responses

**Access Swagger UI:** `http://your-domain/api/documentation`

---

## Usage Examples

### Flutter/Mobile App
```dart
// Get latest news
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/news/latest?limit=5')
);

// Get single article
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/news/how-to-start-business-in-nyc')
);

// Search news
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/news?search=technology&city_id=1')
);
```

### JavaScript/Web App
```javascript
// Get news by category
const response = await fetch('/api/v1/news/category/technology?page=1');
const data = await response.json();

// Get featured news
const response = await fetch('/api/v1/news/featured?limit=5&city_id=1');
const data = await response.json();
```

---

## Error Handling

### 404 Not Found
Returned when:
- News article slug doesn't exist
- Category slug doesn't exist
- City ID doesn't exist

```json
{
  "message": "No query results for model [App\\Models\\News] {slug}"
}
```

### 422 Validation Error
Returned when invalid parameters are provided.

---

## Testing

### Test Endpoints
```bash
# Get all news
curl http://localhost/api/v1/news

# Get single article
curl http://localhost/api/v1/news/your-article-slug

# Get categories
curl http://localhost/api/v1/news/categories/list

# Get latest news
curl http://localhost/api/v1/news/latest?limit=10

# Get featured news
curl http://localhost/api/v1/news/featured

# Search news
curl "http://localhost/api/v1/news?search=technology&city_id=1"

# Get news by category
curl http://localhost/api/v1/news/category/technology

# Get news by city
curl http://localhost/api/v1/cities/1/news
```

---

## Admin Panel Integration

The News API works seamlessly with the existing admin panel:
- **Create/Edit News:** `http://your-domain/admin/news`
- **Manage Categories:** `http://your-domain/admin/news-categories`
- News created through admin panel are immediately available via API
- Push notifications can be sent when publishing news

---

## Performance Considerations

1. **Eager Loading**: All endpoints use `with(['category', 'city'])` to prevent N+1 queries
2. **Caching**: Consider implementing cache for:
   - Categories list (rarely changes)
   - Featured news (cache for 1 hour)
   - Latest news (cache for 15 minutes)
3. **Pagination**: Default limit of 15 items per page to prevent large responses
4. **Indexing**: Database indexes on:
   - `slug` column (for fast lookups)
   - `is_active`, `published_at` (for filtering)
   - `category_id`, `city_id` (for foreign keys)
   - `views_count` (for featured sorting)

---

## Future Enhancements

Consider adding:
1. **Comments API** - Allow users to comment on news articles
2. **Bookmarks** - Save articles for later reading
3. **Share Tracking** - Track social media shares
4. **Reading Progress** - Save user's reading position
5. **Recommendations** - AI-powered article recommendations
6. **Tags** - Additional classification beyond categories
7. **Author Profiles** - Author information and bio
8. **RSS Feed** - RSS/Atom feed generation

---

## Related Documentation
- Admin News Management: `routes/admin.php` (news routes)
- Web News Pages: `routes/web.php` (public news routes)
- News Model: `app/Models/News.php`
- Admin Controller: `app/Http/Controllers/Admin/AdminNewsController.php`

---

## Support
For issues or questions about the News API implementation, refer to:
- API Documentation: `/api/documentation`
- Admin Dashboard: `/admin/news`
- This documentation file

---

**Implementation Status:** ✅ Complete and Ready for Production
