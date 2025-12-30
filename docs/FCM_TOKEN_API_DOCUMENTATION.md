# FCM Token & Device Registration API Documentation

## Overview
This API allows mobile applications to register FCM (Firebase Cloud Messaging) tokens and device details for push notifications.

## Base URL
```
https://your-domain.com/api/v1
```

---

## Endpoints

### 1. Register Device Token (Authenticated Users)
Register or update FCM token for authenticated users.

**Endpoint:** `POST /device-tokens`

**Authentication:** Required (Bearer Token)

**Headers:**
```
Authorization: Bearer {your_access_token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "device_token": "dGhpc19pc19hX2ZjbV90b2tlbl9leGFtcGxl...",
  "device_type": "android",
  "device_name": "Samsung Galaxy S21",
  "os_version": "Android 13",
  "device_model": "SM-G991B",
  "device_manufacturer": "Samsung",
  "device_id": "unique-device-identifier-123",
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

**Required Fields:**
- `device_token` (string): FCM token from Firebase
- `device_type` (string): One of: `android`, `ios`, `web`

**Optional Fields:**
- `city_id` (integer): City ID for targeted notifications (null for global notifications)
- `device_name` (string, max 255): Device display name
- `os_version` (string, max 100): Operating system version
- `device_model` (string, max 255): Device model number
- `device_manufacturer` (string, max 255): Device manufacturer
- `device_id` (string, max 255): Unique device identifier
- `app_version` (string, max 50): App version (e.g., "1.0.0")
- `app_build_number` (string, max 50): Build number (e.g., "100")
- `language` (string, max 10): Language code (default: "ar")
- `timezone` (string, max 100): Timezone (e.g., "Asia/Riyadh")
- `notifications_enabled` (boolean): Whether notifications are enabled (default: true)
- `device_metadata` (object): Additional device information as JSON

**Success Response (200):**
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

**Error Response (422):**
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

---

### 2. Register Device Token (Guest Users)
Register FCM token for users who are not logged in.

**Endpoint:** `POST /guest-device-tokens`

**Authentication:** Not required

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
Same as authenticated endpoint above.

**Success Response (200):**
```json
{
  "success": true,
  "message": "Guest device token registered successfully",
  "data": {
    "id": 456,
    "device_type": "android",
    "device_model": "SM-G991B",
    "is_active": true,
    "notifications_enabled": true
  }
}
```

---

### 3. Get User's Device Tokens
Retrieve all registered devices for the current user.

**Endpoint:** `GET /device-tokens`

**Authentication:** Required (Bearer Token)

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "device_type": "android",
      "device_name": "Samsung Galaxy S21",
      "app_version": "1.0.0",
      "is_active": true,
      "last_used_at": "2025-12-02T10:30:00.000000Z",
      "created_at": "2025-11-01T08:15:00.000000Z"
    },
    {
      "id": 124,
      "device_type": "ios",
      "device_name": "iPhone 14 Pro",
      "app_version": "1.0.0",
      "is_active": true,
      "last_used_at": "2025-12-01T14:20:00.000000Z",
      "created_at": "2025-10-15T12:45:00.000000Z"
    }
  ]
}
```

---

### 4. Remove Device Token
Unregister a device token (used when user logs out or uninstalls app).

**Endpoint:** `DELETE /device-tokens`

**Authentication:** Required (Bearer Token)

**Request Body:**
```json
{
  "device_token": "dGhpc19pc19hX2ZjbV90b2tlbl9leGFtcGxl..."
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Device token removed successfully"
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Device token not found"
}
```

---

## Implementation Examples

### Flutter (Android/iOS)

```dart
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:device_info_plus/device_info_plus.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:io';

class DeviceTokenService {
  final String baseUrl = 'https://your-domain.com/api/v1';
  
  Future<void> registerDeviceToken({String? accessToken}) async {
    // Get FCM Token
    String? fcmToken = await FirebaseMessaging.instance.getToken();
    if (fcmToken == null) return;
    
    // Get device info
    final deviceInfo = DeviceInfoPlugin();
    Map<String, dynamic> deviceData = {};
    
    if (Platform.isAndroid) {
      AndroidDeviceInfo androidInfo = await deviceInfo.androidInfo;
      deviceData = {
        'device_token': fcmToken,
        'device_type': 'android',
        'device_name': androidInfo.model,
        'os_version': 'Android ${androidInfo.version.release}',
        'device_model': androidInfo.model,
        'device_manufacturer': androidInfo.manufacturer,
        'device_id': androidInfo.id,
        'app_version': '1.0.0',
        'app_build_number': '100',
        'language': 'ar',
        'timezone': DateTime.now().timeZoneName,
        'notifications_enabled': true,
        'device_metadata': {
          'sdk_int': androidInfo.version.sdkInt,
          'brand': androidInfo.brand,
        }
      };
    } else if (Platform.isIOS) {
      IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
      deviceData = {
        'device_token': fcmToken,
        'device_type': 'ios',
        'device_name': iosInfo.name,
        'os_version': 'iOS ${iosInfo.systemVersion}',
        'device_model': iosInfo.model,
        'device_manufacturer': 'Apple',
        'device_id': iosInfo.identifierForVendor,
        'app_version': '1.0.0',
        'app_build_number': '100',
        'language': 'ar',
        'timezone': DateTime.now().timeZoneName,
        'notifications_enabled': true,
        'device_metadata': {
          'device': iosInfo.utsname.machine,
        }
      };
    }
    
    // Determine endpoint based on authentication
    String endpoint = accessToken != null 
        ? '/device-tokens' 
        : '/guest-device-tokens';
    
    // Make API request
    final response = await http.post(
      Uri.parse('$baseUrl$endpoint'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        if (accessToken != null) 'Authorization': 'Bearer $accessToken',
      },
      body: jsonEncode(deviceData),
    );
    
    if (response.statusCode == 200) {
      print('Device token registered successfully');
    } else {
      print('Failed to register device token: ${response.body}');
    }
  }
  
  Future<void> unregisterDeviceToken(String accessToken) async {
    String? fcmToken = await FirebaseMessaging.instance.getToken();
    if (fcmToken == null) return;
    
    final response = await http.delete(
      Uri.parse('$baseUrl/device-tokens'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $accessToken',
      },
      body: jsonEncode({'device_token': fcmToken}),
    );
    
    if (response.statusCode == 200) {
      print('Device token unregistered successfully');
    }
  }
}

// Usage
void main() async {
  final tokenService = DeviceTokenService();
  
  // For guest users
  await tokenService.registerDeviceToken();
  
  // For authenticated users
  await tokenService.registerDeviceToken(accessToken: 'your_access_token');
}
```

### React Native

```javascript
import messaging from '@react-native-firebase/messaging';
import DeviceInfo from 'react-native-device-info';
import axios from 'axios';

const BASE_URL = 'https://your-domain.com/api/v1';

export const registerDeviceToken = async (accessToken = null) => {
  try {
    // Request permission (iOS)
    const authStatus = await messaging().requestPermission();
    const enabled =
      authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
      authStatus === messaging.AuthorizationStatus.PROVISIONAL;

    if (!enabled) {
      console.log('Permission not granted');
      return;
    }

    // Get FCM token
    const fcmToken = await messaging().getToken();

    // Collect device info
    const deviceData = {
      device_token: fcmToken,
      device_type: Platform.OS === 'android' ? 'android' : 'ios',
      device_name: await DeviceInfo.getDeviceName(),
      os_version: `${Platform.OS} ${await DeviceInfo.getSystemVersion()}`,
      device_model: await DeviceInfo.getModel(),
      device_manufacturer: await DeviceInfo.getManufacturer(),
      device_id: await DeviceInfo.getUniqueId(),
      app_version: await DeviceInfo.getVersion(),
      app_build_number: await DeviceInfo.getBuildNumber(),
      language: 'ar',
      timezone: await DeviceInfo.getTimezone(),
      notifications_enabled: enabled,
      device_metadata: {
        brand: await DeviceInfo.getBrand(),
        device_type: await DeviceInfo.getDeviceType(),
      }
    };

    // Determine endpoint
    const endpoint = accessToken ? '/device-tokens' : '/guest-device-tokens';

    // Make API request
    const response = await axios.post(
      `${BASE_URL}${endpoint}`,
      deviceData,
      {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          ...(accessToken && { 'Authorization': `Bearer ${accessToken}` }),
        },
      }
    );

    console.log('Device registered:', response.data);
  } catch (error) {
    console.error('Failed to register device:', error);
  }
};

export const unregisterDeviceToken = async (accessToken) => {
  try {
    const fcmToken = await messaging().getToken();

    await axios.delete(
      `${BASE_URL}/device-tokens`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${accessToken}`,
        },
        data: { device_token: fcmToken },
      }
    );

    console.log('Device unregistered successfully');
  } catch (error) {
    console.error('Failed to unregister device:', error);
  }
};
```

---

## Best Practices

### 1. **When to Register**
- **On App Launch:** Register the token when the app starts
- **After Login:** Re-register with user_id when user logs in
- **Token Refresh:** Re-register when FCM token is refreshed

### 2. **Guest vs Authenticated**
- Use `/guest-device-tokens` for users who haven't logged in
- Use `/device-tokens` for authenticated users
- When user logs in, re-register the token to associate it with their account

### 3. **Token Refresh**
```dart
// Flutter example
FirebaseMessaging.instance.onTokenRefresh.listen((newToken) {
  registerDeviceToken(); // Re-register with new token
});
```

### 4. **Error Handling**
- Handle network failures gracefully
- Retry registration if it fails
- Store pending registrations locally if offline

### 5. **Privacy**
- Only collect necessary device information
- Inform users about data collection in privacy policy
- Allow users to opt-out of push notifications

---

## Database Schema

The `device_tokens` table stores:
- FCM token and device identification
- Operating system and hardware details
- App version and build information
- User preferences (language, timezone, notifications)
- Last activity tracking
- Additional metadata in JSON format

---

## Notes

- **Token Uniqueness:** Each device_token is unique. If the same token is registered again, it will be updated.
- **Auto-cleanup:** Inactive tokens (not used for 90+ days) may be automatically deactivated.
- **IP Address:** User's IP address is automatically captured and stored.
- **Timezone:** Defaults to server timezone if not provided.
- **Language:** Defaults to Arabic ("ar") if not provided.

---

## Support

For questions or issues, contact the backend development team.
