# Quick Start Guide - Mobile App Control

## 🚀 Get Started in 5 Minutes

### Step 1: Configure Environment Variables

Add these to your `.env` file:

```env
# Generate a secure random API key (64 characters)
MOBILE_API_KEY=paste-your-generated-key-here

# Optional: For signature verification
MOBILE_APP_SECRET=paste-your-secret-key-here

# Get this from Firebase Console
FIREBASE_SERVER_KEY=paste-your-firebase-server-key-here

# Enable Firebase notifications
FIREBASE_ENABLED=true
```

**Generate API Key:**
```bash
php artisan tinker
>>> Str::random(64)
# Copy the output and paste it as MOBILE_API_KEY
```

### Step 2: Get Firebase Server Key

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project (or create new)
3. Click Settings ⚙️ > Project Settings
4. Go to "Cloud Messaging" tab
5. Copy the **Server key** (under Cloud Messaging API - Legacy)
6. Paste it as `FIREBASE_SERVER_KEY` in your `.env`

### Step 3: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Access Admin Panel

1. Login to admin panel: `https://yourdomain.com/admin`
2. Navigate to **Mobile App Settings** (or go directly to `/admin/app-settings`)
3. Configure your app settings:
   - App Name: Your app's name
   - Upload App Icon (optional)
   - Set version numbers
   - Enable Firebase notifications

4. Click **Save Settings**

### Step 5: Send Your First Notification

1. Click **"Manage Notifications"** button
2. Click **"Send New Notification"**
3. Fill in:
   - Title: "Welcome!"
   - Body: "Thank you for using our app!"
   - Type: General
   - Target: All users
4. Check **"Send immediately after saving"**
5. Click **"Create Notification"**

## 📱 Mobile App Integration

### For Flutter Apps

```dart
// 1. Add to pubspec.yaml
dependencies:
  firebase_messaging: ^14.0.0
  http: ^1.0.0

// 2. Add this to your main app initialization
import 'package:firebase_messaging/firebase_messaging.dart';

Future<void> initializeApp() async {
  // Request permission
  await FirebaseMessaging.instance.requestPermission();
  
  // Get FCM token
  String? token = await FirebaseMessaging.instance.getToken();
  
  // Register with your backend
  if (token != null) {
    await registerDevice(token);
  }
}

// 3. Register device function
Future<void> registerDevice(String fcmToken) async {
  final response = await http.post(
    Uri.parse('https://yourdomain.com/api/v1/mobile/device/register'),
    headers: {
      'X-API-Key': 'your-api-key-from-env',
      'Content-Type': 'application/json',
    },
    body: jsonEncode({
      'device_token': fcmToken,
      'device_type': Platform.isIOS ? 'ios' : 'android',
      'app_version': '1.0.0',
    }),
  );
}
```

### For React Native Apps

```javascript
// 1. Install dependencies
npm install @react-native-firebase/app @react-native-firebase/messaging axios

// 2. Add to your App.js
import messaging from '@react-native-firebase/messaging';
import axios from 'axios';

const API_KEY = 'your-api-key-from-env';

async function setupPushNotifications() {
  // Request permission
  await messaging().requestPermission();
  
  // Get FCM token
  const token = await messaging().getToken();
  
  // Register device
  await axios.post(
    'https://yourdomain.com/api/v1/mobile/device/register',
    {
      device_token: token,
      device_type: Platform.OS,
      app_version: '1.0.0',
    },
    {
      headers: {
        'X-API-Key': API_KEY,
      },
    }
  );
}

// Call on app startup
useEffect(() => {
  setupPushNotifications();
}, []);
```

## 🧪 Testing

### Test 1: Check App Configuration

```bash
curl -X GET https://yourdomain.com/api/v1/mobile/config \
  -H "X-API-Key: your-api-key"
```

Expected response:
```json
{
  "success": true,
  "data": {
    "app_name": "City App",
    "maintenance_mode": false,
    "api_status": "active",
    ...
  }
}
```

### Test 2: Register a Device

```bash
curl -X POST https://yourdomain.com/api/v1/mobile/device/register \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "test-token-123",
    "device_type": "android",
    "app_version": "1.0.0"
  }'
```

### Test 3: Send Test Notification

1. Go to Admin Panel > App Settings > Notifications
2. Click "Send New Notification"
3. Use your test device token
4. Click "Send immediately"
5. Check your mobile device

## 🔧 Troubleshooting

### Problem: Notifications not sending

**Solution:**
1. Check Firebase is enabled: Admin > App Settings
2. Verify `FIREBASE_SERVER_KEY` in `.env`
3. Check logs: `storage/logs/laravel.log`
4. Verify device token is registered: Admin > App Settings > Devices

### Problem: API returns 401 Unauthorized

**Solution:**
1. Check `MOBILE_API_KEY` is set in `.env`
2. Verify header name is exactly `X-API-Key` (case-sensitive)
3. Run `php artisan config:clear`

### Problem: Maintenance mode not working

**Solution:**
1. Enable in Admin > App Settings
2. Run `php artisan cache:clear`
3. Check `app_settings` table has `maintenance_mode = true`

## 📊 Monitor Your App

### View Statistics

Go to **Admin > App Settings** to see:
- Total registered devices
- Active devices
- iOS vs Android distribution
- Pending notifications

### Check Device List

Go to **Admin > App Settings > Devices** to see:
- All registered devices
- Last activity time
- App versions
- User associations

### Monitor Notifications

Go to **Admin > App Settings > Notifications** to see:
- All sent notifications
- Delivery success rates
- Failed notifications

## 🎯 Common Use Cases

### 1. Enable Maintenance Mode

1. Go to Admin > App Settings
2. Check "Enable maintenance mode"
3. Enter custom message (e.g., "We're upgrading! Back soon.")
4. Save settings
5. Mobile apps will now show maintenance screen

### 2. Force App Update

1. Go to Admin > App Settings
2. Set "Minimum app version" to new version (e.g., "2.0.0")
3. Check "Force update"
4. Save settings
5. Users with older versions must update

### 3. Send Promotional Notification

1. Go to Admin > Notifications > Create
2. Title: "50% Off This Weekend!"
3. Type: Promotional
4. Target: All users
5. Add image (optional)
6. Set action URL to your promo page
7. Schedule or send immediately

### 4. Target Specific City

1. Create notification
2. Select Target: "Specific City"
3. Enter city IDs (comma-separated)
4. Send

## 📚 Next Steps

1. Read full documentation: `MOBILE_APP_CONTROL_GUIDE.md`
2. Review implementation details: `IMPLEMENTATION_SUMMARY.md`
3. Customize notification templates
4. Set up scheduled notifications
5. Monitor analytics and improve

## 🆘 Need Help?

- Check logs: `storage/logs/laravel.log`
- Review documentation files
- Test API endpoints with Postman
- Check Firebase console for delivery stats

---

**You're all set! 🎉**

Your mobile app control system is ready to use. Start by sending your first notification!
