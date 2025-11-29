# App Settings API Documentation

## Overview
Complete REST API for managing mobile application settings, push notifications, and device management from the admin dashboard. This allows full control over the mobile app behavior remotely.

## Implementation Date
November 29, 2025

---

## Files Created

### 1. Controller
**Location:** `app/Http/Controllers/Api/Admin/AppSettingsController.php`

Implements comprehensive app management:
- App settings CRUD operations
- Push notification management
- Device token management
- Statistics and analytics
- File uploads (icon/logo)

### 2. Resources
**Location:** `app/Http/Resources/`

- `AppSettingResource.php` - App settings transformation
- `PushNotificationResource.php` - Notification transformation
- `DeviceTokenResource.php` - Device token transformation

### 3. Routes
**Location:** `routes/api.php`

All routes require admin authentication and are under `/api/v1/admin/app-settings` prefix.

---

## API Endpoints

### Base URL
```
/api/v1/admin/app-settings
```

**Authentication:** Required (Admin/Super Admin only)
**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

---

## 1. App Settings Management

### Get All Settings
```
GET /api/v1/admin/app-settings
```

**Response:**
```json
{
  "success": true,
  "data": {
    "settings": {
      "app_name": "City App",
      "app_icon_url": "https://domain.com/storage/app-settings/icon.png",
      "app_logo_url": "https://domain.com/storage/app-settings/logo.png",
      "maintenance_mode": false,
      "maintenance_message": "App is under maintenance",
      "force_update": false,
      "min_app_version": "1.0.0",
      "latest_app_version": "1.5.0",
      "update_message": "Please update to continue",
      "android_app_url": "https://play.google.com/...",
      "ios_app_url": "https://apps.apple.com/...",
      "api_status": "active",
      "firebase_enabled": true
    },
    "stats": {
      "total_devices": 1250,
      "active_devices": 980,
      "ios_devices": 450,
      "android_devices": 530,
      "total_notifications": 85,
      "pending_notifications": 3,
      "sent_notifications": 82
    }
  }
}
```

---

### Update Settings
```
PUT /api/v1/admin/app-settings
```

**Request Body:**
```json
{
  "app_name": "My City App",
  "maintenance_mode": true,
  "maintenance_message": "We are updating the app. Please check back in 30 minutes.",
  "force_update": false,
  "min_app_version": "1.0.0",
  "latest_app_version": "1.6.0",
  "update_message": "New version available with exciting features!",
  "android_app_url": "https://play.google.com/store/apps/details?id=com.myapp",
  "ios_app_url": "https://apps.apple.com/app/id123456789",
  "api_status": "active"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Settings updated successfully",
  "data": {
    "app_name": "My City App",
    "maintenance_mode": true,
    ...
  }
}
```

---

### Upload App Icon
```
POST /api/v1/admin/app-settings/upload-icon
Content-Type: multipart/form-data
```

**Request Body:**
```
icon: [file] (PNG, JPG, JPEG, max 2MB)
```

**Response:**
```json
{
  "success": true,
  "message": "App icon uploaded successfully",
  "data": {
    "icon_url": "https://domain.com/storage/app-settings/app_icon_1701280800.png"
  }
}
```

---

### Upload App Logo
```
POST /api/v1/admin/app-settings/upload-logo
Content-Type: multipart/form-data
```

**Request Body:**
```
logo: [file] (PNG, JPG, JPEG, max 2MB)
```

**Response:**
```json
{
  "success": true,
  "message": "App logo uploaded successfully",
  "data": {
    "logo_url": "https://domain.com/storage/app-settings/app_logo_1701280800.png"
  }
}
```

---

### Get Statistics
```
GET /api/v1/admin/app-settings/statistics
```

**Response:**
```json
{
  "success": true,
  "data": {
    "devices": {
      "total": 1250,
      "active": 980,
      "ios": 450,
      "android": 530,
      "inactive": 270,
      "today": 25,
      "this_week": 120,
      "this_month": 380
    },
    "notifications": {
      "total": 85,
      "pending": 3,
      "sent": 78,
      "failed": 4,
      "scheduled": 2,
      "today": 5
    },
    "app_status": {
      "maintenance_mode": false,
      "force_update": false,
      "api_status": "active",
      "firebase_enabled": true
    }
  }
}
```

---

## 2. Push Notifications Management

### Get Notifications List
```
GET /api/v1/admin/app-settings/notifications
```

**Query Parameters:**
- `status` (string) - Filter by status: `pending`, `sent`, `failed`
- `type` (string) - Filter by type: `general`, `alert`, `promo`, `update`
- `page` (integer) - Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "data": {
    "notifications": [
      {
        "id": 1,
        "title": "New Feature Available!",
        "body": "Check out our latest feature in the app.",
        "type": "update",
        "target": "all",
        "target_ids": null,
        "image_url": null,
        "action_url": "/features/new",
        "status": "sent",
        "scheduled_at": null,
        "sent_at": "2025-11-29T10:30:00.000000Z",
        "total_sent": 980,
        "total_opened": 450,
        "total_failed": 5,
        "creator": {
          "id": 1,
          "name": "Admin User",
          "email": "admin@example.com"
        },
        "created_at": "2025-11-29T10:00:00.000000Z",
        "updated_at": "2025-11-29T10:30:00.000000Z"
      }
    ],
    "stats": {
      "total": 85,
      "pending": 3,
      "sent": 78,
      "failed": 4
    },
    "pagination": {
      "total": 85,
      "current_page": 1,
      "last_page": 5,
      "per_page": 20
    }
  }
}
```

---

### Create and Send Notification
```
POST /api/v1/admin/app-settings/notifications
```

**Request Body:**
```json
{
  "title": "Black Friday Sale!",
  "body": "Get 50% off on all services. Limited time offer!",
  "type": "promo",
  "target": "all",
  "action_url": "/promotions",
  "send_now": true
}
```

**Target Options:**
- `all` - All users
- `users` - Authenticated users only
- `cities` - Specific cities (requires `target_ids`)
- `shop_owners` - Shop owners only
- `regular_users` - Regular users (non-shop owners)

**Request Body (Scheduled):**
```json
{
  "title": "Reminder",
  "body": "Don't forget to check new shops in your city!",
  "type": "general",
  "target": "cities",
  "target_ids": [1, 2, 3],
  "scheduled_at": "2025-11-30T09:00:00Z",
  "send_now": false
}
```

**Response:**
```json
{
  "success": true,
  "message": "Notification sent to 980 devices",
  "data": {
    "notification": {
      "id": 86,
      "title": "Black Friday Sale!",
      "body": "Get 50% off on all services. Limited time offer!",
      "type": "promo",
      "status": "sent",
      ...
    },
    "send_result": {
      "success_count": 980,
      "failure_count": 5
    }
  }
}
```

---

### Send Pending Notification
```
POST /api/v1/admin/app-settings/notifications/{notification_id}/send
```

**Response:**
```json
{
  "success": true,
  "message": "Notification sent to 980 devices",
  "data": {
    "send_result": {
      "success_count": 980,
      "failure_count": 5
    }
  }
}
```

---

### Delete Notification
```
DELETE /api/v1/admin/app-settings/notifications/{notification_id}
```

**Response:**
```json
{
  "success": true,
  "message": "Notification deleted successfully"
}
```

---

### Send Test Notification
```
POST /api/v1/admin/app-settings/test-notification
```

**Request Body:**
```json
{
  "title": "Test Notification",
  "body": "This is a test notification from admin panel"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Test notification sent successfully"
}
```

---

## 3. Device Management

### Get Devices List
```
GET /api/v1/admin/app-settings/devices
```

**Query Parameters:**
- `device_type` (string) - Filter by type: `ios`, `android`
- `status` (string) - Filter by status: `active`, `inactive`
- `page` (integer) - Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "data": {
    "devices": [
      {
        "id": 1,
        "device_token": "dT3kFn8pQ5mR7sV2x...",
        "device_type": "ios",
        "device_name": "iPhone 14 Pro",
        "app_version": "1.5.0",
        "is_active": true,
        "user": {
          "id": 123,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "last_used_at": "2025-11-29T08:30:00.000000Z",
        "created_at": "2025-11-20T10:00:00.000000Z",
        "updated_at": "2025-11-29T08:30:00.000000Z"
      }
    ],
    "pagination": {
      "total": 1250,
      "current_page": 1,
      "last_page": 25,
      "per_page": 50
    }
  }
}
```

---

## Use Cases

### 1. Enable Maintenance Mode
**Scenario:** Need to perform server maintenance

```bash
PUT /api/v1/admin/app-settings
{
  "maintenance_mode": true,
  "maintenance_message": "We are upgrading our servers. The app will be back online at 10:00 AM."
}
```

**Result:** Mobile app will show maintenance screen to all users

---

### 2. Force Update for Old Versions
**Scenario:** Critical bug fix in version 1.6.0

```bash
PUT /api/v1/admin/app-settings
{
  "force_update": true,
  "min_app_version": "1.6.0",
  "update_message": "Critical security update available. Please update immediately."
}
```

**Result:** Users with version < 1.6.0 cannot access the app until they update

---

### 3. Send Promotional Notification
**Scenario:** Black Friday sale announcement

```bash
POST /api/v1/admin/app-settings/notifications
{
  "title": "🔥 Black Friday Sale!",
  "body": "Get 50% off on all featured shops. Sale ends tonight!",
  "type": "promo",
  "target": "all",
  "action_url": "/promotions/black-friday",
  "send_now": true
}
```

**Result:** Push notification sent to all active devices immediately

---

### 4. Schedule City-Specific Announcement
**Scenario:** New shops opening in specific cities tomorrow

```bash
POST /api/v1/admin/app-settings/notifications
{
  "title": "New Shops Opening Tomorrow!",
  "body": "10 new amazing shops are opening in your city. Be the first to explore!",
  "type": "general",
  "target": "cities",
  "target_ids": [1, 5, 8],
  "scheduled_at": "2025-11-30T09:00:00Z",
  "send_now": false
}
```

**Result:** Notification scheduled to be sent to users in cities 1, 5, and 8 tomorrow at 9 AM

---

## Settings Reference

### App Status Values

**`maintenance_mode`** (boolean)
- `true` - App shows maintenance screen, users cannot access
- `false` - App functions normally

**`force_update`** (boolean)
- `true` - Users must update to minimum version to access app
- `false` - Updates are optional

**`api_status`** (string)
- `active` - All API endpoints working normally
- `limited` - Some features may be restricted
- `disabled` - API access disabled (emergency only)

### Notification Types
- `general` - General announcements
- `alert` - Important alerts
- `promo` - Promotional messages
- `update` - App update notifications

### Target Types
- `all` - All registered devices
- `users` - Only authenticated users
- `cities` - Users in specific cities (requires target_ids)
- `shop_owners` - Shop owners only
- `regular_users` - Non-shop owner users

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 400 Bad Request
```json
{
  "success": false,
  "message": "Only pending notifications can be sent"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "body": ["The body field is required."]
  }
}
```

---

## Integration Examples

### JavaScript/React Admin Dashboard
```javascript
// Get all settings
const response = await fetch('/api/v1/admin/app-settings', {
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Content-Type': 'application/json'
  }
});
const data = await response.json();

// Enable maintenance mode
await fetch('/api/v1/admin/app-settings', {
  method: 'PUT',
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    maintenance_mode: true,
    maintenance_message: 'Scheduled maintenance in progress'
  })
});

// Send notification
await fetch('/api/v1/admin/app-settings/notifications', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    title: 'New Feature!',
    body: 'Check out our new search feature',
    type: 'update',
    target: 'all',
    send_now: true
  })
});
```

### Flutter Admin App
```dart
// Get statistics
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/admin/app-settings/statistics'),
  headers: {
    'Authorization': 'Bearer $adminToken',
    'Content-Type': 'application/json'
  }
);
final stats = jsonDecode(response.body)['data'];

// Send test notification
await http.post(
  Uri.parse('$baseUrl/api/v1/admin/app-settings/test-notification'),
  headers: {
    'Authorization': 'Bearer $adminToken',
    'Content-Type': 'application/json'
  },
  body: jsonEncode({
    'title': 'Test',
    'body': 'Testing push notifications'
  })
);
```

---

## Security Considerations

1. **Admin Authentication Required** - All endpoints require admin/super_admin role
2. **Device Token Truncation** - Device tokens are truncated in API responses for security
3. **Rate Limiting** - Consider implementing rate limits for notification sending
4. **Image Upload Validation** - Strict validation on icon/logo uploads
5. **Scheduled Notifications** - Cannot schedule notifications in the past

---

## Best Practices

1. **Test Before Production**
   - Use test notification endpoint before mass notifications
   - Test maintenance mode during off-peak hours

2. **Notification Content**
   - Keep titles under 50 characters
   - Keep body under 150 characters for better display
   - Use emojis sparingly

3. **Version Management**
   - Always test new versions before forcing updates
   - Provide clear update messages
   - Give users reasonable time to update (don't force immediately)

4. **Maintenance Mode**
   - Schedule during low-traffic periods
   - Provide estimated downtime in message
   - Send notification before enabling maintenance mode

---

## Performance Tips

1. **Caching** - Settings are cached for 1 hour automatically
2. **Batch Operations** - Use scheduled notifications for large batches
3. **Device Cleanup** - Periodically remove inactive devices
4. **Statistics** - Statistics queries are optimized with indexes

---

## Related Documentation
- Mobile App Controller: `app/Http/Controllers/Api/MobileAppController.php`
- App Settings Model: `app/Models/AppSetting.php`
- Admin Web Interface: `/admin/app-settings`
- Firebase Integration: `app/Services/FirebaseNotificationService.php`

---

## Support
For issues or questions:
- API Documentation: `/api/documentation`
- Admin Dashboard: `/admin/app-settings`
- News API Documentation: `NEWS_API_DOCUMENTATION.md`

---

**Implementation Status:** ✅ Complete and Ready for Production
**Version:** 1.0
**Last Updated:** November 29, 2025
