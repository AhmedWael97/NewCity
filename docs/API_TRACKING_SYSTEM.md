# API Request Tracking System

## Overview
Comprehensive system that automatically tracks ALL API requests to monitor:
- Which endpoints are being accessed
- What actions users are trying to perform
- Request/response patterns and performance
- Error rates and types
- User behavior via API
- Device and platform usage

## Features

### 🎯 Automatic Tracking
Every API request is automatically logged with:
- **Endpoint & Method** - Full URL path and HTTP method (GET, POST, PUT, DELETE)
- **Request Data** - POST/PUT body and query parameters (sensitive data sanitized)
- **Response Status** - HTTP status codes (200, 404, 500, etc.)
- **Response Time** - How long the request took to process (in milliseconds)
- **User Information** - User ID (if authenticated), IP address, device type
- **Action Detection** - Automatically detects what the user is trying to do (search, view, create, update, delete)
- **Resource Detection** - Identifies the resource type being accessed (shop, city, product, etc.)
- **Error Tracking** - Captures error messages for failed requests

### 📊 Analytics Dashboard
Access at: `/admin/api-analytics`

**Available Metrics:**
- **Overview Stats**
  - Total API requests
  - Success rate
  - Failed requests count
  - Unique users
  - Average response time

- **Endpoint Analysis**
  - Most used endpoints
  - Slowest endpoints
  - Most failed endpoints
  - Request count per endpoint

- **User Behavior**
  - Requests by action type (search, view, create, etc.)
  - Requests by resource type (shops, cities, products)
  - Top users by request count
  - Device distribution (mobile, tablet, desktop)

- **Performance Metrics**
  - Response time trends
  - Requests over time (daily breakdown)
  - HTTP method distribution
  - Status code distribution

- **Error Analysis**
  - Most common error messages
  - Failed endpoints
  - Error trends over time

### 🔒 Security & Privacy
- **Sensitive Data Protection** - Passwords, tokens, and credit card info are automatically redacted
- **GDPR Compliant** - User data can be anonymized or deleted
- **Minimal Performance Impact** - Tracking happens after response is sent
- **Graceful Failure** - Tracking errors never disrupt API responses

## Installation & Setup

### 1. Run Migration
```bash
php artisan migrate
```

This creates the `api_requests` table with all necessary fields and indexes.

### 2. Verify Middleware Registration
The middleware is already registered in `bootstrap/app.php`:
```php
$middleware->api(append: [
    \App\Http\Middleware\TrackApiRequest::class,
]);
```

### 3. Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### 4. Test the System
Make any API request:
```bash
curl http://your-domain.com/api/v1/shops
```

Then check the admin dashboard at `/admin/api-analytics`

## How It Works

### Architecture

```
API Request
    ↓
TrackApiRequest Middleware
    ↓
Process Request (Next)
    ↓
Calculate Response Time
    ↓
Extract Request Details
    ↓
Detect Action & Resource Type
    ↓
Save to Database (ApiRequest Model)
    ↓
Return Response
```

### Request Flow

1. **Request Arrives** - API request hits your server
2. **Middleware Captures** - TrackApiRequest middleware starts timer
3. **Request Processed** - Your controller handles the request normally
4. **Response Generated** - Response is created
5. **Tracking Happens** - Middleware logs the request details (after response)
6. **Data Stored** - Information saved to `api_requests` table
7. **Response Sent** - Original response sent to client

### Action Type Detection

The system automatically detects what action the user is trying to perform:

| Pattern | Action Type | Example |
|---------|-------------|---------|
| `/search` | search | `/api/v1/shops/search` |
| `/login`, `/register` | authentication | `/api/v1/auth/login` |
| `/favorite` | favorite | `/api/v1/shops/123/favorite` |
| `/rating`, `/review` | review | `/api/v1/shops/123/rating` |
| `/contact` | contact | `/api/v1/shops/123/contact` |
| `GET /resource/{id}` | view | `/api/v1/shops/123` |
| `GET /resource` | list | `/api/v1/shops` |
| `POST /resource` | create | `POST /api/v1/shops` |
| `PUT/PATCH /resource` | update | `PUT /api/v1/shops/123` |
| `DELETE /resource` | delete | `DELETE /api/v1/shops/123` |

### Resource Type Detection

The system identifies which resource is being accessed:

| Endpoint Contains | Resource Type |
|-------------------|---------------|
| `/shops` | shop |
| `/cities` | city |
| `/categories` | category |
| `/products` | product |
| `/services` | service |
| `/marketplace` | marketplace |
| `/news` | news |
| `/forums` | forum |
| `/user` | user |

## Usage Examples

### Viewing Analytics

**Access the Dashboard:**
```
http://your-domain.com/admin/api-analytics
```

**Filter by Time Period:**
```
/admin/api-analytics?days=7   # Last 7 days
/admin/api-analytics?days=30  # Last 30 days
```

### Querying Data Programmatically

```php
use App\Models\ApiRequest;

// Get all requests from today
$todayRequests = ApiRequest::today()->get();

// Get failed requests
$failedRequests = ApiRequest::failed()->get();

// Get requests for specific endpoint
$shopRequests = ApiRequest::byEndpoint('shops')->get();

// Get requests by user
$userRequests = ApiRequest::byUser($userId)->get();

// Get successful requests from last 7 days
$recentSuccess = ApiRequest::lastDays(7)
    ->successful()
    ->orderByDesc('created_at')
    ->get();

// Average response time for an endpoint
$avgTime = ApiRequest::byEndpoint('api/v1/shops')
    ->lastDays(7)
    ->avg('response_time');

// Most common errors
$errors = ApiRequest::failed()
    ->select('error_message', DB::raw('COUNT(*) as count'))
    ->groupBy('error_message')
    ->orderByDesc('count')
    ->get();
```

### Export Data

**Export to CSV:**
```
GET /admin/api-analytics/export?days=30
```

This downloads a CSV file with all API request data including:
- Date/Time
- User ID
- Endpoint
- Method
- Action Type
- Resource Type
- Response Status
- Response Time
- Device Type
- IP Address
- Error Message

### Real-Time Monitoring

**Get Recent Requests (JSON):**
```
GET /admin/api-analytics/recent?limit=100
```

**Get Endpoint Statistics:**
```
GET /admin/api-analytics/endpoint-stats?endpoint=shops&days=7
```

Response:
```json
{
  "success": true,
  "data": {
    "total_requests": 1542,
    "avg_response_time": 124.5,
    "min_response_time": 45.2,
    "max_response_time": 892.3,
    "successful": 1498,
    "failed": 44
  }
}
```

## Database Schema

### api_requests Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | User ID (nullable for guest requests) |
| endpoint | string | API endpoint path |
| method | string | HTTP method (GET, POST, etc.) |
| request_data | json | POST/PUT body data |
| query_params | json | URL query parameters |
| headers | json | Selected request headers |
| ip_address | string | Client IP address |
| user_agent | string | Browser/app user agent |
| device_type | string | mobile/tablet/desktop |
| response_status | integer | HTTP status code |
| response_time | decimal | Time in milliseconds |
| error_message | text | Error message if failed |
| action_type | string | Detected action (search, view, etc.) |
| resource_type | string | Resource being accessed |
| resource_id | string | ID of the resource |
| session_id | string | Session identifier |
| created_at | timestamp | When request was made |
| updated_at | timestamp | Last update time |

**Indexes:**
- Primary key on `id`
- Index on `user_id`, `endpoint`, `method`, `response_status`, `action_type`, `resource_type`, `created_at`
- Composite indexes for common query patterns

## Performance Considerations

### Why It Doesn't Slow Down Your API:

1. **Post-Response Tracking**
   - Response is sent to client first
   - Tracking happens after
   - No blocking operations

2. **Efficient Database Writes**
   - Single INSERT query
   - Indexed columns for fast writes
   - No complex joins during write

3. **Minimal Data Processing**
   - Simple pattern matching
   - No external API calls
   - Fast string operations

4. **Graceful Error Handling**
   - All errors caught and logged
   - Never disrupts API response
   - Fails silently

5. **Optimized Schema**
   - Indexed columns for analytics queries
   - JSON columns for flexible data
   - Proper data types for performance

### Expected Performance Impact:
- **Request overhead:** < 5ms
- **Database write:** ~2-3ms
- **Memory usage:** Minimal
- **Storage:** ~1KB per request

## Use Cases

### 1. Monitor API Usage
Track which endpoints are most popular to optimize performance and plan scaling.

### 2. Detect Issues
Identify endpoints with high error rates or slow response times before users complain.

### 3. User Behavior Analysis
Understand how users interact with your API - what they search for, which resources they access.

### 4. Security Monitoring
Detect unusual patterns like:
- Excessive requests from single IP
- Failed authentication attempts
- Suspicious endpoint access patterns

### 5. Performance Optimization
Identify slow endpoints and optimize them based on real usage data.

### 6. Business Intelligence
- Track feature adoption
- Monitor mobile vs desktop usage
- Understand user journeys through API

### 7. Debugging
- Reproduce user issues with exact request details
- Track down intermittent errors
- Analyze error patterns

## Best Practices

### 1. Regular Cleanup
Set up a scheduled task to archive or delete old data:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Delete API requests older than 90 days
    $schedule->call(function () {
        ApiRequest::where('created_at', '<', now()->subDays(90))->delete();
    })->daily();
}
```

### 2. Monitor Disk Usage
API tracking can generate significant data. Monitor your database size:

```bash
# Check table size
php artisan tinker
>>> DB::select("SELECT 
    table_name, 
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES 
WHERE table_name = 'api_requests'");
```

### 3. Use Time-Based Queries
Always filter by date range for better performance:

```php
// Good - uses indexed created_at
ApiRequest::where('created_at', '>=', now()->subDays(7))->get();

// Bad - scans all records
ApiRequest::all();
```

### 4. Archive Historical Data
For long-term storage, export to data warehouse:

```php
// Export to S3, BigQuery, etc.
$oldData = ApiRequest::where('created_at', '<', now()->subMonths(6))->get();
// ... export logic ...
$oldData->delete();
```

### 5. Set Up Alerts
Monitor critical endpoints:

```php
// Check for high error rates
$errorRate = ApiRequest::lastDays(1)
    ->where('endpoint', 'api/v1/shops')
    ->failed()
    ->count();

if ($errorRate > 100) {
    // Send alert
    Mail::to('admin@example.com')->send(new HighErrorRateAlert($errorRate));
}
```

## Troubleshooting

### Requests Not Being Tracked

1. **Check middleware is registered:**
   ```php
   // bootstrap/app.php
   $middleware->api(append: [
       \App\Http\Middleware\TrackApiRequest::class,
   ]);
   ```

2. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

3. **Check database:**
   ```bash
   php artisan migrate:status
   ```

### Slow API Responses

1. **Check if tracking is blocking:**
   - Tracking should happen AFTER response
   - Check error logs for tracking exceptions

2. **Optimize database:**
   ```bash
   php artisan db:optimize
   ```

3. **Add indexes if missing:**
   ```sql
   CREATE INDEX idx_created_at ON api_requests(created_at);
   ```

### High Disk Usage

1. **Implement cleanup schedule**
2. **Archive old data**
3. **Compress JSON fields**
4. **Consider partitioning large tables**

## API Endpoints

### Admin Routes (require admin authentication)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/api-analytics` | Main analytics dashboard |
| GET | `/admin/api-analytics/recent` | Recent requests (JSON) |
| GET | `/admin/api-analytics/export` | Export to CSV |
| GET | `/admin/api-analytics/endpoint-stats` | Endpoint statistics |
| GET | `/admin/api-analytics/{id}` | Detailed request info |

## Security Notes

### Sensitive Data Handling

The system automatically redacts:
- `password`
- `password_confirmation`
- `token`
- `api_key`
- `secret`
- `credit_card`
- `cvv`
- `ssn`

To add more fields to redact:

```php
// app/Http/Middleware/TrackApiRequest.php
protected function sanitizeRequestData(array $data): array
{
    $sensitiveFields = [
        'password',
        'your_sensitive_field', // Add here
    ];
    // ...
}
```

### Access Control

API analytics are only accessible to admin users. The routes are protected by:

```php
Route::middleware(['auth:web', 'admin'])->group(function () {
    // API analytics routes
});
```

## Future Enhancements

Potential improvements:
- Real-time dashboard with WebSockets
- Alert system for anomalies
- API rate limiting based on tracking data
- Machine learning for pattern detection
- Integration with external monitoring tools (Datadog, New Relic)
- API usage billing based on tracking data

## Support

For issues or questions:
1. Check error logs: `storage/logs/laravel.log`
2. Review database for tracking data
3. Test with simple API request
4. Check middleware registration

## Conclusion

This API tracking system provides complete visibility into how your API is being used without impacting performance. Use it to optimize, debug, and understand your API usage patterns.

**Quick Start:**
1. Run migration: `php artisan migrate`
2. Make API request
3. View dashboard: `/admin/api-analytics`
4. Analyze your API usage!
