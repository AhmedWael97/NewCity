# Chrome Push Notifications Implementation

## Overview
This document describes the complete implementation of Chrome push notifications using Firebase Cloud Messaging (FCM) for the SENÚ سنو application.

## Architecture

### Components Implemented

1. **Frontend (Browser)**
   - `resources/views/components/firebase-init.blade.php` - Firebase SDK initialization
   - `public/firebase-messaging-sw.js` - Service Worker for background notifications
   - `resources/views/layouts/app.blade.php` - Service Worker registration

2. **Backend Services**
   - `app/Services/NotificationService.php` - Core notification sending service
   - `app/Http/Controllers/Api/DeviceTokenController.php` - Device token management
   - `app/Http/Controllers/Api/NotificationController.php` - Notification tracking
   - `app/Http/Controllers/Admin/AdminAppSettingsController.php` - Admin notification management

3. **Database Models**
   - `app/Models/DeviceToken.php` - Store FCM device tokens
   - `app/Models/PushNotification.php` - Store notification campaigns
   - `app/Models/NotificationLog.php` - Track delivery status

4. **Scheduled Tasks**
   - `app/Console/Commands/SendScheduledNotifications.php` - Process scheduled notifications

## Configuration

### Environment Variables (.env)

```env
# Firebase Configuration
FIREBASE_ENABLED=true
FIREBASE_SERVER_KEY=your_server_key_here
FIREBASE_API_KEY=your_api_key
FIREBASE_AUTH_DOMAIN=senu-66fb2.firebaseapp.com
FIREBASE_PROJECT_ID=senu-66fb2
FIREBASE_STORAGE_BUCKET=senu-66fb2.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=your_sender_id
FIREBASE_APP_ID=your_app_id
FIREBASE_MEASUREMENT_ID=your_measurement_id
FIREBASE_VAPID_KEY=your_vapid_key_here
```

### Getting Firebase Keys

1. **Server Key (Legacy)**:
   - Go to Firebase Console → Project Settings → Cloud Messaging
   - Copy "Server key" under Cloud Messaging API (Legacy)

2. **VAPID Key (Web Push Certificate)**:
   - Go to Firebase Console → Project Settings → Cloud Messaging
   - Under "Web Push certificates" section
   - Click "Generate key pair" if not exists
   - Copy the key

3. **Web Configuration**:
   - Go to Firebase Console → Project Settings → General
   - Scroll to "Your apps" section
   - Select web app or create one
   - Copy all configuration values

## API Endpoints

### Device Token Management

#### Register Device Token
```
POST /api/v1/device-tokens
Authorization: Bearer {token}

Body:
{
  "device_token": "FCM_TOKEN_HERE",
  "device_type": "web|android|ios",
  "device_name": "Chrome Browser",
  "app_version": "1.0.0"
}

Response:
{
  "success": true,
  "message": "Device token registered successfully",
  "data": {
    "id": 1,
    "device_type": "web",
    "is_active": true
  }
}
```

#### Get User's Device Tokens
```
GET /api/v1/device-tokens
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "device_type": "web",
      "device_name": "Chrome Browser",
      "app_version": "1.0.0",
      "is_active": true,
      "last_used_at": "2024-01-15T10:30:00Z",
      "created_at": "2024-01-10T08:00:00Z"
    }
  ]
}
```

#### Remove Device Token
```
DELETE /api/v1/device-tokens
Authorization: Bearer {token}

Body:
{
  "device_token": "FCM_TOKEN_HERE"
}

Response:
{
  "success": true,
  "message": "Device token removed successfully"
}
```

### Notification Tracking

#### Mark Notification as Opened
```
POST /api/v1/notifications/opened
Authorization: Bearer {token}

Body:
{
  "notification_id": 123
}

Response:
{
  "success": true,
  "message": "Notification marked as opened"
}
```

## Admin Panel Usage

### Creating Notifications

1. Navigate to Admin → App Settings → Notifications
2. Click "Create New Notification"
3. Fill in the form:
   - **Title**: Notification title (max 255 chars)
   - **Body**: Notification message (max 1000 chars)
   - **Type**: general, alert, promo, update
   - **Target**: 
     - `all` - All users
     - `users` - Specific user IDs
     - `cities` - Users in specific cities
     - `shop_owners` - Shop owners only
     - `regular_users` - Non-shop owners
   - **Image**: Optional notification image
   - **Action URL**: URL to open when clicked
   - **Schedule**: Optional future send time
4. Choose "Send Now" or "Schedule for Later"

### Notification Targets

#### All Users
```php
[
  'target' => 'all',
  'target_ids' => null
]
```

#### Specific Users
```php
[
  'target' => 'users',
  'target_ids' => [1, 2, 3, 4, 5]
]
```

#### Cities
```php
[
  'target' => 'cities',
  'target_ids' => [1, 2] // City IDs
]
```

#### Shop Owners
```php
[
  'target' => 'shop_owners',
  'target_ids' => null
]
```

#### Regular Users
```php
[
  'target' => 'regular_users',
  'target_ids' => null
]
```

## Programmatic Usage

### Send to Specific Users

```php
use App\Services\NotificationService;

$notificationService = app(NotificationService::class);

$result = $notificationService->sendToUsers(
    [1, 2, 3], // User IDs
    'New Shop Added',
    'A new shop has been added in your city!',
    ['action_url' => '/shops/123']
);
```

### Send to All Users

```php
$result = $notificationService->sendToAll(
    'Important Update',
    'SENÚ app will be under maintenance tonight',
    ['type' => 'alert']
);
```

### Send Using PushNotification Model

```php
use App\Models\PushNotification;

$notification = PushNotification::create([
    'title' => 'Flash Sale!',
    'body' => '50% off on all services today only',
    'type' => 'promo',
    'target' => 'all',
    'action_url' => '/promotions',
    'created_by' => auth()->id(),
    'status' => 'pending',
]);

$result = $notificationService->sendPushNotification($notification);
```

## Scheduled Notifications

### Setup Cron Job

Add to your server's crontab:

```bash
* * * * * cd /path/to/project && php artisan notifications:send-scheduled >> /dev/null 2>&1
```

### Manual Testing

```bash
php artisan notifications:send-scheduled
```

### Schedule in Kernel (Alternative)

Edit `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('notifications:send-scheduled')
             ->everyMinute();
}
```

## Frontend Implementation

### Service Worker (firebase-messaging-sw.js)

The service worker handles:
1. Background message reception
2. Notification display
3. Notification click handling
4. Opened tracking API calls

### Firebase Initialization (firebase-init.blade.php)

Handles:
1. FCM token generation
2. Permission request
3. Token registration with backend
4. Foreground message handling

### Registration (app.blade.php)

Automatically registers the service worker when page loads.

## Testing

### Test Notification Flow

1. **User Registration**:
   - User logs in
   - Firebase SDK requests notification permission
   - Token is saved to database

2. **Admin Sends Notification**:
   - Admin creates notification in dashboard
   - Clicks "Send Now"
   - NotificationService processes sending

3. **User Receives**:
   - Chrome displays notification
   - User clicks notification
   - Opens action URL
   - Tracks opened event

### Manual Test via Command

```bash
# Test service (requires authenticated user)
php artisan tinker

$service = app(\App\Services\NotificationService::class);
$service->sendTestNotification(1); // Admin user ID
```

### Check Logs

```bash
tail -f storage/logs/laravel.log | grep notification
```

## Database Schema

### device_tokens
```sql
- id (bigint, PK)
- user_id (bigint, FK to users)
- device_token (text, unique)
- device_type (enum: web, android, ios)
- device_name (varchar)
- app_version (varchar)
- is_active (boolean)
- last_used_at (timestamp)
- created_at, updated_at
```

### push_notifications
```sql
- id (bigint, PK)
- title (varchar)
- body (text)
- data (json)
- type (enum: general, alert, promo, update)
- target (enum: all, users, cities, shop_owners, regular_users)
- target_ids (json)
- image_url (varchar)
- action_url (varchar)
- scheduled_at (timestamp)
- sent_at (timestamp)
- sent_count (integer)
- success_count (integer)
- failure_count (integer)
- status (enum: pending, sending, sent, failed)
- created_by (bigint, FK to users)
- created_at, updated_at
```

### notification_logs
```sql
- id (bigint, PK)
- push_notification_id (bigint, FK to push_notifications)
- device_token_id (bigint, FK to device_tokens)
- status (enum: sent, failed)
- error_message (text)
- opened_at (timestamp)
- created_at, updated_at
```

## Troubleshooting

### Notifications Not Received

1. **Check Firebase Config**:
   ```bash
   php artisan config:cache
   php artisan cache:clear
   ```

2. **Verify Server Key**:
   - Test with Postman/curl to FCM API
   - Ensure FIREBASE_SERVER_KEY is set

3. **Check Device Tokens**:
   ```sql
   SELECT * FROM device_tokens WHERE is_active = 1;
   ```

4. **Check Browser Console**:
   - F12 → Console tab
   - Look for Firebase errors
   - Verify service worker registration

5. **Test Service Worker**:
   - F12 → Application tab → Service Workers
   - Should show "firebase-messaging-sw.js" as active

### Permission Denied

- User must manually enable notifications in browser settings
- Chrome: chrome://settings/content/notifications
- Check site permissions

### Invalid Token Errors

- Tokens expire or become invalid
- Service automatically deactivates invalid tokens
- User needs to refresh and allow notifications again

## Performance Considerations

### Batch Sending

- FCM allows up to 1000 tokens per request
- Service automatically batches in chunks of 500
- Large campaigns sent efficiently

### Rate Limiting

- FCM has quotas (quota exceeded errors)
- Monitor `notification_logs` for failures
- Implement exponential backoff if needed

### Cleanup

Remove old inactive tokens:

```php
DeviceToken::deactivateOldTokens(90); // 90 days
```

## Security

1. **Server Key Protection**:
   - Never expose server key in frontend
   - Keep in .env file
   - Use environment variables in production

2. **Token Validation**:
   - Only authenticated users can register tokens
   - Tokens associated with user accounts
   - API endpoints protected with Sanctum

3. **CSRF Protection**:
   - Admin forms use CSRF tokens
   - API uses Bearer authentication

## Mobile App Integration

For Flutter/React Native apps:

1. Register device token same way:
   ```dart
   POST /api/v1/device-tokens
   device_type: "android" or "ios"
   ```

2. Handle notifications in app:
   ```dart
   FirebaseMessaging.onMessage.listen((RemoteMessage message) {
     // Show local notification
   });
   ```

3. Track opens:
   ```dart
   POST /api/v1/notifications/opened
   notification_id: message.data['notification_id']
   ```

## Next Steps

1. **Get Real Firebase Keys**:
   - Replace placeholder VAPID_KEY
   - Replace placeholder SERVER_KEY

2. **Test End-to-End**:
   - Login as user
   - Send test notification from admin
   - Verify reception in Chrome

3. **Setup Cron**:
   - Add scheduled command to crontab
   - Test scheduled notification

4. **Monitor Performance**:
   - Check notification_logs table
   - Monitor success/failure rates
   - Adjust batching if needed

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console
3. Verify Firebase configuration
4. Test with manual API calls

---

**Implementation Date**: 2024
**Status**: ✅ Complete and Ready for Testing
**Version**: 1.0.0
