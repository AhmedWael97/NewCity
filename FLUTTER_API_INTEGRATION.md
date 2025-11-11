# Flutter API Integration Guide

## 📋 OpenAPI/Swagger Documentation

### Access Methods

#### 1. **JSON File (Recommended for AI Agents)**
**Location:** `storage/api-docs/api-docs.json`

**Direct Access:**
- File path: `E:\coupons\githubs\City\City\storage\api-docs\api-docs.json`
- Web URL: `http://your-domain.com/docs/api-docs.json`

**Generate/Update:**
```bash
php artisan l5-swagger:generate
```

---

#### 2. **Web Interface (Human-Readable)**
**URL:** `http://your-domain.com/api/documentation`

Features:
- Interactive API testing
- Try out endpoints directly
- View request/response examples
- Authentication testing

---

## 🤖 AI Agent Integration

### Using OpenAPI JSON with AI Tools

The `api-docs.json` file contains complete API specification that AI agents can parse:

1. **All Endpoints** - Every API route with methods (GET, POST, PUT, DELETE)
2. **Request Parameters** - Required/optional fields with types
3. **Response Schemas** - Expected response structures
4. **Authentication** - Token-based auth (Sanctum)
5. **Examples** - Sample requests and responses

### For Flutter Code Generation

AI agents can use the OpenAPI spec to:
- Generate Dart models
- Create API service classes
- Build repository patterns
- Generate type-safe HTTP clients

---

## 📦 OpenAPI JSON Structure

```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "City Shop Directory API",
    "version": "1.0.0"
  },
  "servers": [
    {
      "url": "/api/v1",
      "description": "API Server"
    }
  ],
  "paths": {
    "/api/v1/auth/login": { ... },
    "/api/v1/cities": { ... },
    "/api/v1/cities/{city}/featured-shops": { ... }
  },
  "components": {
    "schemas": { ... },
    "securitySchemes": {
      "sanctum": {
        "type": "http",
        "scheme": "bearer"
      }
    }
  }
}
```

---

## 🚀 Quick Start for Flutter

### 1. Download API Specification

**PowerShell:**
```powershell
# Copy to Flutter project
Copy-Item "storage\api-docs\api-docs.json" "C:\path\to\flutter\project\openapi.json"
```

**Or generate fresh:**
```powershell
php artisan l5-swagger:generate
```

---

### 2. Use with OpenAPI Generator

Install OpenAPI Generator:
```bash
dart pub global activate openapi_generator_cli
```

Generate Dart code:
```bash
openapi-generator-cli generate \
  -i openapi.json \
  -g dart \
  -o lib/api \
  --additional-properties=pubName=city_api
```

---

### 3. Manual Integration (Recommended)

Create API service based on endpoints:

```dart
// lib/services/api_service.dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String baseUrl = 'http://your-domain.com/api/v1';
  String? _token;

  // Set token after login
  void setToken(String token) {
    _token = token;
  }

  // Get headers
  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };

  // Auth endpoints
  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      headers: _headers,
      body: jsonEncode({
        'email': email,
        'password': password,
      }),
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      setToken(data['data']['token']);
      return data;
    } else {
      throw Exception('Login failed');
    }
  }

  // Cities endpoints
  Future<List<dynamic>> getCities() async {
    final response = await http.get(
      Uri.parse('$baseUrl/cities'),
      headers: _headers,
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['data'];
    } else {
      throw Exception('Failed to load cities');
    }
  }

  // City landing page - Featured Shops
  Future<List<dynamic>> getFeaturedShops(int cityId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/cities/$cityId/featured-shops'),
      headers: _headers,
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['data'];
    } else {
      throw Exception('Failed to load featured shops');
    }
  }

  // City landing page - Banners
  Future<List<dynamic>> getCityBanners(int cityId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/cities/$cityId/banners'),
      headers: _headers,
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['data']['banners'];
    } else {
      throw Exception('Failed to load banners');
    }
  }

  // City landing page - Statistics
  Future<Map<String, dynamic>> getCityStatistics(int cityId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/cities/$cityId/statistics'),
      headers: _headers,
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['data'];
    } else {
      throw Exception('Failed to load statistics');
    }
  }
}
```

---

## 📝 Complete Endpoint List

### Authentication (Public)
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `POST /api/v1/auth/forgot-password` - Request password reset
- `POST /api/v1/auth/reset-password` - Reset password

### Cities (Public)
- `GET /api/v1/cities` - List all cities
- `GET /api/v1/cities/{id}` - Get city details
- `GET /api/v1/cities/{id}/featured-shops` - Get featured shops for city
- `GET /api/v1/cities/{id}/latest-shops` - Get latest shops for city
- `GET /api/v1/cities/{id}/statistics` - Get city statistics
- `GET /api/v1/cities/{id}/banners` - Get city promotional banners

### Shops (Public)
- `GET /api/v1/shops` - List shops (with filters)
- `GET /api/v1/shops/{id}` - Get shop details
- `GET /api/v1/shops/{id}/ratings` - Get shop ratings
- `GET /api/v1/shops/featured` - Get featured shops
- `GET /api/v1/shops/search/nearby` - Search nearby shops

### Categories (Public)
- `GET /api/v1/categories` - List categories

### Services (Public)
- `GET /api/v1/services` - List services
- `GET /api/v1/services/{id}` - Get service details

### User (Protected - Requires Token)
- `GET /api/v1/user/profile` - Get user profile
- `PUT /api/v1/user/profile` - Update profile
- `POST /api/v1/user/logout` - Logout
- `DELETE /api/v1/user/account` - Delete account

### User Services (Protected)
- `GET /api/v1/user/services` - Get user's services
- `POST /api/v1/user/services` - Create service
- `PUT /api/v1/user/services/{id}` - Update service
- `DELETE /api/v1/user/services/{id}` - Delete service

### Device Registration (Public)
- `POST /api/v1/device/register` - Register device for notifications
- `POST /api/v1/device/unregister` - Unregister device

### Mobile App Settings (Public)
- `GET /api/v1/app/settings` - Get app settings (maintenance mode, etc.)

---

## 🔐 Authentication Flow

### 1. Login/Register
```dart
final apiService = ApiService();
final response = await apiService.login('user@example.com', 'password');
// Token is automatically saved in apiService
```

### 2. Make Authenticated Requests
```dart
// Token is included automatically
final profile = await apiService.getUserProfile();
```

### 3. Logout
```dart
await apiService.logout();
```

---

## 🎨 City Landing Page Implementation

### Full Landing Page Widget Example

```dart
class CityLandingPage extends StatefulWidget {
  final int cityId;
  
  const CityLandingPage({required this.cityId});

  @override
  _CityLandingPageState createState() => _CityLandingPageState();
}

class _CityLandingPageState extends State<CityLandingPage> {
  final ApiService _api = ApiService();
  
  Map<String, dynamic>? city;
  List<dynamic> banners = [];
  List<dynamic> featuredShops = [];
  Map<String, dynamic>? statistics;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadCityData();
  }

  Future<void> _loadCityData() async {
    setState(() => isLoading = true);
    
    try {
      // Load all data in parallel
      final results = await Future.wait([
        _api.getCityDetails(widget.cityId),
        _api.getCityBanners(widget.cityId),
        _api.getFeaturedShops(widget.cityId),
        _api.getCityStatistics(widget.cityId),
      ]);
      
      setState(() {
        city = results[0];
        banners = results[1];
        featuredShops = results[2];
        statistics = results[3];
        isLoading = false;
      });
    } catch (e) {
      print('Error loading city data: $e');
      setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    // Apply theme from city configuration
    final themeConfig = city?['theme_config'];
    final primaryColor = themeConfig?['primary_color'] ?? '#007bff';

    return Scaffold(
      appBar: AppBar(
        title: Text(city?['name'] ?? 'City'),
        backgroundColor: Color(int.parse(primaryColor.replaceFirst('#', '0xFF'))),
      ),
      body: RefreshIndicator(
        onRefresh: _loadCityData,
        child: SingleChildScrollView(
          child: Column(
            children: [
              // Banners Carousel
              if (banners.isNotEmpty) _buildBannersCarousel(),
              
              // Statistics Cards
              if (statistics != null) _buildStatistics(),
              
              // Featured Shops
              if (featuredShops.isNotEmpty) _buildFeaturedShops(),
              
              // Latest Shops
              _buildLatestShops(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBannersCarousel() {
    return CarouselSlider(
      items: banners.map((banner) {
        return GestureDetector(
          onTap: () => _handleBannerTap(banner),
          child: Image.network(
            banner['image_url'],
            fit: BoxFit.cover,
            width: double.infinity,
          ),
        );
      }).toList(),
      options: CarouselOptions(
        height: 200,
        autoPlay: true,
        enlargeCenterPage: true,
      ),
    );
  }

  Widget _buildStatistics() {
    return Padding(
      padding: EdgeInsets.all(16),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildStatCard('Shops', statistics!['total_shops']),
          _buildStatCard('Services', statistics!['total_services']),
          _buildStatCard('Users', statistics!['total_users']),
        ],
      ),
    );
  }

  Widget _buildStatCard(String label, int value) {
    return Card(
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          children: [
            Text('$value', style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
            Text(label),
          ],
        ),
      ),
    );
  }

  Widget _buildFeaturedShops() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: EdgeInsets.all(16),
          child: Text('Featured Shops', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
        ),
        ListView.builder(
          shrinkWrap: true,
          physics: NeverScrollableScrollPhysics(),
          itemCount: featuredShops.length,
          itemBuilder: (context, index) {
            final shop = featuredShops[index];
            return ListTile(
              leading: shop['logo_url'] != null 
                ? Image.network(shop['logo_url'], width: 50, height: 50)
                : Icon(Icons.store),
              title: Text(shop['name']),
              subtitle: Text(shop['description'] ?? ''),
              trailing: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.star, color: Colors.amber, size: 16),
                  Text('${shop['rating'] ?? 0}'),
                ],
              ),
              onTap: () => _openShopDetails(shop['id']),
            );
          },
        ),
      ],
    );
  }

  void _handleBannerTap(Map<String, dynamic> banner) {
    // Handle banner click based on link_type
    final linkType = banner['link_type'];
    final linkValue = banner['link_value'];
    
    if (linkType == 'shop') {
      // Navigate to shop details
    } else if (linkType == 'category') {
      // Navigate to category
    } else if (linkType == 'url') {
      // Open external URL
    }
  }
}
```

---

## 📊 API Response Examples

### City Landing Page Data

**Featured Shops Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Premium Shop",
      "description": "Best shop in town",
      "logo_url": "https://example.com/logo.jpg",
      "rating": 4.5,
      "featured_priority": 10,
      "featured_expires_at": "2025-12-31"
    }
  ]
}
```

**City Banners Response:**
```json
{
  "success": true,
  "data": {
    "city": {
      "id": 1,
      "name": "Cairo"
    },
    "banners": [
      {
        "id": 1,
        "title": "Special Offer",
        "description": "50% off",
        "image_url": "https://example.com/banner.jpg",
        "link_type": "shop",
        "link_value": "5",
        "priority": 10
      }
    ]
  }
}
```

**City Statistics Response:**
```json
{
  "success": true,
  "data": {
    "total_shops": 150,
    "active_shops": 120,
    "total_services": 450,
    "total_users": 1200,
    "total_reviews": 890
  }
}
```

---

## 🛠️ Development Tools

### Testing API with PowerShell

```powershell
# Get cities
$response = Invoke-RestMethod -Uri "http://localhost/api/v1/cities" -Method Get
$response.data

# Get featured shops for city 1
$response = Invoke-RestMethod -Uri "http://localhost/api/v1/cities/1/featured-shops" -Method Get
$response.data

# Login and get token
$loginResponse = Invoke-RestMethod -Uri "http://localhost/api/v1/auth/login" `
    -Method Post `
    -ContentType "application/json" `
    -Body '{"email":"test@example.com","password":"password123"}'
    
$token = $loginResponse.data.token

# Use token for protected endpoint
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}
$profile = Invoke-RestMethod -Uri "http://localhost/api/v1/user/profile" -Headers $headers
```

---

## 🔄 Auto-Update OpenAPI Spec

Add to your development workflow:

**After any controller changes:**
```bash
php artisan l5-swagger:generate
```

**Or create a git hook** (`/.git/hooks/pre-commit`):
```bash
#!/bin/sh
php artisan l5-swagger:generate
git add storage/api-docs/api-docs.json
```

---

## 📱 Flutter Packages Recommendations

```yaml
# pubspec.yaml
dependencies:
  http: ^1.1.0           # HTTP client
  dio: ^5.4.0            # Alternative HTTP client with interceptors
  json_annotation: ^4.8.1 # JSON serialization
  shared_preferences: ^2.2.2 # Store token locally
  flutter_secure_storage: ^9.0.0 # Secure token storage
  
dev_dependencies:
  build_runner: ^2.4.7   # Code generation
  json_serializable: ^6.7.1 # JSON code generation
```

---

## 🎯 AI Agent Prompt Example

**For AI Code Generation:**

```
I have an OpenAPI 3.0 specification file at storage/api-docs/api-docs.json for a City Shop Directory API.

Please generate:
1. Dart models for all API responses
2. An API service class with methods for all endpoints
3. A repository pattern implementation
4. Error handling with custom exceptions
5. Token management with secure storage

The API uses:
- Laravel Sanctum Bearer token authentication
- Base URL: /api/v1
- Standard Laravel JSON responses with 'success', 'message', 'data' structure

Focus on:
- Type safety
- Null safety
- Proper error handling
- Clean architecture patterns
- Cached data for offline support
```

---

## 📞 Support

For API issues or questions:
- Check Swagger UI: `http://your-domain.com/api/documentation`
- Review OpenAPI JSON: `storage/api-docs/api-docs.json`
- Test with Postman using the OpenAPI import feature

---

**Last Updated:** November 10, 2025  
**API Version:** 1.0.0
