# Shop Management API - My Shops

## Overview
The "My Shops" API allows authenticated shop owners to manage their shops. Only users with `user_type` = `shop_owner` can create and manage shops.

## Authentication
All endpoints require authentication using Laravel Sanctum. Include the bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Endpoints

### 1. List My Shops
**GET** `/api/v1/my-shops`

Get all shops owned by the authenticated user.

**Query Parameters:**
- `page` (integer, optional): Page number for pagination (default: 1)

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "My Shop",
        "slug": "my-shop-1234567890",
        "description": "Shop description",
        "address": "123 Main Street",
        "city_id": 1,
        "category_id": 2,
        "latitude": 40.7128,
        "longitude": -74.0060,
        "phone": "+1234567890",
        "email": "shop@example.com",
        "website": "https://example.com",
        "is_active": true,
        "is_verified": false,
        "is_featured": false,
        "rating": 0,
        "review_count": 0,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z",
        "city": {...},
        "category": {...}
      }
    ],
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

### 2. Create Shop
**POST** `/api/v1/my-shops`

Create a new shop. **Only shop owners can create shops.**

**Authorization Required:** User must have `user_type` = `shop_owner`

**Request Body:**
```json
{
  "name": "My Amazing Shop",
  "description": "A detailed description of the shop",
  "city_id": 1,
  "category_id": 2,
  "address": "123 Main Street",
  "latitude": 40.7128,
  "longitude": -74.0060,
  "phone": "+1234567890",
  "email": "shop@example.com",
  "website": "https://example.com",
  "images": ["image1.jpg", "image2.jpg"],
  "opening_hours": {
    "monday": "9:00 AM - 5:00 PM",
    "tuesday": "9:00 AM - 5:00 PM"
  }
}
```

**Required Fields:**
- `name` (string, max 255): Shop name
- `city_id` (integer): Valid city ID
- `category_id` (integer): Valid category ID
- `address` (string, max 500): Shop address
- `latitude` (number): Between -90 and 90
- `longitude` (number): Between -180 and 180

**Optional Fields:**
- `description` (string): Shop description
- `phone` (string, max 20): Contact phone
- `email` (string, email): Contact email
- `website` (string, url): Shop website
- `images` (array of strings): Shop images
- `opening_hours` (object): Opening hours

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Shop created successfully",
  "data": {
    "id": 1,
    "name": "My Amazing Shop",
    "slug": "my-amazing-shop-1234567890",
    "user_id": 5,
    ...
  }
}
```

**Response (403 Forbidden) - Not a shop owner:**
```json
{
  "success": false,
  "message": "Only shop owners can create shops"
}
```

**Response (422 Unprocessable Entity) - Validation error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "city_id": ["The selected city id is invalid."]
  }
}
```

---

### 3. Get Single Shop
**GET** `/api/v1/my-shops/{shop}`

Get details of a specific shop owned by the authenticated user.

**URL Parameters:**
- `shop` (integer): Shop ID

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "My Shop",
    "slug": "my-shop-1234567890",
    ...
    "city": {...},
    "category": {...}
  }
}
```

**Response (403 Forbidden) - Not your shop:**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Response (404 Not Found) - Shop doesn't exist:**
```json
{
  "message": "No query results for model [App\\Models\\Shop] {shop_id}"
}
```

---

### 4. Update Shop
**PUT** `/api/v1/my-shops/{shop}`

Update details of a shop owned by the authenticated user.

**URL Parameters:**
- `shop` (integer): Shop ID

**Request Body:**
```json
{
  "name": "Updated Shop Name",
  "description": "Updated description",
  "category_id": 2,
  "address": "Updated Address",
  "latitude": 41.0000,
  "longitude": -75.0000,
  "phone": "+9876543210",
  "email": "updated@example.com",
  "website": "https://updated.com",
  "images": ["new_image.jpg"],
  "opening_hours": {...}
}
```

**Required Fields:**
- `name` (string, max 255)
- `category_id` (integer)
- `address` (string, max 500)
- `latitude` (number)
- `longitude` (number)

**Note:** The `slug` will be automatically updated if the shop name changes.

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Shop updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Shop Name",
    "slug": "updated-shop-name-1234567890",
    ...
  }
}
```

**Response (403 Forbidden) - Not your shop:**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

### 5. Delete Shop
**DELETE** `/api/v1/my-shops/{shop}`

Delete a shop owned by the authenticated user.

**URL Parameters:**
- `shop` (integer): Shop ID

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Shop deleted successfully"
}
```

**Response (403 Forbidden) - Not your shop:**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## User Type Requirements

### How to become a shop owner:
Users must have their `user_type` field set to `shop_owner`. This can be:
1. Set during user registration
2. Updated by an administrator
3. Changed through a user profile update endpoint (if implemented)

### User Type Constants:
```php
User::TYPE_REGULAR = 'regular'
User::TYPE_SHOP_OWNER = 'shop_owner'
User::TYPE_ADMIN = 'admin'
```

### Checking user type:
```php
$user->isShopOwner(); // Returns true if user_type === 'shop_owner'
$user->isRegular();   // Returns true if user_type === 'regular'
$user->isAdmin();     // Returns true if user_type === 'admin'
```

---

## Security Features

1. **Authentication**: All endpoints require valid Sanctum token
2. **Authorization**: Only shop owners can create shops
3. **Ownership Verification**: Users can only view, update, or delete their own shops
4. **Validation**: All inputs are validated before processing
5. **Automatic Slug Generation**: Shop slugs are automatically generated and unique

---

## Example Usage

### Creating a shop (cURL):
```bash
curl -X POST https://example.com/api/v1/my-shops \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Coffee Paradise",
    "description": "Best coffee in town",
    "city_id": 1,
    "category_id": 3,
    "address": "456 Coffee Street",
    "latitude": 40.7589,
    "longitude": -73.9851,
    "phone": "+1234567890",
    "email": "info@coffeeparadise.com",
    "website": "https://coffeeparadise.com"
  }'
```

### Creating a shop (JavaScript/Axios):
```javascript
const response = await axios.post('/api/v1/my-shops', {
  name: 'Coffee Paradise',
  description: 'Best coffee in town',
  city_id: 1,
  category_id: 3,
  address: '456 Coffee Street',
  latitude: 40.7589,
  longitude: -73.9851,
  phone: '+1234567890',
  email: 'info@coffeeparadise.com',
  website: 'https://coffeeparadise.com'
}, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
});
```

### Flutter/Dart Example:
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<void> createShop(String token) async {
  final response = await http.post(
    Uri.parse('https://example.com/api/v1/my-shops'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
    },
    body: jsonEncode({
      'name': 'Coffee Paradise',
      'description': 'Best coffee in town',
      'city_id': 1,
      'category_id': 3,
      'address': '456 Coffee Street',
      'latitude': 40.7589,
      'longitude': -73.9851,
      'phone': '+1234567890',
      'email': 'info@coffeeparadise.com',
      'website': 'https://coffeeparadise.com',
    }),
  );

  if (response.statusCode == 201) {
    final data = jsonDecode(response.body);
    print('Shop created: ${data['data']['id']}');
  } else if (response.statusCode == 403) {
    print('Error: User is not a shop owner');
  } else {
    print('Error: ${response.body}');
  }
}
```

---

## Testing

Run the test suite:
```bash
php artisan test --filter=MyShopControllerTest
```

Individual test cases:
- ✓ Shop owner can create shop
- ✓ Regular user cannot create shop
- ✓ Unauthenticated user cannot create shop
- ✓ Shop creation requires mandatory fields
- ✓ Shop owner can view their shops
- ✓ Shop owner can view single shop
- ✓ Shop owner cannot view other users' shops
- ✓ Shop owner can update their shop
- ✓ Shop owner cannot update other users' shops
- ✓ Shop owner can delete their shop
- ✓ Shop owner cannot delete other users' shops
- ✓ Shop slug is automatically generated from name
- ✓ Shop slug updates when name changes

---

## Notes

1. **Slug Generation**: Slugs are automatically generated from the shop name with a timestamp appended to ensure uniqueness.
2. **City Cannot Change**: Once a shop is created, the `city_id` cannot be changed through updates (not included in update validation).
3. **Soft Deletes**: Shops are soft-deleted, meaning they can be restored if needed.
4. **Default Values**: New shops are created with `is_active = true`, `is_verified = false`, and `is_featured = false`.
5. **Images & Opening Hours**: These fields accept arrays/objects and are stored as JSON in the database.
