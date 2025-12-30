# API Tracking Quick Reference

## ✅ System is Active!

The API tracking system is now installed and automatically tracking all API requests.

## 📍 Quick Access

### View Analytics Dashboard
```
http://your-domain.com/admin/api-analytics
```

### Time Filters
```
/admin/api-analytics?days=1    # Today
/admin/api-analytics?days=7    # Last 7 days (default)
/admin/api-analytics?days=30   # Last 30 days
/admin/api-analytics?days=90   # Last 90 days
```

### Export Data
```
/admin/api-analytics/export?days=30
```

### Real-Time Monitoring (JSON)
```
/admin/api-analytics/recent?limit=100
```

## 📊 What's Being Tracked

Every API request captures:
- ✓ Endpoint path and HTTP method
- ✓ Request parameters and body data
- ✓ Response status and time
- ✓ User ID (if authenticated)
- ✓ Device type (mobile/tablet/desktop)
- ✓ IP address and user agent
- ✓ Action type (search, view, create, etc.)
- ✓ Resource type (shop, city, product, etc.)
- ✓ Error messages (if failed)

## 🔍 Key Metrics Available

### Overview
- Total API requests
- Success rate percentage
- Failed requests count
- Unique users
- Average response time

### Endpoint Analytics
- Most used endpoints
- Slowest performing endpoints
- Most failed endpoints
- Requests per endpoint

### User Behavior
- Requests by action type (search, view, create, update, delete)
- Requests by resource type (shops, cities, products, etc.)
- Top users by API usage
- Device breakdown (mobile vs desktop vs tablet)

### Performance
- Response time trends
- Requests over time (daily)
- HTTP method distribution
- Status code distribution

### Errors
- Most common error messages
- Failed endpoint list
- Error rate trends

## 💡 Common Queries

### Check Today's API Usage
```php
use App\Models\ApiRequest;

ApiRequest::today()->count();
```

### Find Slow Endpoints
```php
ApiRequest::lastDays(7)
    ->select('endpoint', DB::raw('AVG(response_time) as avg_time'))
    ->groupBy('endpoint')
    ->orderByDesc('avg_time')
    ->limit(10)
    ->get();
```

### Monitor Failed Requests
```php
ApiRequest::failed()
    ->lastDays(1)
    ->with('user')
    ->get();
```

### Track Specific Endpoint
```php
ApiRequest::byEndpoint('shops')
    ->lastDays(7)
    ->count();
```

### Get User Activity
```php
ApiRequest::byUser($userId)
    ->orderByDesc('created_at')
    ->limit(50)
    ->get();
```

## 🚨 Monitoring & Alerts

### Set Up Daily Cleanup (Optional)
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Delete API requests older than 90 days
    $schedule->call(function () {
        \App\Models\ApiRequest::where('created_at', '<', now()->subDays(90))->delete();
    })->daily();
}
```

### Monitor High Error Rates
```php
$errorCount = ApiRequest::today()->failed()->count();

if ($errorCount > 100) {
    // Send alert to admin
}
```

## 🔧 How It Works

1. **API request arrives** → `/api/v1/shops/123`
2. **Middleware intercepts** → TrackApiRequest starts timer
3. **Request processed** → Your controller runs normally
4. **Response generated** → Status 200, data returned
5. **Tracking happens** → Details saved to database
6. **Response sent** → User gets response (no delay!)

## 📈 Action Types Detected

| Action | Description | Example Endpoint |
|--------|-------------|------------------|
| `list` | Getting multiple resources | `GET /api/v1/shops` |
| `view` | Getting single resource | `GET /api/v1/shops/123` |
| `search` | Searching resources | `GET /api/v1/shops/search` |
| `create` | Creating resource | `POST /api/v1/shops` |
| `update` | Updating resource | `PUT /api/v1/shops/123` |
| `delete` | Deleting resource | `DELETE /api/v1/shops/123` |
| `favorite` | Favoriting | `POST /api/v1/shops/123/favorite` |
| `review` | Rating/reviewing | `POST /api/v1/shops/123/rating` |
| `contact` | Contact action | `POST /api/v1/shops/123/contact` |
| `authentication` | Login/register | `POST /api/v1/auth/login` |

## 🎯 Resource Types Detected

- `shop` - Shop endpoints
- `city` - City endpoints
- `category` - Category endpoints
- `product` - Product endpoints
- `service` - Service endpoints
- `marketplace` - Marketplace endpoints
- `news` - News endpoints
- `forum` - Forum endpoints
- `user` - User endpoints

## 🔒 Security Features

### Automatic Data Sanitization
These fields are automatically redacted from logs:
- passwords
- tokens
- API keys
- credit card numbers
- CVV codes
- SSN

### Access Control
API analytics dashboard requires admin authentication.

## 📁 Database Table

**Table:** `api_requests`

**Key Columns:**
- `endpoint` - API path
- `method` - GET/POST/PUT/DELETE
- `response_status` - HTTP status code
- `response_time` - Milliseconds
- `action_type` - What user tried to do
- `resource_type` - What resource was accessed
- `user_id` - User who made request
- `created_at` - When request happened

## 🎉 Next Steps

1. **Test It:** Make some API requests and check the dashboard
2. **Set Alerts:** Monitor critical endpoints
3. **Optimize:** Identify and fix slow endpoints
4. **Analyze:** Understand user behavior patterns
5. **Export:** Download data for external analysis

## 📞 Need Help?

- Full documentation: `API_TRACKING_SYSTEM.md`
- Check logs: `storage/logs/laravel.log`
- Test migration: `php artisan migrate:status`

---

**That's it! Your API is now fully tracked. Every request is logged automatically with zero configuration needed.** 🚀
