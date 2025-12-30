# Mobile App Settings API - Quick Reference

## Overview
Public API endpoints for mobile applications to fetch configuration, check updates, and maintenance status. **No authentication required** for these endpoints.

---

## 🔓 Public Endpoints

### 1. Get App Settings
**Endpoint:** `GET /api/v1/app-settings`  
**Auth:** Not Required  
**Description:** Fetch all app configuration settings

#### Response Example:
```json
{
  "success": true,
  "data": {
    "app_name": "City Services",
    "app_version": "1.0.0",
    "maintenance_mode": false,
    "maintenance_message": "We are currently performing maintenance. Please check back soon.",
    "force_update": false,
    "min_app_version": "1.0.0",
    "update_message": "A new version is available. Please update to continue.",
    "update_url_ios": "https://apps.apple.com/app/id123456789",
    "update_url_android": "https://play.google.com/store/apps/details?id=com.example.app",
    "support_email": "support@example.com",
    "support_phone": "+1234567890",
    "privacy_policy_url": "https://example.com/privacy",
    "terms_of_service_url": "https://example.com/terms",
    "app_icon_url": "https://example.com/storage/app-settings/icon.png",
    "app_logo_url": "https://example.com/storage/app-settings/logo.png",
    "features": {
      "enable_notifications": true,
      "enable_chat": true,
      "enable_location": true,
      "enable_analytics": true
    }
  }
}
```

#### Use Cases:
- Load app configuration on startup
- Get support contact information
- Check enabled features
- Display app branding (icon, logo)

---

### 2. Check Update
**Endpoint:** `GET /api/v1/app-settings/check-update`  
**Auth:** Not Required  
**Description:** Check if app needs to be updated

#### Query Parameters:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| version | string | Yes | Current app version (e.g., "1.0.0") |
| platform | string | Yes | Platform: `ios` or `android` |

#### Request Example:
```
GET /api/v1/app-settings/check-update?version=1.0.0&platform=android
```

#### Response Example:
```json
{
  "success": true,
  "data": {
    "force_update": false,
    "update_required": false,
    "update_available": true,
    "current_version": "1.0.0",
    "min_version": "1.0.0",
    "latest_version": "1.2.0",
    "update_message": "A new version is available with new features!",
    "update_url": "https://play.google.com/store/apps/details?id=com.example.app"
  }
}
```

#### Response Fields:
- **force_update**: If `true`, user MUST update to continue using the app
- **update_required**: If `true`, current version is below minimum required version
- **update_available**: If `true`, a newer version is available
- **update_url**: Direct link to app store for the specified platform

#### Implementation Logic:
```javascript
// Flutter/Dart Example
Future<void> checkAppUpdate() async {
  final response = await http.get(Uri.parse(
    '$baseUrl/api/v1/app-settings/check-update?version=$appVersion&platform=$platform'
  ));
  
  final data = jsonDecode(response.body)['data'];
  
  if (data['force_update']) {
    // Show blocking dialog - user MUST update
    showForceUpdateDialog(data['update_message'], data['update_url']);
  } else if (data['update_available']) {
    // Show optional update dialog
    showOptionalUpdateDialog(data['update_message'], data['update_url']);
  }
}
```

---

### 3. Maintenance Status
**Endpoint:** `GET /api/v1/app-settings/maintenance-status`  
**Auth:** Not Required  
**Description:** Check if app is in maintenance mode

#### Response Example:
```json
{
  "success": true,
  "data": {
    "maintenance_mode": false,
    "maintenance_message": "We are currently performing maintenance. Please check back soon."
  }
}
```

#### Implementation Logic:
```javascript
// Flutter/Dart Example
Future<void> checkMaintenanceStatus() async {
  final response = await http.get(Uri.parse(
    '$baseUrl/api/v1/app-settings/maintenance-status'
  ));
  
  final data = jsonDecode(response.body)['data'];
  
  if (data['maintenance_mode']) {
    // Show maintenance screen
    showMaintenanceScreen(data['maintenance_message']);
  }
}
```

---

## 🎯 Mobile App Integration Flow

### App Startup Sequence:
```
1. App Launches
   ↓
2. GET /api/v1/app-settings/maintenance-status
   ↓
3. If maintenance_mode = true → Show maintenance screen
   ↓
4. GET /api/v1/app-settings/check-update
   ↓
5. If force_update = true → Block app, show update dialog
   ↓
6. If update_available = true → Show optional update dialog
   ↓
7. GET /api/v1/app-settings (fetch full config)
   ↓
8. Apply settings & continue to main app
```

### Recommended Caching Strategy:
```javascript
// Cache settings for 1 hour, check updates on every launch
{
  "app_settings": {
    "cache_duration": 3600,      // 1 hour
    "refresh_on_launch": false
  },
  "maintenance_status": {
    "cache_duration": 0,         // No cache
    "refresh_on_launch": true
  },
  "update_check": {
    "cache_duration": 0,         // No cache
    "refresh_on_launch": true
  }
}
```

---

## 🔐 Admin Control Endpoints

Admins can control the mobile app behavior from the dashboard using these endpoints (requires authentication):

### Admin Endpoints:
- `GET /api/v1/admin/app-settings` - View all settings
- `PUT /api/v1/admin/app-settings` - Update settings
- `POST /api/v1/admin/app-settings/upload-icon` - Upload app icon
- `POST /api/v1/admin/app-settings/upload-logo` - Upload app logo

### Controllable Settings:
1. **Maintenance Mode** - Enable/disable app access
2. **Force Update** - Require users to update
3. **Feature Flags** - Enable/disable features remotely
4. **Support Contacts** - Update support email/phone
5. **Update URLs** - Change app store links
6. **Branding** - Update app icon/logo

---

## 📱 Flutter/React Native Example

### Complete Integration Example (Flutter):

```dart
class AppSettingsService {
  final String baseUrl = 'https://your-domain.com';
  
  // Fetch full app settings
  Future<AppSettings> getSettings() async {
    final response = await http.get(
      Uri.parse('$baseUrl/api/v1/app-settings')
    );
    
    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      return AppSettings.fromJson(json['data']);
    }
    throw Exception('Failed to load settings');
  }
  
  // Check for updates
  Future<UpdateInfo> checkUpdate(String version, String platform) async {
    final response = await http.get(
      Uri.parse('$baseUrl/api/v1/app-settings/check-update?version=$version&platform=$platform')
    );
    
    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      return UpdateInfo.fromJson(json['data']);
    }
    throw Exception('Failed to check update');
  }
  
  // Check maintenance
  Future<MaintenanceInfo> checkMaintenance() async {
    final response = await http.get(
      Uri.parse('$baseUrl/api/v1/app-settings/maintenance-status')
    );
    
    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      return MaintenanceInfo.fromJson(json['data']);
    }
    throw Exception('Failed to check maintenance');
  }
}

// Usage in main.dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  final appSettings = AppSettingsService();
  
  // Check maintenance first
  final maintenance = await appSettings.checkMaintenance();
  if (maintenance.maintenanceMode) {
    runApp(MaintenanceApp(message: maintenance.message));
    return;
  }
  
  // Check for updates
  final updateInfo = await appSettings.checkUpdate('1.0.0', 'android');
  if (updateInfo.forceUpdate) {
    runApp(ForceUpdateApp(updateInfo: updateInfo));
    return;
  }
  
  // Load settings
  final settings = await appSettings.getSettings();
  
  runApp(MyApp(settings: settings));
}
```

---

## 🎨 UI/UX Recommendations

### Force Update Screen:
- **Blocking dialog** that cannot be dismissed
- Clear message: "Update Required"
- Prominent "Update Now" button
- No "Cancel" or "Later" option
- Display update benefits/changes

### Optional Update Screen:
- **Non-blocking dialog** that can be dismissed
- Message: "New Version Available"
- Two buttons: "Update Now" and "Later"
- Don't show again for 24 hours if "Later" selected

### Maintenance Screen:
- **Full-screen blocking UI**
- Clear message from admin
- Company logo/branding
- Expected return time (if available)
- Support contact information

---

## 🧪 Testing

### Test Scenarios:

1. **Normal Operation**
   - maintenance_mode: false
   - force_update: false
   - Expected: App works normally

2. **Maintenance Mode**
   - maintenance_mode: true
   - Expected: Show maintenance screen

3. **Force Update**
   - force_update: true
   - update_required: true
   - Expected: Block app, show update dialog

4. **Optional Update**
   - force_update: false
   - update_available: true
   - Expected: Show dismissible update dialog

5. **Feature Flags**
   - enable_notifications: false
   - Expected: Hide notification settings

### Test Commands:
```bash
# Test maintenance status
curl https://your-domain.com/api/v1/app-settings/maintenance-status

# Test update check
curl "https://your-domain.com/api/v1/app-settings/check-update?version=1.0.0&platform=android"

# Test get settings
curl https://your-domain.com/api/v1/app-settings
```

---

## 📊 Monitoring & Analytics

Recommended tracking:
- Track update check calls
- Monitor maintenance mode activations
- Log force update blocks
- Track feature flag usage
- Monitor API response times

---

## 🔄 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2024-11-29 | Initial public API release |

---

## 📞 Support

For integration help:
- Email: support@example.com
- Documentation: https://your-domain.com/api/documentation
- Swagger: https://your-domain.com/api/documentation
