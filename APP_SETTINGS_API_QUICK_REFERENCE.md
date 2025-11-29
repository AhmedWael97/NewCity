# App Settings API - Quick Reference

## 🎛️ Admin Control Panel API

Complete API for controlling mobile app from dashboard.

---

## 📋 Base Information

**Base URL:** `/api/v1/admin/app-settings`  
**Authentication:** Required (Admin/Super Admin only)  
**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

---

## 🚀 Quick Endpoints

### Settings Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Get all app settings + stats |
| PUT | `/` | Update app settings |
| POST | `/upload-icon` | Upload app icon |
| POST | `/upload-logo` | Upload app logo |
| GET | `/statistics` | Get detailed statistics |

### Push Notifications

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/notifications` | List all notifications |
| POST | `/notifications` | Create & send notification |
| POST | `/notifications/{id}/send` | Send pending notification |
| DELETE | `/notifications/{id}` | Delete notification |
| POST | `/test-notification` | Send test notification |

### Device Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/devices` | List registered devices |

---

## 💡 Common Use Cases

### 1. Enable Maintenance Mode
```bash
PUT /api/v1/admin/app-settings
{
  "maintenance_mode": true,
  "maintenance_message": "Under maintenance. Back in 30 minutes."
}
```

### 2. Force App Update
```bash
PUT /api/v1/admin/app-settings
{
  "force_update": true,
  "min_app_version": "1.6.0",
  "update_message": "Critical update required"
}
```

### 3. Send Push Notification
```bash
POST /api/v1/admin/app-settings/notifications
{
  "title": "New Feature!",
  "body": "Check out our latest feature",
  "type": "update",
  "target": "all",
  "send_now": true
}
```

### 4. Get App Statistics
```bash
GET /api/v1/admin/app-settings/statistics
```

---

## 📊 Key Settings

### App Status
- `maintenance_mode` (bool) - Show maintenance screen
- `force_update` (bool) - Require minimum version
- `api_status` (string) - `active`, `limited`, `disabled`

### Version Control
- `min_app_version` (string) - Minimum required version
- `latest_app_version` (string) - Latest available version
- `android_app_url` (string) - Play Store URL
- `ios_app_url` (string) - App Store URL

### Branding
- `app_name` (string) - App display name
- `app_icon_url` (string) - Icon URL
- `app_logo_url` (string) - Logo URL

---

## 📱 Notification Types

- **general** - General announcements
- **alert** - Important alerts
- **promo** - Promotional messages  
- **update** - App updates

### Target Options
- **all** - All users
- **users** - Authenticated users only
- **cities** - Specific cities (requires target_ids)
- **shop_owners** - Shop owners
- **regular_users** - Regular users

---

## 📈 Statistics Response
```json
{
  "devices": {
    "total": 1250,
    "active": 980,
    "ios": 450,
    "android": 530,
    "today": 25
  },
  "notifications": {
    "total": 85,
    "pending": 3,
    "sent": 78,
    "failed": 4
  },
  "app_status": {
    "maintenance_mode": false,
    "force_update": false,
    "api_status": "active"
  }
}
```

---

## 🎯 Quick Actions

### Disable App Immediately
```json
PUT /api/v1/admin/app-settings
{
  "maintenance_mode": true,
  "maintenance_message": "Emergency maintenance"
}
```

### Send Emergency Alert
```json
POST /api/v1/admin/app-settings/notifications
{
  "title": "⚠️ Important Alert",
  "body": "Please check your notifications",
  "type": "alert",
  "target": "all",
  "send_now": true
}
```

### Update App Name & Branding
```json
PUT /api/v1/admin/app-settings
{
  "app_name": "My New App Name"
}
```
Then upload new icon:
```bash
POST /api/v1/admin/app-settings/upload-icon
[multipart/form-data with icon file]
```

---

## ✅ Response Format

### Success
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error
```json
{
  "success": false,
  "message": "Error description"
}
```

---

## 🔐 Security

- ✅ Admin authentication required
- ✅ Role-based access control
- ✅ Device tokens truncated in responses
- ✅ File upload validation
- ✅ Input sanitization

---

## 📦 Files Created

1. **Controller**: `app/Http/Controllers/Api/Admin/AppSettingsController.php`
2. **Resources**:
   - `app/Http/Resources/AppSettingResource.php`
   - `app/Http/Resources/PushNotificationResource.php`
   - `app/Http/Resources/DeviceTokenResource.php`
3. **Routes**: `routes/api.php` (admin app-settings group)
4. **Documentation**: 
   - `APP_SETTINGS_API_DOCUMENTATION.md` (Full docs)
   - `APP_SETTINGS_API_QUICK_REFERENCE.md` (This file)

---

## 🌐 Integration Example

```javascript
// React/Vue/Angular Dashboard
const adminAPI = {
  async getSettings() {
    const res = await fetch('/api/v1/admin/app-settings', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    return res.json();
  },
  
  async updateSettings(data) {
    const res = await fetch('/api/v1/admin/app-settings', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });
    return res.json();
  },
  
  async sendNotification(notification) {
    const res = await fetch('/api/v1/admin/app-settings/notifications', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(notification)
    });
    return res.json();
  }
};
```

---

## 📖 Full Documentation

For complete API documentation with all parameters, examples, and details:
**See:** `APP_SETTINGS_API_DOCUMENTATION.md`

---

**Status:** ✅ Complete and Production Ready  
**Version:** 1.0  
**Date:** November 29, 2025
