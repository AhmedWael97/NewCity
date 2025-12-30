# FCM Token API - Quick Reference Card

## 🚀 Endpoints

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| POST | `/api/v1/guest-device-tokens` | ❌ No | Register guest device |
| POST | `/api/v1/device-tokens` | ✅ Yes | Register authenticated device |
| GET | `/api/v1/device-tokens` | ✅ Yes | Get user's devices |
| DELETE | `/api/v1/device-tokens` | ✅ Yes | Remove device token |

## 📝 Request Fields

### Required Fields
```json
{
  "device_token": "string (required)",
  "device_type": "android|ios|web (required)"
}
```

### Optional Fields
```json
{
  "device_name": "string (max 255)",
  "os_version": "string (max 100)",
  "device_model": "string (max 255)",
  "device_manufacturer": "string (max 255)",
  "device_id": "string (max 255)",
  "app_version": "string (max 50)",
  "app_build_number": "string (max 50)",
  "language": "string (max 10, default: ar)",
  "timezone": "string (max 100)",
  "notifications_enabled": "boolean (default: true)",
  "device_metadata": "object (JSON)"
}
```

## 💻 Usage Examples

### Flutter
```dart
await DeviceTokenService().registerDeviceToken(
  accessToken: isLoggedIn ? token : null
);
```

### React Native
```javascript
await registerDeviceToken(isLoggedIn ? token : null);
```

### cURL
```bash
# Guest
curl -X POST https://api.example.com/api/v1/guest-device-tokens \
  -H "Content-Type: application/json" \
  -d '{"device_token":"FCM_TOKEN","device_type":"android"}'

# Authenticated
curl -X POST https://api.example.com/api/v1/device-tokens \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"device_token":"FCM_TOKEN","device_type":"ios"}'
```

## ✅ Success Response
```json
{
  "success": true,
  "message": "Device token registered successfully",
  "data": {
    "id": 123,
    "device_type": "android",
    "device_model": "Samsung Galaxy S21",
    "is_active": true,
    "notifications_enabled": true
  }
}
```

## ❌ Error Response
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "device_token": ["The device token field is required."],
    "device_type": ["The device type must be one of: web, android, ios."]
  }
}
```

## 🔄 Common Flows

### 1. Guest User Flow
```
App Launch → Register as Guest → Use App → Login → Re-register with Auth
```

### 2. Authenticated User Flow
```
App Launch → Check Login → Register with Auth → Use App → Logout → Remove Token
```

### 3. Token Refresh Flow
```
FCM Token Changed → Re-register with New Token → Old Record Updated
```

## 📊 Database Queries

### View All Devices
```sql
SELECT * FROM device_tokens ORDER BY created_at DESC;
```

### Count by Type
```sql
SELECT device_type, COUNT(*) FROM device_tokens 
WHERE is_active = 1 GROUP BY device_type;
```

### User's Devices
```sql
SELECT * FROM device_tokens WHERE user_id = 1;
```

### Guest Devices
```sql
SELECT * FROM device_tokens WHERE user_id IS NULL;
```

## 🎯 Best Practices

✅ Register on app startup
✅ Re-register on token refresh
✅ Re-register after login
✅ Remove token on logout
✅ Handle errors with retry
✅ Test both guest and auth flows

## 📚 Full Documentation

- **Complete Guide:** `FCM_TOKEN_API_DOCUMENTATION.md`
- **Testing Guide:** `FCM_TOKEN_API_TESTING_GUIDE.md`
- **Test Requests:** `FCM_TOKEN_API_REQUESTS.http`
- **Implementation:** `FCM_TOKEN_IMPLEMENTATION_SUMMARY.md`

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| 422 Validation Error | Check required fields are present |
| 401 Unauthorized | Verify Bearer token is valid |
| Duplicate tokens | Use updateOrCreate (already handled) |
| Token not found | Ensure token was registered first |

## 📞 Quick Test

```bash
# Test guest registration
curl -X POST http://localhost:8000/api/v1/guest-device-tokens \
  -H "Content-Type: application/json" \
  -d '{"device_token":"test123","device_type":"android"}'
```

Expected: `{"success":true,"message":"Guest device token registered successfully",...}`
