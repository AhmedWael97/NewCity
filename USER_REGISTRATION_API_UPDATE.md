# User Registration API - Updated

## Overview
The registration API has been updated to match the website registration process. Users can register as either **regular users** or **shop owners**.

## Changes Made

### ✅ Updated Fields (matching website registration)

#### **Required Fields:**
1. `name` (string, max 255) - Full name
2. `email` (string, email, unique) - Email address
3. `password` (string, min 8) - Password
4. `password_confirmation` (string) - Password confirmation
5. `phone` (string, max 20) - **NEW: Now required**
6. `user_type` (enum: regular|shop_owner) - **NEW: User type selection**
7. `city_id` (integer, exists) - **NEW: User's city**

#### **Optional Fields:**
8. `address` (string, max 500) - Physical address
9. `date_of_birth` (date, before today) - Date of birth

### 🔄 Key Changes from Previous Version

| Field | Before | After |
|-------|--------|-------|
| `phone` | Optional | **Required** |
| `user_type` | N/A | **Required** (regular or shop_owner) |
| `city_id` | N/A | **Required** |
| `address` | N/A | Optional |
| `date_of_birth` | N/A | Optional |
| `user_role_id` | Used | **Removed** (replaced with user_type) |

---

## API Endpoint

### Register New User
**POST** `/api/v1/auth/register`

**No authentication required** (public endpoint)

### Request Body

```json
{
  "name": "Ahmed Mohamed",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+201234567890",
  "user_type": "shop_owner",
  "city_id": 1,
  "address": "123 Main Street, Cairo",
  "date_of_birth": "1990-01-15"
}
```

### User Types

#### 1. Regular User (`regular`)
- Can browse shops and services
- Can search and filter
- Can save favorites
- Can rate and review shops
- **Auto-verified** upon registration
- **Cannot create shops**

```json
{
  "user_type": "regular"
}
```

#### 2. Shop Owner (`shop_owner`)
- All regular user features
- **Can create and manage shops**
- Access to shop dashboard
- Can manage products/services
- Access to analytics
- **Requires manual verification** for shops
- User account is created immediately

```json
{
  "user_type": "shop_owner"
}
```

---

## Response Examples

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 123,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com",
      "phone": "+201234567890",
      "user_type": "shop_owner",
      "city_id": 1,
      "address": "123 Main Street, Cairo",
      "date_of_birth": "1990-01-15",
      "is_verified": false,
      "is_active": true,
      "created_at": "2024-11-26T12:00:00.000000Z",
      "updated_at": "2024-11-26T12:00:00.000000Z",
      "city": {
        "id": 1,
        "name": "Cairo",
        "slug": "cairo"
      }
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789",
    "token_type": "Bearer"
  }
}
```

### Validation Error Response (422 Unprocessable Entity)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "The email has already been taken."
    ],
    "password": [
      "The password must be at least 8 characters.",
      "The password confirmation does not match."
    ],
    "phone": [
      "The phone field is required."
    ],
    "user_type": [
      "The selected user type is invalid."
    ],
    "city_id": [
      "The selected city id is invalid."
    ]
  }
}
```

---

## Validation Rules

| Field | Rules | Description |
|-------|-------|-------------|
| `name` | required, string, max:255 | Full name of the user |
| `email` | required, email, max:255, unique:users | Must be a valid, unique email |
| `password` | required, string, min:8, confirmed | Minimum 8 characters, must match confirmation |
| `password_confirmation` | required, string | Must match password |
| `phone` | required, string, max:20 | Contact phone number |
| `user_type` | required, in:regular,shop_owner | Must be either 'regular' or 'shop_owner' |
| `city_id` | required, exists:cities,id | Must be a valid city ID |
| `address` | nullable, string, max:500 | Optional physical address |
| `date_of_birth` | nullable, date, before:today | Optional, must be in the past |

---

## Important Notes

### 🔐 Auto-Verification
- **Regular users** are automatically verified (`is_verified = true`)
- **Shop owners** can create accounts immediately but shops require admin approval
- All users can log in immediately after registration

### 🎫 Authentication Token
- A Sanctum authentication token is returned upon successful registration
- Store this token and use it for authenticated requests
- Format: `Authorization: Bearer {token}`

### 🏪 Shop Owner Flow
1. Register as shop owner (`user_type: shop_owner`)
2. Receive authentication token
3. Create shop via `/api/v1/my-shops` endpoint (see SHOP_API_DOCUMENTATION.md)
4. Shop goes to pending status
5. Admin reviews and approves shop
6. Shop becomes active

### 👤 Regular User Flow
1. Register as regular user (`user_type: regular`)
2. Receive authentication token
3. Browse shops, save favorites, leave reviews
4. Cannot create shops

---

## Usage Examples

### JavaScript/Axios
```javascript
const registerUser = async (userData) => {
  try {
    const response = await axios.post('/api/v1/auth/register', {
      name: 'Ahmed Mohamed',
      email: 'ahmed@example.com',
      password: 'password123',
      password_confirmation: 'password123',
      phone: '+201234567890',
      user_type: 'shop_owner',
      city_id: 1,
      address: '123 Main Street',
      date_of_birth: '1990-01-15'
    });

    if (response.data.success) {
      // Store the token
      localStorage.setItem('token', response.data.data.token);
      console.log('User registered:', response.data.data.user);
      
      // Redirect based on user type
      if (response.data.data.user.user_type === 'shop_owner') {
        window.location.href = '/shop-owner/dashboard';
      } else {
        window.location.href = '/';
      }
    }
  } catch (error) {
    if (error.response?.status === 422) {
      console.log('Validation errors:', error.response.data.errors);
    }
  }
};
```

### Flutter/Dart
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<Map<String, dynamic>> registerUser({
  required String name,
  required String email,
  required String password,
  required String passwordConfirmation,
  required String phone,
  required String userType,
  required int cityId,
  String? address,
  String? dateOfBirth,
}) async {
  final response = await http.post(
    Uri.parse('$baseUrl/api/v1/auth/register'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'name': name,
      'email': email,
      'password': password,
      'password_confirmation': passwordConfirmation,
      'phone': phone,
      'user_type': userType,
      'city_id': cityId,
      'address': address,
      'date_of_birth': dateOfBirth,
    }),
  );

  if (response.statusCode == 201) {
    final data = jsonDecode(response.body);
    // Store token
    await storage.write(key: 'token', value: data['data']['token']);
    return data;
  } else if (response.statusCode == 422) {
    final errors = jsonDecode(response.body)['errors'];
    throw ValidationException(errors);
  } else {
    throw Exception('Registration failed');
  }
}

// Usage
try {
  final result = await registerUser(
    name: 'Ahmed Mohamed',
    email: 'ahmed@example.com',
    password: 'password123',
    passwordConfirmation: 'password123',
    phone: '+201234567890',
    userType: 'shop_owner',
    cityId: 1,
    address: '123 Main Street',
    dateOfBirth: '1990-01-15',
  );
  
  print('Registration successful: ${result['data']['user']['name']}');
  
  // Navigate based on user type
  if (result['data']['user']['user_type'] == 'shop_owner') {
    Navigator.pushReplacementNamed(context, '/shop-owner-dashboard');
  } else {
    Navigator.pushReplacementNamed(context, '/home');
  }
} catch (e) {
  print('Registration error: $e');
}
```

### cURL
```bash
curl -X POST https://example.com/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ahmed Mohamed",
    "email": "ahmed@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+201234567890",
    "user_type": "shop_owner",
    "city_id": 1,
    "address": "123 Main Street",
    "date_of_birth": "1990-01-15"
  }'
```

---

## Integration with Shop Creation

After registering as a shop owner, users can immediately create shops:

```javascript
// 1. Register as shop owner
const registerResponse = await axios.post('/api/v1/auth/register', {
  name: 'Ahmed Mohamed',
  email: 'ahmed@example.com',
  password: 'password123',
  password_confirmation: 'password123',
  phone: '+201234567890',
  user_type: 'shop_owner', // Important!
  city_id: 1
});

const token = registerResponse.data.data.token;

// 2. Create shop (requires shop_owner user type)
const shopResponse = await axios.post('/api/v1/my-shops', {
  name: 'My Coffee Shop',
  description: 'Best coffee in town',
  city_id: 1,
  category_id: 3,
  address: '456 Coffee Street',
  latitude: 30.0444,
  longitude: 31.2357,
  phone: '+201234567890',
  email: 'shop@example.com'
}, {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

---

## Comparison: Web vs API Registration

| Feature | Web Registration | API Registration |
|---------|-----------------|------------------|
| Fields | 3-step wizard (user type, info, password) | Single request with all fields |
| User Types | ✓ regular, shop_owner | ✓ regular, shop_owner |
| Phone | ✓ Required | ✓ Required |
| City | ✓ Required | ✓ Required |
| Address | ✓ Optional | ✓ Optional |
| Date of Birth | ✓ Optional | ✓ Optional |
| Auto-login | ✓ Yes (session) | ✓ Yes (token) |
| Validation | ✓ Same rules | ✓ Same rules |

Both registration methods now use identical validation rules and create users with the same structure.

---

## Next Steps After Registration

### For Regular Users:
1. ✅ Account created and verified
2. Browse shops
3. Search and filter
4. Save favorites
5. Rate and review

### For Shop Owners:
1. ✅ Account created
2. Create shop via `/api/v1/my-shops`
3. Wait for admin approval
4. Once approved:
   - Manage shop details
   - Add products/services
   - View analytics
   - Respond to reviews

---

## Testing

### Test Cases Needed:
1. ✅ Register regular user successfully
2. ✅ Register shop owner successfully
3. ✅ Validation errors for missing fields
4. ✅ Validation error for duplicate email
5. ✅ Validation error for invalid user_type
6. ✅ Validation error for invalid city_id
7. ✅ Password confirmation mismatch
8. ✅ Shop owner can create shops after registration
9. ✅ Regular user cannot create shops
10. ✅ Token is valid for authentication

### Sample Test (PHPUnit/Pest):
```php
test('user can register as shop owner', function () {
    $city = City::factory()->create();
    
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Ahmed Mohamed',
        'email' => 'ahmed@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '+201234567890',
        'user_type' => 'shop_owner',
        'city_id' => $city->id,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'User registered successfully'
        ]);
    
    $this->assertDatabaseHas('users', [
        'email' => 'ahmed@example.com',
        'user_type' => 'shop_owner',
        'city_id' => $city->id
    ]);
});
```

---

## Migration Notes

### If you have existing mobile/web apps:

#### Breaking Changes:
- ❌ `user_role_id` field removed
- ✅ `user_type` field now required
- ✅ `phone` field now required (was optional)
- ✅ `city_id` field now required (was not present)

#### Migration Steps:
1. Update registration forms to include:
   - User type selection (regular/shop owner)
   - City selector
   - Phone number (required)
   - Optional: address, date of birth

2. Update API calls to use new field names
3. Handle the new response structure
4. Store and use the returned token
5. Test both regular and shop owner registration flows

---

## Summary

✅ **Registration API now matches website functionality**
- Same fields
- Same validation rules
- Same user types (regular, shop_owner)
- Same auto-verification logic
- Full OpenAPI/Swagger documentation
- Ready for production use

🔗 **Related Documentation:**
- Shop Creation API: `SHOP_API_DOCUMENTATION.md`
- Shop API Implementation: `SHOP_API_IMPLEMENTATION_SUMMARY.md`
