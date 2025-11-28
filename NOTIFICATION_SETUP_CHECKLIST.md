# Chrome Notification System - Setup Checklist

## ✅ Completed Implementation

### Backend Files Created/Modified
- ✅ `app/Services/NotificationService.php` - Core notification sending service
- ✅ `app/Http/Controllers/Api/DeviceTokenController.php` - Device token API
- ✅ `app/Http/Controllers/Api/NotificationController.php` - Notification tracking API
- ✅ `app/Http/Controllers/Admin/AdminAppSettingsController.php` - Updated to use NotificationService
- ✅ `app/Console/Commands/SendScheduledNotifications.php` - Scheduled notifications command
- ✅ `routes/api.php` - Added device token and notification routes
- ✅ `public/firebase-messaging-sw.js` - Service worker for background notifications
- ✅ `resources/views/layouts/app.blade.php` - Added service worker registration

### Database
- ✅ `device_tokens` table - Already exists
- ✅ `push_notifications` table - Already exists  
- ✅ `notification_logs` table - Already exists
- ✅ Models: DeviceToken, PushNotification, NotificationLog - Already exist

### Configuration
- ✅ `config/services.php` - Firebase config already set up
- ✅ `.env` file has Firebase keys (needs real values)

## 🔧 Setup Required

### 1. Get Firebase Keys (HIGH PRIORITY)

#### Server Key (Legacy)
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select project: **senu-66fb2**
3. Go to **Project Settings** (gear icon) → **Cloud Messaging** tab
4. Find "Cloud Messaging API (Legacy)" section
5. Copy the **Server key**
6. Update `.env`:
   ```env
   FIREBASE_SERVER_KEY=your_actual_server_key_here
   ```

#### VAPID Key (Web Push Certificate)
1. Same Firebase Console → **Cloud Messaging** tab
2. Scroll to "Web Push certificates" section
3. If no key exists, click **"Generate key pair"**
4. Copy the key value
5. Update `.env`:
   ```env
   FIREBASE_VAPID_KEY=your_actual_vapid_key_here
   ```

### 2. Clear Laravel Cache
```bash
php artisan config:cache
php artisan cache:clear
php artisan route:cache
```

### 3. Test Service Worker Access
Open browser and visit:
```
https://your-domain.com/firebase-messaging-sw.js
```
Should show JavaScript file, not 404 error.

### 4. Setup Scheduled Notifications (Optional)

#### Option A: Cron Job
Add to server crontab (`crontab -e`):
```bash
* * * * * cd /path/to/City && php artisan notifications:send-scheduled >> /dev/null 2>&1
```

#### Option B: Laravel Scheduler
Edit `app/Console/Kernel.php`, add to `schedule()` method:
```php
$schedule->command('notifications:send-scheduled')->everyMinute();
```

Then setup cron to run Laravel scheduler:
```bash
* * * * * cd /path/to/City && php artisan schedule:run >> /dev/null 2>&1
```

## 🧪 Testing Procedure

### Test 1: Device Token Registration

1. **Login to Website**
   - Navigate to any page as authenticated user
   - Browser should request notification permission
   - Click "Allow"

2. **Check Console (F12)**
   - Should see: "FCM Token: ..." in console
   - Should see: "Token saved successfully"

3. **Verify Database**
   ```sql
   SELECT * FROM device_tokens ORDER BY created_at DESC LIMIT 5;
   ```
   Should show new token entry with device_type='web'

### Test 2: Send Test Notification

#### Via Artisan Tinker
```bash
php artisan tinker
```

```php
$service = app(\App\Services\NotificationService::class);
$result = $service->sendToAll(
    'Test Notification',
    'This is a test from SENÚ سنو',
    ['type' => 'test', 'action_url' => '/']
);
print_r($result);
```

Expected output:
```
Array
(
    [success] => 1
    [sent] => 1
    [success_count] => 1
    [failure_count] => 0
)
```

#### Via Admin Panel
1. Login as admin: `/admin/login`
2. Go to: **App Settings** → **Notifications**
3. Click **"Create New Notification"**
4. Fill form:
   - Title: "Test Notification"
   - Body: "Testing Chrome notifications"
   - Type: "general"
   - Target: "all"
5. Check **"Send Now"**
6. Click **"Create"**

### Test 3: Verify Reception

1. **Browser Tab Active (Foreground)**
   - Should see console message: "Notification received:"
   - Browser notification appears

2. **Browser Tab Inactive (Background)**
   - Chrome notification appears from system tray
   - Click notification → opens action URL
   - Check browser console: "Notification clicked"

3. **Check Logs**
   ```sql
   SELECT * FROM notification_logs ORDER BY created_at DESC LIMIT 10;
   ```
   - Should show status='sent'
   - After clicking: opened_at should be filled

### Test 4: Scheduled Notification

```bash
php artisan tinker
```

```php
$notification = \App\Models\PushNotification::create([
    'title' => 'Scheduled Test',
    'body' => 'This was scheduled for 2 minutes from now',
    'type' => 'general',
    'target' => 'all',
    'scheduled_at' => now()->addMinutes(2),
    'status' => 'pending',
    'created_by' => 1
]);
```

Wait 2+ minutes, then run:
```bash
php artisan notifications:send-scheduled
```

Should output:
```
Checking for scheduled notifications...
Found 1 scheduled notification(s) to send.
Sending notification: Scheduled Test
✓ Sent to X device(s)
Scheduled notifications processing complete.
```

## 🔍 Troubleshooting

### Issue: No permission prompt

**Solution:**
- Clear browser cache and cookies
- Check browser settings: `chrome://settings/content/notifications`
- Ensure site is not blocked
- Try incognito mode

### Issue: "Messaging: This browser doesn't support the API's"

**Solution:**
- Service worker must be on HTTPS (or localhost)
- Check if service worker registered: F12 → Application → Service Workers

### Issue: Token not saving

**Check:**
1. User is authenticated
2. Network tab shows POST to `/api/v1/device-tokens`
3. Response is 200 OK
4. Check Laravel logs: `tail -f storage/logs/laravel.log`

### Issue: Notifications not sending

**Check:**
1. FIREBASE_SERVER_KEY is set in `.env`
2. Run: `php artisan config:cache`
3. Check logs for FCM errors
4. Verify device_tokens table has active tokens:
   ```sql
   SELECT COUNT(*) FROM device_tokens WHERE is_active = 1;
   ```

### Issue: Service worker 404

**Solution:**
- Ensure `public/firebase-messaging-sw.js` exists
- Check file permissions: `chmod 644 public/firebase-messaging-sw.js`
- Clear browser cache
- Try direct URL: `https://domain.com/firebase-messaging-sw.js`

## 📊 Monitoring

### Check Notification Statistics

#### Admin Dashboard
- Visit: `/admin/app-settings/notifications`
- View stats: Total, Pending, Sent, Failed

#### Database Queries

**Success Rate:**
```sql
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
    ROUND(SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as success_rate
FROM push_notifications;
```

**Open Rate:**
```sql
SELECT 
    COUNT(*) as total_sent,
    SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened,
    ROUND(SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as open_rate
FROM notification_logs
WHERE status = 'sent';
```

**Device Type Distribution:**
```sql
SELECT 
    device_type,
    COUNT(*) as count,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
FROM device_tokens
GROUP BY device_type;
```

## 🚀 Next Steps After Testing

1. **Configure Real Keys**: Replace placeholder Firebase keys
2. **Test on Production**: Deploy to live server with HTTPS
3. **Setup Monitoring**: Track notification delivery rates
4. **Setup Cron**: Enable scheduled notifications
5. **Train Admin Users**: Show how to create and send notifications
6. **Monitor Logs**: Watch for FCM quota issues or errors

## 📝 API Documentation

Full API documentation available in:
- **CHROME_NOTIFICATIONS_GUIDE.md** - Complete implementation guide
- API endpoints documented with examples
- Programmatic usage examples

## 🆘 Support

If issues persist:
1. Check `storage/logs/laravel.log`
2. Check browser console (F12)
3. Verify Firebase configuration
4. Test with API client (Postman)

---

**Status**: ✅ Ready for Testing
**Last Updated**: 2024
**Version**: 1.0.0
