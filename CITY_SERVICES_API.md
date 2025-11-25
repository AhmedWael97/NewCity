# City Services API - Mobile App Integration

## Overview
New endpoint to fetch and sort all user services within a specific city. Perfect for mobile app integration with multiple sorting and filtering options.

## Endpoint

### Get City Services (Sorted)
**GET** `/api/v1/cities/{city}/services`

Get all user services in a specific city with advanced sorting and filtering capabilities.

**No authentication required** (public endpoint)

---

## URL Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `city` | string/integer | Yes | City ID or slug |

---

## Query Parameters

### Sorting Options

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `sort` | string | `latest` | Sort order for services |
| `per_page` | integer | 15 | Items per page (max 100) |
| `page` | integer | 1 | Page number |

#### Available Sort Values:

| Value | Description | Order |
|-------|-------------|-------|
| `latest` | Newest services first | Created date DESC |
| `oldest` | Oldest services first | Created date ASC |
| `rating_high` | Highest rated first | Rating DESC |
| `rating_low` | Lowest rated first | Rating ASC |
| `price_low` | Cheapest first | Base price ASC |
| `price_high` | Most expensive first | Base price DESC |
| `featured` | Featured services first | Featured + Rating |
| `name_asc` | Alphabetical A-Z | Title ASC |
| `name_desc` | Alphabetical Z-A | Title DESC |

### Filtering Options

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `category_id` | integer | Filter by service category | `3` |
| `pricing_type` | string | Filter by pricing type | `hourly` |
| `search` | string | Search in title/description | `plumber` |

#### Pricing Types:
- `fixed` - Fixed price
- `hourly` - Hourly rate
- `per_km` - Per kilometer
- `negotiable` - Price negotiable

---

## Response Structure

### Success Response (200 OK)

```json
{
  "success": true,
  "data": {
    "services": [
      {
        "id": 1,
        "title": "Professional Plumbing Services",
        "description": "Expert plumbing repairs and installations",
        "slug": "professional-plumbing-services",
        "pricing_type": "hourly",
        "base_price": 50.00,
        "hourly_rate": 50.00,
        "minimum_charge": 100.00,
        "rating": 4.8,
        "review_count": 45,
        "is_verified": true,
        "is_featured": true,
        "featured_until": "2024-12-31T23:59:59.000000Z",
        "images": [
          "services/plumbing-1.jpg",
          "services/plumbing-2.jpg"
        ],
        "contact_phone": "+201234567890",
        "contact_whatsapp": "+201234567890",
        "experience_years": 10,
        "user": {
          "id": 5,
          "name": "Ahmed Mohamed",
          "avatar": "avatars/ahmed.jpg"
        },
        "serviceCategory": {
          "id": 3,
          "name": "Home Services",
          "slug": "home-services",
          "icon": "fas fa-home"
        },
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-11-20T14:20:00.000000Z"
      },
      {
        "id": 2,
        "title": "Taxi Service - 24/7",
        "description": "Reliable taxi service available around the clock",
        "slug": "taxi-service-24-7",
        "pricing_type": "per_km",
        "base_price": 10.00,
        "distance_rate": 2.50,
        "minimum_charge": 15.00,
        "rating": 4.5,
        "review_count": 120,
        "is_verified": true,
        "is_featured": false,
        "vehicle_info": {
          "type": "Sedan",
          "model": "Toyota Corolla 2020",
          "seats": 4
        },
        "user": {
          "id": 12,
          "name": "Mohamed Ali",
          "avatar": null
        },
        "serviceCategory": {
          "id": 7,
          "name": "Transportation",
          "slug": "transportation",
          "icon": "fas fa-car"
        }
      }
    ],
    "meta": {
      "total": 45,
      "per_page": 15,
      "current_page": 1,
      "last_page": 3
    }
  }
}
```

### Error Response (404 Not Found)

```json
{
  "success": false,
  "message": "City not found"
}
```

---

## Usage Examples

### Basic Request - Latest Services

```bash
GET /api/v1/cities/1/services
```

```javascript
// JavaScript/Axios
const response = await axios.get('/api/v1/cities/1/services');
console.log(response.data.data.services);
```

```dart
// Flutter/Dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/cities/1/services')
);

if (response.statusCode == 200) {
  final data = jsonDecode(response.body);
  final services = data['data']['services'];
}
```

---

### Sort by Highest Rating

```bash
GET /api/v1/cities/1/services?sort=rating_high
```

```javascript
const response = await axios.get('/api/v1/cities/1/services', {
  params: { sort: 'rating_high' }
});
```

```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/cities/1/services?sort=rating_high')
);
```

---

### Filter by Category and Sort

```bash
GET /api/v1/cities/cairo/services?category_id=3&sort=featured
```

```javascript
const response = await axios.get('/api/v1/cities/cairo/services', {
  params: {
    category_id: 3,
    sort: 'featured'
  }
});
```

```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/cities/cairo/services?category_id=3&sort=featured')
);
```

---

### Search with Filters

```bash
GET /api/v1/cities/1/services?search=plumber&pricing_type=hourly&sort=price_low
```

```javascript
const response = await axios.get('/api/v1/cities/1/services', {
  params: {
    search: 'plumber',
    pricing_type: 'hourly',
    sort: 'price_low'
  }
});
```

```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/cities/1/services?search=plumber&pricing_type=hourly&sort=price_low')
);
```

---

### Pagination

```bash
GET /api/v1/cities/1/services?page=2&per_page=20
```

```javascript
const response = await axios.get('/api/v1/cities/1/services', {
  params: {
    page: 2,
    per_page: 20
  }
});
```

```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/cities/1/services?page=2&per_page=20')
);
```

---

## Complete Flutter Example

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class CityServicesService {
  final String baseUrl;

  CityServicesService(this.baseUrl);

  Future<Map<String, dynamic>> getCityServices({
    required dynamic cityId, // Can be int or string (slug)
    String sort = 'latest',
    int? categoryId,
    String? pricingType,
    String? search,
    int page = 1,
    int perPage = 15,
  }) async {
    // Build query parameters
    final queryParams = {
      'sort': sort,
      'page': page.toString(),
      'per_page': perPage.toString(),
    };

    if (categoryId != null) {
      queryParams['category_id'] = categoryId.toString();
    }
    if (pricingType != null) {
      queryParams['pricing_type'] = pricingType;
    }
    if (search != null && search.isNotEmpty) {
      queryParams['search'] = search;
    }

    final uri = Uri.parse('$baseUrl/api/v1/cities/$cityId/services')
        .replace(queryParameters: queryParams);

    final response = await http.get(uri);

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else if (response.statusCode == 404) {
      throw Exception('City not found');
    } else {
      throw Exception('Failed to load services');
    }
  }
}

// Usage Example
void main() async {
  final service = CityServicesService('https://example.com');

  try {
    // Get featured services in Cairo
    final result = await service.getCityServices(
      cityId: 'cairo',
      sort: 'featured',
      perPage: 20,
    );

    print('Total services: ${result['data']['meta']['total']}');
    
    for (var service in result['data']['services']) {
      print('${service['title']} - Rating: ${service['rating']}');
    }

    // Get plumbing services sorted by price
    final plumbingServices = await service.getCityServices(
      cityId: 1,
      categoryId: 3,
      sort: 'price_low',
    );

    // Search for taxi services
    final taxiServices = await service.getCityServices(
      cityId: 1,
      search: 'taxi',
      sort: 'rating_high',
    );

  } catch (e) {
    print('Error: $e');
  }
}
```

---

## Complete JavaScript Example

```javascript
class CityServicesAPI {
  constructor(baseURL) {
    this.baseURL = baseURL;
  }

  async getCityServices({
    cityId,
    sort = 'latest',
    categoryId = null,
    pricingType = null,
    search = null,
    page = 1,
    perPage = 15
  }) {
    const params = new URLSearchParams({
      sort,
      page: page.toString(),
      per_page: perPage.toString()
    });

    if (categoryId) params.append('category_id', categoryId);
    if (pricingType) params.append('pricing_type', pricingType);
    if (search) params.append('search', search);

    try {
      const response = await fetch(
        `${this.baseURL}/api/v1/cities/${cityId}/services?${params}`
      );

      if (!response.ok) {
        if (response.status === 404) {
          throw new Error('City not found');
        }
        throw new Error('Failed to fetch services');
      }

      return await response.json();
    } catch (error) {
      console.error('Error fetching city services:', error);
      throw error;
    }
  }
}

// Usage
const api = new CityServicesAPI('https://example.com');

// Get latest services
const latest = await api.getCityServices({ cityId: 1 });

// Get featured services
const featured = await api.getCityServices({
  cityId: 'cairo',
  sort: 'featured',
  perPage: 20
});

// Filter and search
const filtered = await api.getCityServices({
  cityId: 1,
  categoryId: 3,
  pricingType: 'hourly',
  search: 'plumber',
  sort: 'price_low'
});

console.log(`Found ${filtered.data.meta.total} services`);
```

---

## Mobile App Implementation Tips

### 1. **Caching Strategy**
```dart
// Cache city services for better performance
final prefs = await SharedPreferences.getInstance();
final cacheKey = 'city_${cityId}_services_${sort}';
final cachedData = prefs.getString(cacheKey);

if (cachedData != null && !forceRefresh) {
  return jsonDecode(cachedData);
}

final response = await getCityServices(...);
await prefs.setString(cacheKey, jsonEncode(response));
```

### 2. **Pagination Loading**
```dart
// Load more when user scrolls to bottom
ScrollController _scrollController = ScrollController();

_scrollController.addListener(() {
  if (_scrollController.position.pixels == 
      _scrollController.position.maxScrollExtent) {
    loadMoreServices();
  }
});
```

### 3. **Pull to Refresh**
```dart
RefreshIndicator(
  onRefresh: () async {
    await loadCityServices(forceRefresh: true);
  },
  child: ServicesList(services: services),
)
```

### 4. **Sort Dropdown UI**
```dart
DropdownButton<String>(
  value: currentSort,
  items: [
    DropdownMenuItem(value: 'latest', child: Text('Latest')),
    DropdownMenuItem(value: 'rating_high', child: Text('Highest Rated')),
    DropdownMenuItem(value: 'price_low', child: Text('Price: Low to High')),
    DropdownMenuItem(value: 'featured', child: Text('Featured')),
  ],
  onChanged: (value) {
    setState(() {
      currentSort = value!;
      loadCityServices();
    });
  },
)
```

---

## Testing

### Test Cases:

1. ✅ Get services for valid city ID
2. ✅ Get services for valid city slug
3. ✅ Sort by each available sort option
4. ✅ Filter by category
5. ✅ Filter by pricing type
6. ✅ Search functionality
7. ✅ Pagination works correctly
8. ✅ Invalid city returns 404
9. ✅ Combine multiple filters
10. ✅ Empty results handled properly

---

## Performance Notes

- **Only active and verified services** are returned
- Services include user and category relationships (optimized with `select`)
- Response is **not cached** (real-time data)
- Pagination recommended for large result sets
- Consider implementing caching in your mobile app

---

## Related Endpoints

| Endpoint | Description |
|----------|-------------|
| `GET /api/v1/cities` | Get all cities |
| `GET /api/v1/cities/{city}` | Get city details |
| `GET /api/v1/user-services` | Get all services (global) |
| `GET /api/v1/user-services/{id}` | Get single service |
| `GET /api/v1/service-categories` | Get service categories |
| `GET /api/v1/search` | Global search |

---

## Summary

✅ **New endpoint implemented**: `GET /api/v1/cities/{city}/services`

**Features:**
- 9 different sorting options
- Category filtering
- Pricing type filtering
- Search functionality
- Pagination support
- Only verified and active services
- Optimized database queries
- Full Swagger documentation

**Perfect for:**
- Mobile app service listings
- City-specific service directories
- Filtered and sorted service views
- Service discovery features

The endpoint is **production-ready** and optimized for mobile app usage! 🚀
