# FCM Token API - Quick Test Guide

## Test the API Endpoints

### 1. Test Guest Device Token Registration (No Authentication)

**Request:**
```bash
curl -X POST https://your-domain.com/api/v1/guest-device-tokens \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "device_token": "test_fcm_token_guest_12345",
    "device_type": "android",
    "device_name": "Test Device",
    "os_version": "Android 13",
    "device_model": "Pixel 7",
    "device_manufacturer": "Google",
    "device_id": "test-device-001",
    "app_version": "1.0.0",
    "app_build_number": "100",
    "language": "ar",
    "timezone": "Asia/Riyadh",
    "notifications_enabled": true
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Guest device token registered successfully",
  "data": {
    "id": 1,
    "device_type": "android",
    "device_model": "Pixel 7",
    "is_active": true,
    "notifications_enabled": true
  }
}
```

---

### 2. Test Authenticated Device Token Registration

First, login to get access token:
```bash
curl -X POST https://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

Then register device:
```bash
curl -X POST https://your-domain.com/api/v1/device-tokens \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "device_token": "test_fcm_token_auth_12345",
    "device_type": "ios",
    "device_name": "iPhone 14 Pro",
    "os_version": "iOS 17.2",
    "device_model": "iPhone15,2",
    "device_manufacturer": "Apple",
    "device_id": "test-device-002",
    "app_version": "1.0.0",
    "app_build_number": "100",
    "language": "ar",
    "timezone": "Asia/Riyadh",
    "notifications_enabled": true
  }'
```

---

### 3. Test Get User's Devices

```bash
curl -X GET https://your-domain.com/api/v1/device-tokens \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

---

### 4. Test Remove Device Token

```bash
curl -X DELETE https://your-domain.com/api/v1/device-tokens \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "device_token": "test_fcm_token_auth_12345"
  }'
```

---

## Testing with Postman

### Collection Setup

1. **Create Environment Variables:**
   - `base_url`: `https://your-domain.com/api/v1`
   - `access_token`: (will be set after login)

2. **Import Requests:**
   - Guest Registration: `POST {{base_url}}/guest-device-tokens`
   - Login: `POST {{base_url}}/auth/login`
   - Auth Registration: `POST {{base_url}}/device-tokens`
   - Get Devices: `GET {{base_url}}/device-tokens`
   - Remove Device: `DELETE {{base_url}}/device-tokens`

---

## Verification Steps

### Check Database Directly

```sql
-- View all registered devices
SELECT 
    id,
    user_id,
    device_type,
    device_name,
    device_model,
    os_version,
    app_version,
    is_active,
    notifications_enabled,
    created_at,
    last_used_at
FROM device_tokens
ORDER BY created_at DESC;

-- Count devices by type
SELECT device_type, COUNT(*) as count
FROM device_tokens
WHERE is_active = 1
GROUP BY device_type;

-- View user's devices
SELECT * FROM device_tokens WHERE user_id = 1;

-- View guest devices
SELECT * FROM device_tokens WHERE user_id IS NULL;
```

---

## Common Test Scenarios

### Scenario 1: Guest to Authenticated User Flow
1. Register as guest (no auth)
2. User logs in
3. Re-register same device token with auth
4. Verify device is now associated with user

### Scenario 2: Multiple Devices per User
1. Login as user
2. Register device 1 (Android)
3. Register device 2 (iOS)
4. GET /device-tokens to see both devices

### Scenario 3: Token Update
1. Register device with token ABC
2. Register same device with token ABC again (different details)
3. Verify only one record exists (updated)

### Scenario 4: Logout/Uninstall
1. Register device
2. Call DELETE endpoint
3. Verify device token is removed

---

## Error Testing

### Test Invalid Device Type
```bash
curl -X POST https://your-domain.com/api/v1/guest-device-tokens \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "test_token",
    "device_type": "invalid_type"
  }'
```

Expected: 422 Validation Error

### Test Missing Required Fields
```bash
curl -X POST https://your-domain.com/api/v1/guest-device-tokens \
  -H "Content-Type: application/json" \
  -d '{}'
```

Expected: 422 Validation Error

### Test Unauthorized Access
```bash
curl -X GET https://your-domain.com/api/v1/device-tokens \
  -H "Accept: application/json"
```

Expected: 401 Unauthorized

---

## Performance Testing

### Test Concurrent Registrations
Use a tool like Apache Bench or k6 to simulate multiple simultaneous registrations:

```bash
# Simple load test with Apache Bench
ab -n 100 -c 10 -T 'application/json' -p device_data.json \
  https://your-domain.com/api/v1/guest-device-tokens
```

---

## Monitoring

After deployment, monitor:
- Total registered devices
- Active vs inactive devices
- Devices by type (Android/iOS/Web)
- Guest vs authenticated devices
- Average tokens per user
- Token refresh rate

```sql
-- Analytics queries
SELECT 
    COUNT(*) as total_devices,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_devices,
    SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest_devices,
    SUM(CASE WHEN device_type = 'android' THEN 1 ELSE 0 END) as android,
    SUM(CASE WHEN device_type = 'ios' THEN 1 ELSE 0 END) as ios,
    SUM(CASE WHEN device_type = 'web' THEN 1 ELSE 0 END) as web
FROM device_tokens;
```
