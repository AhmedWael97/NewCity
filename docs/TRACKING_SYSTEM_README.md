# User Activity Tracking System

## Overview
This system tracks user behavior, navigation patterns, searches, and interactions without affecting performance. All tracking is done asynchronously using Laravel's queue system.

## Features

### 1. **Automatic Tracking**
- Page views
- Time on page
- Scroll depth
- Device & browser information
- Geographic data (city, IP)

### 2. **User Interactions**
- Button clicks (Call, Directions, Share, Favorite)
- Shop card clicks
- Navigation menu clicks
- Tab switches
- Form submissions

### 3. **Search Tracking**
- Global search queries
- Inline search (products, services)
- Search filters and sorting

### 4. **Conversions**
- Phone calls initiated
- Direction requests
- Share actions
- Favorite/bookmark actions

### 5. **Error Tracking**
- JavaScript errors
- Unhandled promise rejections
- API failures

## Setup Instructions

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Configure Queue
Make sure your queue is running:
```bash
php artisan queue:work
```

For production, set up supervisor or use Laravel Horizon.

### 3. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## How It Works

### Backend (Laravel)
1. **UserEvent Model** - Stores all tracking data
2. **UserActivityTracked Event** - Dispatched when tracking occurs
3. **StoreUserActivity Listener** - Processes events in queue (ShouldQueue)
4. **UserTrackingService** - Service class for tracking operations
5. **TrackingController** - API endpoint for frontend tracking

### Frontend (JavaScript)
- `public/js/user-tracking.js` - Automatic tracking script
- Tracks interactions using event listeners
- Sends data via fetch API with `keepalive` flag
- Uses `navigator.sendBeacon` for page unload events

## Usage

### Automatic Tracking
Just include the script in your layout (already done):
```html
<script src="{{ asset('js/user-tracking.js') }}" defer></script>
```

### Manual Tracking (Backend)
```php
use App\Services\UserTrackingService;

class YourController extends Controller
{
    protected $tracking;

    public function __construct(UserTrackingService $tracking)
    {
        $this->tracking = $tracking;
    }

    public function yourMethod()
    {
        // Track shop view
        $this->tracking->trackShopView($shopId, $shopName, $cityId);

        // Track search
        $this->tracking->trackSearch($query, 'shops');

        // Track conversion
        $this->tracking->trackConversion('phone_call', $shopName, [
            'shop_id' => $shopId
        ]);

        // Track error
        $this->tracking->trackError('api_error', $errorMessage);
    }
}
```

### Manual Tracking (Frontend)
```javascript
// Track custom event
window.CityTracker.send('custom_event', {
    event_category: 'custom',
    event_action: 'your_action',
    event_label: 'your_label',
    event_data: {
        custom_field: 'value'
    }
});

// Track conversion
window.CityTracker.send('conversion', {
    event_category: 'conversion',
    event_action: 'form_submitted',
    event_label: 'Contact Form'
});
```

## Analytics Dashboard

Access analytics at: `/admin/analytics` (requires admin authentication)

### Available Metrics:
- **Overview**: Total events, unique sessions, unique users, page views
- **Top Pages**: Most visited pages
- **Top Searches**: Most popular search queries
- **Device Breakdown**: Mobile vs Desktop vs Tablet
- **Browser Breakdown**: Chrome, Safari, Firefox, etc.
- **Events Over Time**: Daily event trends
- **Top Shops**: Most viewed shops
- **Top Cities**: Most viewed cities
- **Conversions**: Phone calls, directions, shares
- **Errors**: JavaScript and API errors
- **Engagement**: Avg time on page, avg scroll depth

### Export Data
Click "Export CSV" to download analytics data for external analysis.

## Performance Considerations

### Why It Doesn't Affect Performance:

1. **Asynchronous Processing**
   - All events are processed in the background queue
   - No blocking operations during user requests

2. **Lightweight Frontend**
   - Tracking script is loaded with `defer`
   - Uses efficient event delegation
   - Minimal DOM queries

3. **Optimized Database**
   - Indexed columns for fast queries
   - Batch inserts possible via queue

4. **Graceful Failures**
   - All errors are caught and logged
   - Never disrupts user experience
   - Returns success even on failure

5. **Smart Batching**
   - Debounced scroll tracking
   - Throttled search tracking
   - Uses `sendBeacon` for reliable unload tracking

## Database Schema

### user_events Table
```sql
- id: Primary key
- user_id: User ID (nullable for guests)
- session_id: Session identifier
- event_type: Type of event (page_view, search, click, etc.)
- event_category: Category (navigation, search, interaction, error)
- event_action: Specific action taken
- event_label: Additional context
- event_data: JSON data for detailed information
- page_url: Current page URL
- page_title: Page title
- referrer: Previous page URL
- user_agent: Browser user agent
- device_type: mobile|tablet|desktop
- browser: Browser name
- platform: Operating system
- ip_address: User IP
- city_id: Related city
- shop_id: Related shop
- category_id: Related category
- time_on_page: Seconds spent on page
- scroll_depth: Percentage scrolled
- created_at, updated_at: Timestamps
```

## Use Cases

### Marketing Analysis
- Identify which pages have high bounce rates
- See where users spend most time
- Track conversion funnels
- Identify popular searches with no results

### UX Improvements
- Find pages where users get stuck
- Identify confusing navigation patterns
- See which CTAs are most effective
- Track mobile vs desktop behavior differences

### Business Intelligence
- Most viewed shops and cities
- Popular search terms
- Peak usage times
- User journey mapping

### Error Monitoring
- JavaScript errors affecting users
- API failures and their impact
- Browser-specific issues

## Privacy & GDPR Compliance

- IP addresses can be anonymized (modify UserTrackingService)
- User IDs are nullable (supports anonymous tracking)
- Data retention policies can be implemented
- Export feature allows data portability
- No third-party tracking services used

## Maintenance

### Clean Old Data
Create a scheduled command to delete old events:
```php
// In App\Console\Kernel.php
$schedule->command('tracking:cleanup')->daily();
```

### Monitor Queue
Make sure queue workers are always running:
```bash
php artisan queue:listen --tries=3
```

## Troubleshooting

### Events Not Being Tracked
1. Check if queue is running: `php artisan queue:work`
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify JavaScript console for errors
4. Check network tab for API calls to `/api/track`

### High Database Load
1. Ensure proper indexing (already done in migration)
2. Consider partitioning `user_events` table by date
3. Archive old data to separate table
4. Use Redis queue driver instead of database

## Future Enhancements

- [ ] Real-time analytics dashboard with WebSockets
- [ ] A/B testing integration
- [ ] Funnel visualization
- [ ] Heatmap generation
- [ ] Session replay
- [ ] Automatic anomaly detection
- [ ] Integration with Google Analytics
- [ ] Custom event triggers and alerts

## Support

For questions or issues, check the logs or create a support ticket.
