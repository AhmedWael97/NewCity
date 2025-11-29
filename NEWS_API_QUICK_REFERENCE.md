# News API - Quick Reference

## 📰 Endpoints Overview

### Public Endpoints (No Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/news` | Get paginated news list |
| GET | `/api/v1/news/{slug}` | Get single article by slug |
| GET | `/api/v1/news/latest` | Get latest articles |
| GET | `/api/v1/news/featured` | Get most viewed articles |
| GET | `/api/v1/news/categories/list` | Get all categories |
| GET | `/api/v1/news/category/{slug}` | Get articles by category |
| GET | `/api/v1/cities/{city_id}/news` | Get articles by city |

---

## 🚀 Quick Start Examples

### 1. Get Latest News
```bash
GET /api/v1/news/latest?limit=10
```

### 2. Get Single Article
```bash
GET /api/v1/news/how-to-start-business
```
- Automatically increments view count
- Returns related articles

### 3. Search News
```bash
GET /api/v1/news?search=technology&city_id=1&page=1
```

### 4. Filter by Category
```bash
GET /api/v1/news?category_id=2&sort=popular
```

### 5. Get Categories
```bash
GET /api/v1/news/categories/list
```

---

## 📋 Common Parameters

### List Endpoints
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15)
- `search` - Search text
- `sort` - Sort order: `latest`, `popular`, `oldest`
- `category_id` - Filter by category
- `city_id` - Filter by city

### Latest/Featured
- `limit` - Number of items (default: 10/5)
- `city_id` - Filter by city

---

## 📦 Response Format

### Success Response
```json
{
  "success": true,
  "data": { ... }
}
```

### News Article Object
```json
{
  "id": 1,
  "title": "Article Title",
  "slug": "article-title",
  "description": "Short description",
  "content": "Full content HTML",
  "thumbnail_url": "https://...",
  "views_count": 150,
  "reading_time": 5,
  "published_at": "2025-11-29T10:00:00Z",
  "category": {
    "id": 1,
    "name": "Technology",
    "slug": "technology"
  },
  "city": {
    "id": 1,
    "name": "New York"
  }
}
```

---

## 🎯 Key Features

✅ **View Tracking** - Automatic increment on article view  
✅ **Related Content** - 4 related articles per article  
✅ **Full-text Search** - Search in title, description, content  
✅ **Multiple Sorting** - Latest, popular, oldest  
✅ **Pagination** - Efficient data loading  
✅ **Eager Loading** - Optimized database queries  
✅ **Swagger Docs** - Complete API documentation  

---

## 🔧 Admin Integration

Manage news via admin panel:
- Create/Edit: `/admin/news`
- Categories: `/admin/news-categories`
- Send notifications when publishing

---

## 📱 Mobile App Integration

```dart
// Dart/Flutter Example
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/news/latest?limit=5')
);
final data = jsonDecode(response.body);
List<News> newsList = (data['data'] as List)
    .map((json) => News.fromJson(json))
    .toList();
```

---

## 🌐 Web Integration

```javascript
// JavaScript Example
async function getNews() {
  const response = await fetch('/api/v1/news?page=1&per_page=15');
  const data = await response.json();
  return data.data.news;
}
```

---

## 📄 Files Created

1. **Controller**: `app/Http/Controllers/Api/NewsController.php`
2. **Resources**: 
   - `app/Http/Resources/NewsResource.php`
   - `app/Http/Resources/NewsCategoryResource.php`
3. **Routes**: `routes/api.php` (news endpoints added)
4. **Documentation**: `NEWS_API_DOCUMENTATION.md`

---

## ✅ Implementation Status

**Status**: Complete and Ready ✅  
**Date**: November 29, 2025  
**Version**: 1.0  

All endpoints are:
- ✅ Fully implemented
- ✅ Tested and working
- ✅ Documented with Swagger
- ✅ Integrated with existing models
- ✅ Optimized for performance

---

For detailed documentation, see: **NEWS_API_DOCUMENTATION.md**
