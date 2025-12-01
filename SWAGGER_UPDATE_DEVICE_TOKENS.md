# Swagger API Documentation Update - Device Tokens

## ✅ Update Complete

The Swagger/OpenAPI documentation has been successfully updated to include all FCM token and device management endpoints.

## 📋 What Was Added

### 1. New API Tag
- **Tag Name:** "Device Tokens"
- **Description:** "API endpoints for managing FCM device tokens and push notifications"

### 2. Device Token Schema
Added comprehensive `DeviceToken` schema with all fields:
- Core fields: `device_token`, `device_type`, `device_name`
- OS details: `os_version`, `device_model`, `device_manufacturer`, `device_id`
- App info: `app_version`, `app_build_number`
- Localization: `language`, `timezone`
- Settings: `is_active`, `notifications_enabled`
- Metadata: `ip_address`, `device_metadata`, `last_used_at`

### 3. Documented Endpoints

#### 🔓 Public Endpoint (No Auth Required)
```
POST /api/v1/guest-device-tokens
```
- **Operation ID:** `registerGuestDeviceToken`
- **Summary:** Register device token (guest)
- **Description:** Register FCM device token for non-authenticated users
- **Request Body:** Full device information
- **Responses:** 200 (Success), 422 (Validation Error), 500 (Server Error)

#### 🔒 Protected Endpoints (Auth Required)

##### Register Device Token
```
POST /api/v1/device-tokens
```
- **Operation ID:** `registerDeviceToken`
- **Summary:** Register device token (authenticated)
- **Security:** Bearer Token (Sanctum)
- **Request Body:** Full device information
- **Responses:** 200, 401 (Unauthorized), 422, 500

##### Get User's Devices
```
GET /api/v1/device-tokens
```
- **Operation ID:** `getUserDeviceTokens`
- **Summary:** Get user's device tokens
- **Security:** Bearer Token (Sanctum)
- **Response:** Array of device tokens
- **Responses:** 200, 401, 500

##### Remove Device Token
```
DELETE /api/v1/device-tokens
```
- **Operation ID:** `deleteDeviceToken`
- **Summary:** Remove device token
- **Security:** Bearer Token (Sanctum)
- **Request Body:** `device_token` to remove
- **Responses:** 200, 401, 404 (Not Found), 422, 500

## 📝 Request Examples in Swagger

### Guest Registration Request Body
```json
{
  "device_token": "fcm_token_example_here",
  "device_type": "android",
  "device_name": "Samsung Galaxy S21",
  "os_version": "Android 13",
  "device_model": "SM-G991B",
  "device_manufacturer": "Samsung",
  "device_id": "unique-device-id-123",
  "app_version": "1.0.0",
  "app_build_number": "100",
  "language": "ar",
  "timezone": "Asia/Riyadh",
  "notifications_enabled": true,
  "device_metadata": {
    "screen_width": 1080,
    "screen_height": 2400,
    "ram": "8GB",
    "storage": "128GB"
  }
}
```

### Success Response Example
```json
{
  "success": true,
  "message": "Device token registered successfully",
  "data": {
    "id": 123,
    "device_type": "android",
    "device_model": "SM-G991B",
    "is_active": true,
    "notifications_enabled": true
  }
}
```

## 🎯 Features Documented

✅ **Complete Request Schemas**
- All required and optional fields documented
- Field types, formats, and examples provided
- Enum values for `device_type` (android, ios, web)

✅ **Comprehensive Responses**
- Success responses (200)
- Validation errors (422)
- Authentication errors (401)
- Not found errors (404)
- Server errors (500)

✅ **Security Documentation**
- Sanctum bearer token authentication
- Public vs protected endpoints clearly marked

✅ **Detailed Descriptions**
- Purpose of each endpoint
- Field descriptions and constraints
- Example values for all properties

## 🌐 Access Swagger UI

### Development
```
http://localhost:8000/api/documentation
```

### Production
```
https://your-domain.com/api/documentation
```

## 🔄 Regenerate Documentation

### Using PowerShell Script
```powershell
.\regenerate-swagger.ps1
```

### Using Artisan Command
```bash
php artisan l5-swagger:generate
```

### Full Regeneration (with cache clear)
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan l5-swagger:generate
```

## 📂 Files Modified

### Controller with Annotations
```
app/Http/Controllers/Api/DeviceTokenController.php
```
- Added OpenAPI annotations to all methods
- Detailed request/response documentation
- Example values for all fields

### Schema Definitions
```
app/Http/Controllers/Api/ApiDocumentationController.php
```
- Added `DeviceToken` schema definition
- Includes all 20 model properties
- Type definitions and examples

### Generated Documentation
```
storage/api-docs/api-docs.json
```
- Auto-generated OpenAPI 3.0 specification
- Includes all endpoint definitions
- Ready for Swagger UI

## 🧪 Testing in Swagger UI

1. **Open Swagger UI** at `/api/documentation`
2. **Find "Device Tokens" section** in the API list
3. **Try Guest Endpoint:**
   - Click on `POST /api/v1/guest-device-tokens`
   - Click "Try it out"
   - Use the pre-filled example or modify as needed
   - Click "Execute"
   - View the response

4. **Try Authenticated Endpoints:**
   - Click "Authorize" button at top
   - Enter Bearer token: `Bearer YOUR_TOKEN`
   - Click "Authorize"
   - Try `POST /api/v1/device-tokens`
   - Try `GET /api/v1/device-tokens`
   - Try `DELETE /api/v1/device-tokens`

## 📊 Documentation Quality

✅ **Complete Coverage**
- All 4 endpoints documented
- All request parameters included
- All response codes covered

✅ **Developer Friendly**
- Clear descriptions
- Working examples
- Proper data types

✅ **OpenAPI 3.0 Compliant**
- Valid OpenAPI specification
- Compatible with Swagger UI
- Can be imported to Postman

✅ **Maintainable**
- Annotations in source code
- Auto-regeneration capability
- Version controlled

## 🎓 For Mobile Developers

Mobile app developers can now:

1. **Browse the API** in Swagger UI
2. **Test endpoints** directly from the browser
3. **Copy example requests** for their code
4. **See all available fields** and their types
5. **Understand response formats** with examples
6. **Download OpenAPI spec** for code generation

## 🔗 Integration Tools

The generated OpenAPI specification can be used with:

- **Swagger UI** - Interactive documentation (included)
- **Postman** - Import OpenAPI spec
- **Insomnia** - Import OpenAPI spec
- **Code Generators** - Generate client SDKs
- **API Testing Tools** - Automated testing

## ✨ Additional Features

### Auto-generation
Set in `.env`:
```env
L5_SWAGGER_GENERATE_ALWAYS=true
```

### Custom Styling
Configure in `config/l5-swagger.php`

### Multiple API Versions
Support for versioned documentation

## 📞 Next Steps

1. ✅ Swagger documentation updated
2. ✅ All endpoints documented
3. ✅ Schema definitions added
4. ✅ Documentation regenerated
5. ⏳ Share Swagger URL with mobile team
6. ⏳ Use for API testing and validation

---

**Documentation URL:** `/api/documentation`
**Generated:** December 2, 2025
**Status:** ✅ Complete and Available
