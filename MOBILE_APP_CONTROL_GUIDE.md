# Mobile App Control & Push Notifications

This document describes the mobile app control features and push notification system implemented in the City application.

## Features

### 1. **App Settings Management**
- Change app name dynamically
- Update app icon and logo
- Control maintenance mode
- Manage app version requirements
- Configure API status (active/limited/disabled)

### 2. **Version Control**
- Set minimum required app version
- Force update mechanism
- Optional update notifications
- Platform-specific app store URLs

### 3. **Push Notifications**
- Firebase Cloud Messaging integration
- Send notifications to all users or specific targets
- Schedule notifications for later delivery
- Track notification delivery status
- Support for images and action URLs
- Different notification types (general, alert, promo, update)

### 4. **Device Management**
- Register and track mobile devices
- iOS and Android support
- Device token management
- Automatic cleanup of inactive tokens

### 5. **Secure API Access**
- API key authentication
- Optional signature verification
- App version checking middleware
- Maintenance mode enforcement

## Setup Instructions

### 1. Environment Configuration

Add the following to your `.env` file:

```env
# Mobile App API Security
MOBILE_API_KEY=your-secure-random-api-key-here
MOBILE_APP_SECRET=your-app-secret-for-signature-verification

# Firebase Cloud Messaging
FIREBASE_SERVER_KEY=your-firebase-server-key-from-firebase-console
FIREBASE_ENABLED=true
```

**To generate secure keys:**
```bash
# Generate API Key
php artisan tinker
>>> Str::random(64)

# Or use online tools like: https://randomkeygen.com/
```

### 2. Firebase Setup

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project or select existing
3. Go to Project Settings > Cloud Messaging
4. Copy the **Server Key** (Legacy)
5. Add it to your `.env` file as `FIREBASE_SERVER_KEY`

### 3. Database Migration

The migrations have already been run. If you need to run them again:

```bash
php artisan migrate
```

This creates:
- `app_settings` - Stores app configuration
- `device_tokens` - Registered mobile devices
- `push_notifications` - Notification history
- `notification_logs` - Delivery tracking

## Admin Panel Usage

### Accessing App Settings

Navigate to: **Admin Dashboard > Mobile App Settings**

Or directly: `https://yourdomain.com/admin/app-settings`

### Managing App Settings

1. **General Settings**
   - Set app name
   - Upload app icon and logo

2. **Maintenance Mode**
   - Toggle maintenance mode
   - Set custom maintenance message
   - Users will see the message when trying to access the app

3. **Version Control**
   - Set minimum required version
   - Set latest available version
   - Force update: users below minimum version cannot access
   - Optional update: users below latest version see update notification

4. **API Status**
   - Active: Full API access
   - Limited: Restricted access (can be customized)
   - Disabled: API completely disabled

5. **Firebase Settings**
   - Enable/disable push notifications
   - Configure Firebase server key

### Sending Push Notifications

1. Go to **Admin > App Settings > Notifications**
2. Click **"Send New Notification"**
3. Fill in the form:
   - **Title**: Notification headline (required)
   - **Body**: Notification content (required)
   - **Type**: general/alert/promo/update
   - **Target**: All users, specific city, or specific users
   - **Image**: Optional notification image
   - **Action URL**: Optional deep link
   - **Schedule**: Send now or schedule for later

4. Click **"Create Notification"**

### Viewing Registered Devices

Go to **Admin > App Settings > Devices** to see:
- All registered devices
- Device type (iOS/Android)
- App version
- Last active time
- Associated user

## API Endpoints

### Public Endpoints (Require API Key)

All mobile endpoints require the `X-API-Key` header.

#### Get App Configuration
```http
GET /api/v1/mobile/config
Headers:
  X-API-Key: your-api-key
```

Response:
```json
{
  "success": true,
  "data": {
    "app_name": "City App",
    "app_icon_url": "https://...",
    "maintenance_mode": false,
    "force_update": false,
    "min_app_version": "1.0.0",
    "latest_app_version": "1.5.0",
    "api_status": "active"
  }
}
```

#### Check App Status
```http
POST /api/v1/mobile/status
Headers:
  X-API-Key: your-api-key
Body:
{
  "app_version": "1.2.0",
  "platform": "android"
}
```

Response:
```json
{
  "success": true,
  "status": "active",
  "message": "App is up to date",
  "can_access": true
}
```

#### Register Device
```http
POST /api/v1/mobile/device/register
Headers:
  X-API-Key: your-api-key
  Authorization: Bearer {token} (optional)
Body:
{
  "device_token": "firebase-token-here",
  "device_type": "ios",
  "device_name": "iPhone 13",
  "app_version": "1.2.0"
}
```

#### Mark Notification as Opened
```http
POST /api/v1/mobile/notification/opened
Headers:
  X-API-Key: your-api-key
Body:
{
  "notification_id": 123,
  "device_token": "firebase-token-here"
}
```

### API Security

The mobile API uses multiple security layers:

1. **API Key Authentication**: Every request must include `X-API-Key` header
2. **Optional Signature Verification**: Requests can include `X-App-Signature` header with HMAC-SHA256 signature
3. **App Version Checking**: Can enforce minimum version requirements
4. **Maintenance Mode**: Can block all access during maintenance

### Generating Request Signature (Optional)

For extra security, sign your requests:

```javascript
// JavaScript/React Native example
const crypto = require('crypto');

const requestBody = JSON.stringify({
  device_token: "...",
  device_type: "ios"
});

const signature = crypto
  .createHmac('sha256', process.env.MOBILE_APP_SECRET)
  .update(requestBody)
  .digest('hex');

fetch('https://api.example.com/api/v1/mobile/device/register', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-API-Key': process.env.MOBILE_API_KEY,
    'X-App-Signature': signature
  },
  body: requestBody
});
```

## Mobile App Integration

### Flutter/Dart Example

```dart
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl = 'https://yourdomain.com/api/v1';
  static const String apiKey = 'your-api-key';

  // Get app configuration
  Future<Map<String, dynamic>> getAppConfig() async {
    final response = await http.get(
      Uri.parse('$baseUrl/mobile/config'),
      headers: {'X-API-Key': apiKey},
    );
    return jsonDecode(response.body);
  }

  // Check app status
  Future<Map<String, dynamic>> checkStatus(String appVersion) async {
    final response = await http.post(
      Uri.parse('$baseUrl/mobile/status'),
      headers: {
        'X-API-Key': apiKey,
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'app_version': appVersion,
        'platform': Platform.isIOS ? 'ios' : 'android',
      }),
    );
    return jsonDecode(response.body);
  }

  // Register device for push notifications
  Future<void> registerDevice(String fcmToken) async {
    final response = await http.post(
      Uri.parse('$baseUrl/mobile/device/register'),
      headers: {
        'X-API-Key': apiKey,
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'device_token': fcmToken,
        'device_type': Platform.isIOS ? 'ios' : 'android',
        'device_name': await getDeviceName(),
        'app_version': '1.0.0',
      }),
    );
  }
}
```

### React Native Example

```javascript
import messaging from '@react-native-firebase/messaging';
import axios from 'axios';

const API_BASE_URL = 'https://yourdomain.com/api/v1';
const API_KEY = 'your-api-key';

// Initialize Firebase messaging
async function requestUserPermission() {
  const authStatus = await messaging().requestPermission();
  const enabled =
    authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
    authStatus === messaging.AuthorizationStatus.PROVISIONAL;

  if (enabled) {
    getFCMToken();
  }
}

// Get FCM token and register device
async function getFCMToken() {
  const fcmToken = await messaging().getToken();
  if (fcmToken) {
    await registerDevice(fcmToken);
  }
}

// Register device with backend
async function registerDevice(fcmToken) {
  try {
    await axios.post(
      `${API_BASE_URL}/mobile/device/register`,
      {
        device_token: fcmToken,
        device_type: Platform.OS,
        app_version: '1.0.0',
      },
      {
        headers: {
          'X-API-Key': API_KEY,
        },
      }
    );
  } catch (error) {
    console.error('Failed to register device:', error);
  }
}

// Handle foreground notifications
messaging().onMessage(async (remoteMessage) => {
  console.log('Notification received:', remoteMessage);
  // Show local notification or update UI
});

// Handle notification opened
messaging().onNotificationOpenedApp((remoteMessage) => {
  console.log('Notification opened:', remoteMessage);
  // Navigate to specific screen if action_url provided
  if (remoteMessage.data?.action_url) {
    // Navigate to action_url
  }
});
```

## Testing

### Test Push Notification

1. Register a test device using the API
2. Go to Admin > App Settings > Notifications
3. Click "Send New Notification"
4. Fill in test data and click "Send Now"
5. Check your mobile device

### Test Maintenance Mode

1. Enable maintenance mode in App Settings
2. Try accessing the API from your mobile app
3. You should receive a 503 response with maintenance message

### Test Version Control

1. Set minimum version to "2.0.0"
2. Send request with app_version "1.0.0"
3. You should receive update_required response

## Troubleshooting

### Notifications Not Sending

1. Check Firebase is enabled in settings
2. Verify FIREBASE_SERVER_KEY is correct
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify device tokens are active in database

### API Key Not Working

1. Verify MOBILE_API_KEY is set in .env
2. Check header name is exactly `X-API-Key`
3. Clear config cache: `php artisan config:clear`

### Maintenance Mode Not Working

1. Clear cache: `php artisan cache:clear`
2. Check app_settings table has maintenance_mode record
3. Verify middleware is applied to routes

## Best Practices

1. **API Key Security**
   - Never commit API keys to version control
   - Use different keys for development/production
   - Rotate keys periodically

2. **Firebase**
   - Enable analytics in Firebase console
   - Monitor notification delivery rates
   - Clean up inactive tokens regularly

3. **Notifications**
   - Keep titles short and descriptive
   - Test on both iOS and Android
   - Use appropriate notification types
   - Schedule important notifications for optimal times

4. **Version Control**
   - Use semantic versioning (major.minor.patch)
   - Give users advance notice before forcing updates
   - Test new versions thoroughly before releasing

## Support

For issues or questions, contact the development team or refer to:
- Laravel Documentation: https://laravel.com/docs
- Firebase Documentation: https://firebase.google.com/docs
- Sanctum Documentation: https://laravel.com/docs/sanctum
