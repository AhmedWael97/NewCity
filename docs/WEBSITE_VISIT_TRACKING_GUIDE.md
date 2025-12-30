# Website Visit Tracking System - Implementation Guide

## Overview
A comprehensive website visit tracking system has been implemented to monitor and analyze all visitors to your website. The system automatically tracks every page visit, collects visitor data, and displays detailed analytics in the admin dashboard.

## Features Implemented

### 1. Automatic Visit Tracking
- **Real-time Tracking**: Every visitor is automatically tracked when they visit your website
- **Unique Visitor Detection**: Identifies first-time visitors vs returning visitors
- **Session Tracking**: Tracks entire user sessions including multiple page views
- **Device Detection**: Identifies mobile, tablet, and desktop users
- **Browser & Platform Detection**: Tracks which browsers and operating systems visitors use
- **Referrer Tracking**: Captures where visitors came from (traffic sources)
- **Page View Analytics**: Tracks which pages are most popular

### 2. Database Storage
A new `website_visits` table stores all visitor data including:
- Session ID
- User ID (for logged-in users)
- IP Address
- User Agent
- Device Type (mobile/tablet/desktop)
- Browser & Platform
- Referrer URL
- Landing Page & Current Page
- Country & City (optional)
- Visit Duration
- Pages Viewed Count
- Bounce Rate Data
- Timestamps

### 3. Admin Dashboard Analytics

#### Main Analytics Dashboard (`/admin/analytics`)
Displays overview including:
- Real-time visitors (currently online)
- Today's visitor count
- Total visits (last 30 days)
- Average pages per visit
- Integration with existing shop and city analytics

#### Dedicated Website Visits Page (`/admin/analytics/website-visits`)
Comprehensive analytics including:

**Real-time Metrics:**
- Visitors currently online (last 5 minutes)
- Today's visitors and visits
- Today's page views
- Bounce rate

**Period Comparisons:**
- Last 7 days statistics
- Last 30 days statistics
- Comparison with previous periods (growth/decline percentages)

**Visual Charts:**
- Daily visits trend (last 30 days)
- Hourly distribution (peak hours)
- Device breakdown (mobile/tablet/desktop)
- Browser statistics

**Detailed Reports:**
- Top landing pages
- Traffic sources (referrers)
- Browser breakdown
- Recent visits table (last 50 visits)

## Files Created/Modified

### New Files:
1. **`database/migrations/2024_11_28_000001_create_website_visits_table.php`**
   - Migration to create the website_visits table

2. **`app/Models/WebsiteVisit.php`**
   - Model with tracking methods and analytics functions
   - Includes helper methods for statistics, comparisons, and reports

3. **`app/Http/Middleware/TrackWebsiteVisit.php`**
   - Middleware that automatically tracks every website visit
   - Filters out admin pages, API calls, and bot traffic

4. **`resources/views/admin/analytics/website-visits.blade.php`**
   - Beautiful admin dashboard page with charts and statistics
   - Includes Chart.js visualizations
   - Auto-refreshes every 5 minutes

### Modified Files:
1. **`app/Http/Controllers/Admin/AnalyticsController.php`**
   - Added `websiteVisits()` method for dedicated analytics page
   - Updated `index()` to include website stats overview

2. **`resources/views/admin/analytics/index.blade.php`**
   - Added website visits button in header
   - Added 4 new stat cards showing real-time and daily metrics

3. **`routes/admin.php`**
   - Added route: `/admin/analytics/website-visits`

4. **`bootstrap/app.php`**
   - Registered `TrackWebsiteVisit` middleware to web routes

## How It Works

### 1. Automatic Tracking
When a visitor accesses your website:
1. The `TrackWebsiteVisit` middleware intercepts the request
2. Collects visitor information (IP, device, browser, etc.)
3. Checks if it's a new session or returning visitor
4. Updates/creates a record in the `website_visits` table
5. Continues to serve the page normally (no user-facing impact)

### 2. Session Management
- Each browser session gets a unique session ID
- If visitor visits multiple pages within 30 minutes, updates existing record
- Tracks total pages viewed and session duration
- Marks single-page visits as "bounces"

### 3. Privacy & Performance
- Excludes admin pages, API calls, and AJAX requests
- Optionally excludes bot traffic
- Tracks anonymously (IP addresses only, no personal data)
- Lightweight tracking with minimal performance impact

## Accessing the Dashboard

### View Website Visits Analytics:
1. Log in to admin panel
2. Navigate to **Analytics** → **Website Visits**
3. Or directly visit: `http://your-domain.com/admin/analytics/website-visits`

### View Quick Overview:
1. Go to main analytics dashboard: `http://your-domain.com/admin/analytics`
2. See real-time visitors and today's stats at the top

## Key Metrics Explained

- **Real-time Visitors**: Users active in the last 5 minutes
- **Unique Visitors**: Count of distinct IP addresses
- **Total Visits**: All sessions/visits (including returning visitors)
- **Page Views**: Total number of pages viewed across all visits
- **Avg Pages/Visit**: Average pages viewed per session
- **Bounce Rate**: Percentage of single-page visits
- **Avg Duration**: Average time spent on the website per session

## Customization Options

### Exclude Specific Pages from Tracking
Edit `app/Http/Middleware/TrackWebsiteVisit.php` line 75-85 to add more excluded paths:

```php
$skipPaths = [
    'admin/*',
    'api/*',
    'your-custom-path/*',
];
```

### Track Bot Traffic
Comment out lines 95-97 in `TrackWebsiteVisit.php`:

```php
// if ($this->agent->isRobot()) {
//     return true;
// }
```

### Change Real-time Window
Default is 5 minutes. To change, edit `app/Models/WebsiteVisit.php` line 177:

```php
return self::where('last_seen_at', '>=', Carbon::now()->subMinutes(10)) // Change to 10 minutes
```

### Adjust Auto-refresh Interval
Dashboard auto-refreshes every 5 minutes. To change, edit line 455 in `website-visits.blade.php`:

```javascript
setTimeout(() => {
    location.reload();
}, 600000); // Change to 10 minutes (600000ms)
```

## Database Maintenance

### Clear Old Visit Data
To keep database size manageable, you can periodically delete old visits:

```php
// Delete visits older than 90 days
WebsiteVisit::where('created_at', '<', Carbon::now()->subDays(90))->delete();
```

Consider creating a scheduled task for this in `app/Console/Kernel.php`.

## API Methods Available

The `WebsiteVisit` model provides these static methods:

```php
// Get statistics for a date range
WebsiteVisit::getStatistics($startDate, $endDate);

// Get daily statistics
WebsiteVisit::getDailyStats($days = 30);

// Get hourly distribution
WebsiteVisit::getHourlyDistribution($days = 7);

// Get top referrers
WebsiteVisit::getTopReferrers($days = 30, $limit = 10);

// Get top landing pages
WebsiteVisit::getTopLandingPages($days = 30, $limit = 10);

// Get device breakdown
WebsiteVisit::getDeviceBreakdown($days = 30);

// Get browser breakdown
WebsiteVisit::getBrowserBreakdown($days = 30, $limit = 10);

// Get real-time visitors
WebsiteVisit::getRealTimeVisitors();

// Get today's visitor count
WebsiteVisit::getTodayVisitors();

// Get comparison with previous period
WebsiteVisit::getComparison($days = 30);
```

## Troubleshooting

### Visits Not Being Tracked
1. Check if middleware is registered in `bootstrap/app.php`
2. Verify migration ran successfully: `php artisan migrate:status`
3. Clear cache: `php artisan optimize:clear`
4. Check if path is excluded in middleware

### Dashboard Shows Zero Visitors
1. Visit your website homepage to generate test data
2. Check database: `SELECT COUNT(*) FROM website_visits;`
3. Verify the view is loading correct data

### Charts Not Displaying
1. Check browser console for JavaScript errors
2. Verify Chart.js CDN is accessible
3. Ensure data arrays are properly formatted in blade template

## Security Considerations

- IP addresses are stored but not personally identifiable without additional context
- No passwords, emails, or sensitive data are tracked
- Compliant with GDPR as long as you have a privacy policy
- Consider adding IP anonymization for EU visitors if needed

## Performance Tips

1. Add indexes (already included in migration) for faster queries
2. Use caching for expensive statistics queries
3. Archive old data periodically
4. Consider using Laravel queues for tracking in high-traffic sites

## Future Enhancements

Potential improvements you can add:

1. **Geo-location**: Integrate GeoIP database to track visitor countries/cities
2. **User Journey**: Track the path visitors take through your site
3. **Conversion Tracking**: Link visits to specific goals/conversions
4. **Export Reports**: Add CSV/PDF export functionality
5. **Email Alerts**: Get notified when traffic spikes or drops
6. **A/B Testing**: Integrate with A/B test tracking
7. **Heatmaps**: Visual representation of where users click

## Support

If you need to modify or extend this system:

1. All tracking logic is in `app/Http/Middleware/TrackWebsiteVisit.php`
2. All analytics methods are in `app/Models/WebsiteVisit.php`
3. Dashboard controller methods are in `app/Http/Controllers/Admin/AnalyticsController.php`
4. Views are in `resources/views/admin/analytics/`

## Summary

✅ Automatic visit tracking activated
✅ Real-time visitor monitoring
✅ Comprehensive analytics dashboard
✅ Daily, weekly, and monthly reports
✅ Device and browser analytics
✅ Traffic source tracking
✅ Beautiful visualizations with charts

Your website visit tracking system is now fully operational!
