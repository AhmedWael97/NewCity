# How to Get the Correct Firebase Server Key for Android, iOS, and Web

## Step-by-Step Guide

### Step 1: Enable Firebase Cloud Messaging API

1. Go to: https://console.cloud.google.com/apis/library/fcm.googleapis.com?project=senu-66fb2
2. Click the **"ENABLE"** button
3. Wait for it to enable (takes a few seconds)

### Step 2: Create a Server Key (API Key)

1. Go to: https://console.cloud.google.com/apis/credentials?project=senu-66fb2
2. Click **"+ CREATE CREDENTIALS"** at the top
3. Select **"API key"**
4. A new API key will be created - **COPY IT IMMEDIATELY**
5. Click **"RESTRICT KEY"** (or the pencil icon to edit)

### Step 3: Restrict the API Key

1. Give it a name: `Firebase Server Key`
2. Under **"Application restrictions"**: Select "None" (or "IP addresses" if you want more security)
3. Under **"API restrictions"**:
   - Select **"Restrict key"**
   - Check **"Firebase Cloud Messaging API"**
   - You can also add:
     - Firebase Installations API
     - Firebase Management API
4. Click **"SAVE"**

### Step 4: Update Your .env File

```env
FIREBASE_SERVER_KEY=AIzaSy... (the NEW key you just created, not the web API key)
```

### Step 5: Clear Cache and Test

```bash
php artisan config:clear
php test-send.php
```

---

## Important Notes

### Difference Between Keys:

1. **Web API Key** (`AIzaSyDiVlpeECTtVrseh86myD1c6LNZuGNaKH4`)
   - Used in: **Browser JavaScript** (firebase-init.blade.php)
   - For: Client-side authentication
   - Already in: `FIREBASE_API_KEY`

2. **Server Key** (What you need to create)
   - Used in: **Server-side PHP** (NotificationService.php)
   - For: Sending push notifications to all platforms
   - Should go in: `FIREBASE_SERVER_KEY`

### Current Status:

❌ You're using the **Web API Key** as **Server Key** - that's why it fails!  
✅ You need to create a **NEW API key** restricted to FCM API

---

## Alternative: Check if Server Key Already Exists

Before creating a new one, check if you already have one:

1. Go to: https://console.cloud.google.com/apis/credentials?project=senu-66fb2
2. Look for an API key with name like:
   - "Server key"
   - "Firebase Server Key"  
   - "API key" (created automatically)
3. Click on it to see restrictions
4. If it has "Firebase Cloud Messaging API" - USE THAT ONE
5. Copy the key value

---

## Testing After Setup

Once you have the correct server key:

```bash
# Check configuration
php artisan notifications:check

# Test sending
php test-send.php

# Should show:
# Success Count: 1
# Failure Count: 0
```

---

## For Mobile Apps (Flutter)

### Android Setup:
1. Download `google-services.json` from Firebase Console
2. Place in `android/app/` folder
3. Use the same `FIREBASE_SERVER_KEY` in your backend

### iOS Setup:
1. Download `GoogleService-Info.plist` from Firebase Console  
2. Place in `ios/Runner/` folder
3. Use the same `FIREBASE_SERVER_KEY` in your backend

### Flutter Code:
```dart
// In your Flutter app
final messaging = FirebaseMessaging.instance;

// Request permission
await messaging.requestPermission();

// Get FCM token
String? token = await messaging.getToken();

// Send to your API
await http.post(
  Uri.parse('https://yourapi.com/api/v1/device-tokens'),
  headers: {'Authorization': 'Bearer $userToken'},
  body: {
    'device_token': token,
    'device_type': Platform.isAndroid ? 'android' : 'ios',
  },
);
```

---

## Summary

✅ **Web API Key**: Already configured correctly  
❌ **Server Key**: Need to create new API key restricted to FCM API  
✅ **VAPID Key**: Already configured correctly  

**Next Step**: Create the Server Key following Step 1-3 above!
