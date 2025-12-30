# User Profile API - Testing Guide

## Authentication Required
All endpoints require Bearer token authentication.

## Endpoints

### 1. Get User Profile
```bash
GET /api/v1/user/profile
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "avatar": "http://localhost:8000/storage/avatars/xyz.jpg",
      "user_type": "customer",
      "is_active": true,
      "is_verified": true,
      "address": "123 Main St",
      "date_of_birth": "1990-01-15",
      "city": {
        "id": 1,
        "name": "New York"
      },
      "preferred_city": {
        "id": 2,
        "name": "Los Angeles"
      },
      "role": {
        "id": 1,
        "name": "Customer",
        "slug": "customer"
      },
      "created_at": "2025-01-01 12:00:00",
      "email_verified_at": "2025-01-01 12:05:00"
    }
  }
}
```

### 2. Update User Profile
```bash
PUT /api/v1/user/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Updated",
  "phone": "+1234567891",
  "address": "456 Oak Avenue",
  "date_of_birth": "1990-01-15",
  "city_id": 3,
  "preferred_city_id": 4
}
```

**Response:** Same structure as Get Profile

### 3. Upload Avatar
```bash
POST /api/v1/user/avatar
Authorization: Bearer {token}
Content-Type: multipart/form-data

avatar: [file]
```

**Response:**
```json
{
  "success": true,
  "message": "Avatar uploaded successfully",
  "data": {
    "avatar_url": "http://localhost:8000/storage/avatars/xyz.jpg"
  }
}
```

### 4. Delete Avatar
```bash
DELETE /api/v1/user/avatar
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Avatar deleted successfully"
}
```

### 5. Change Password
```bash
PUT /api/v1/user/password
Authorization: Bearer {token}
Content-Type: application/json

{
  "current_password": "OldPassword123",
  "new_password": "NewPassword123",
  "new_password_confirmation": "NewPassword123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

**Error Response (Wrong Current Password):**
```json
{
  "success": false,
  "message": "Current password is incorrect",
  "errors": {
    "current_password": [
      "The current password is incorrect"
    ]
  }
}
```

### 6. Get User Statistics
```bash
GET /api/v1/user/statistics
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Statistics retrieved successfully",
  "data": {
    "favorites_count": 12,
    "reviews_count": 8,
    "shops_count": 2,
    "services_count": 5
  }
}
```

### 7. Delete Account
```bash
DELETE /api/v1/user/account
Authorization: Bearer {token}
Content-Type: application/json

{
  "password": "CurrentPassword123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Account deleted successfully"
}
```

## PowerShell Testing Examples

### Get Profile
```powershell
$token = "your_bearer_token_here"
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/v1/user/profile" -Headers $headers -Method Get
```

### Update Profile
```powershell
$token = "your_bearer_token_here"
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}
$body = @{
    name = "John Updated"
    phone = "+1234567891"
    address = "456 Oak Avenue"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/v1/user/profile" -Headers $headers -Method Put -Body $body
```

### Upload Avatar
```powershell
$token = "your_bearer_token_here"
$headers = @{
    "Authorization" = "Bearer $token"
}
$filePath = "C:\path\to\avatar.jpg"
$form = @{
    avatar = Get-Item -Path $filePath
}

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/v1/user/avatar" -Headers $headers -Method Post -Form $form
```

### Change Password
```powershell
$token = "your_bearer_token_here"
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}
$body = @{
    current_password = "OldPassword123"
    new_password = "NewPassword123"
    new_password_confirmation = "NewPassword123"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/v1/user/password" -Headers $headers -Method Put -Body $body
```

### Get Statistics
```powershell
$token = "your_bearer_token_here"
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/v1/user/statistics" -Headers $headers -Method Get
```

### Delete Account
```powershell
$token = "your_bearer_token_here"
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}
$body = @{
    password = "CurrentPassword123"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/v1/user/account" -Headers $headers -Method Delete -Body $body
```

## Validation Rules

### Update Profile
- `name`: Optional, string, max 255 characters
- `phone`: Optional, nullable, string, max 20 characters
- `address`: Optional, nullable, string, max 500 characters
- `date_of_birth`: Optional, nullable, date, must be before today
- `city_id`: Optional, nullable, must exist in cities table
- `preferred_city_id`: Optional, nullable, must exist in cities table

### Upload Avatar
- `avatar`: Required, image file (jpeg, png, jpg, gif), max 2MB

### Change Password
- `current_password`: Required, string
- `new_password`: Required, confirmed, minimum 8 characters, must contain mixed case and numbers
- `new_password_confirmation`: Required, must match new_password

### Delete Account
- `password`: Required, string, must match user's current password

## Features

1. **Profile Management**
   - View complete profile with all relationships
   - Update personal information
   - Separate avatar management endpoints

2. **Security**
   - Password verification for sensitive operations
   - Token revocation on password change (except current token)
   - Account deletion requires password confirmation
   - Avatar automatically deleted on account deletion

3. **Statistics**
   - Track user activity across the platform
   - Counts for favorites, reviews, shops, and services

4. **Data Protection**
   - Old avatar automatically deleted when uploading new one
   - All tokens revoked on account deletion
   - Soft delete support for user recovery

## Admin Management

Admin users can manage all user profiles through:
- `AdminUserController` (already exists)
- Admin dashboard at `/admin/users`
- Full CRUD operations with additional controls

## Next Steps

To test these endpoints:
1. First login to get a bearer token: `POST /api/v1/auth/login`
2. Use the token in Authorization header for all profile endpoints
3. Check Swagger documentation at: `http://127.0.0.1:8000/api/documentation`
