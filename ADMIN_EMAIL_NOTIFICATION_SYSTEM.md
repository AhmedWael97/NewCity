# Admin Email Notification System - Complete Guide

## Overview
A complete email queue system that sends notifications to administrators when important events occur in the application.

## Features
- ✅ SMTP Configuration with testing capability
- ✅ Email queue system with retry mechanism
- ✅ Individual admin email preferences
- ✅ Automatic cron job processing
- ✅ Admin dashboard integration
- ✅ Support for multiple notification types

## Notification Events

The system sends email notifications for:
1. **Shop Suggestions** - When users suggest new shops
2. **City Suggestions** - When users suggest new cities
3. **Shop Ratings** - When users rate shops
4. **Service Ratings** - When users rate services
5. **New Services** - When new user services are created
6. **New Marketplace Items** - When new marketplace products are listed
7. **New User Registrations** - When new users register

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

This creates three tables:
- `smtp_settings` - Stores SMTP configuration
- `admin_email_queue` - Email queue with retry support
- `admin_email_preferences` - Individual admin preferences

### 2. Configure SMTP Settings

Navigate to: **Admin Panel → SMTP Settings** (`/admin/smtp`)

#### Gmail Setup Example:
- **Host:** smtp.gmail.com
- **Port:** 587
- **Encryption:** TLS
- **Username:** your-email@gmail.com
- **Password:** Your App Password (not your Gmail password)
- **From Address:** noreply@yourapp.com
- **From Name:** City App

**Important for Gmail:** 
- Enable 2-Factor Authentication
- Generate an [App Password](https://myaccount.google.com/apppasswords)
- Use the App Password instead of your regular password

#### Test SMTP Configuration:
1. After saving SMTP settings
2. Enter a test email address
3. Click "Send Test Email"
4. Check your inbox for the test message

### 3. Set Up Cron Job

The email queue is processed automatically every 5 minutes.

#### Linux/Mac (crontab):
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

#### Windows (Task Scheduler):
1. Open Task Scheduler
2. Create Basic Task
3. Trigger: Daily at 12:00 AM
4. Action: Start a program
5. Program: `C:\php\php.exe`
6. Arguments: `artisan schedule:run`
7. Start in: `C:\path\to\your\project`
8. In Triggers, edit and set "Repeat task every: 1 minute"

#### Manual Processing (for testing):
```bash
php artisan email:process-admin-queue
```

Options:
```bash
# Process up to 50 emails
php artisan email:process-admin-queue --limit=50

# See help
php artisan email:process-admin-queue --help
```

## Usage

### Admin Panel Routes

#### SMTP Configuration:
- **View/Edit:** `/admin/smtp`
- **Test Email:** POST `/admin/smtp/test`

#### Email Queue Management:
- **View Queue:** `/admin/email-queue`
- **View Email Details:** `/admin/email-queue/{id}`
- **Retry Failed Email:** POST `/admin/email-queue/{id}/retry`
- **Delete Email:** DELETE `/admin/email-queue/{id}`
- **Clear Sent Emails:** DELETE `/admin/email-queue`

#### Email Preferences:
- **Edit Preferences:** `/admin/email-queue/preferences/edit`
- **Update:** PUT `/admin/email-queue/preferences`

### Admin Email Preferences

Each admin can customize which notifications they receive:

1. Navigate to: **Email Queue → Email Preferences**
2. Toggle notifications for each event type
3. Save preferences

### Programmatic Usage

To queue a custom email notification:

```php
use App\Services\AdminEmailQueueService;

// Simple notification
AdminEmailQueueService::queueEmail(
    'custom_event',           // Event type
    'Subject Line',           // Email subject
    'Email body content...',  // Email body
    ['data' => 'value']      // Optional event data
);
```

#### Built-in Helper Methods:

```php
use App\Services\AdminEmailQueueService;

// Shop suggestion notification
AdminEmailQueueService::queueShopSuggestion($suggestion);

// City suggestion notification
AdminEmailQueueService::queueCitySuggestion($suggestion);

// Shop rating notification
AdminEmailQueueService::queueShopRating($rating);

// Service rating notification
AdminEmailQueueService::queueServiceRating($review);

// New service notification
AdminEmailQueueService::queueNewService($service);

// New marketplace item notification
AdminEmailQueueService::queueNewMarketplaceItem($item);

// New user registration notification
AdminEmailQueueService::queueNewUser($user);
```

## Email Queue Status

### Status Types:
- **pending** - Waiting to be sent
- **processing** - Currently being sent
- **sent** - Successfully delivered
- **failed** - Delivery failed (will retry up to 3 times)

### Retry Logic:
- Failed emails are automatically retried
- Maximum 3 attempts per email
- After 3 failures, email remains in "failed" status
- Admins can manually retry failed emails

## Database Structure

### `smtp_settings` Table:
```sql
- id
- host (varchar)
- port (int)
- encryption (varchar: tls, ssl, null)
- username (varchar)
- password (encrypted)
- from_address (varchar)
- from_name (varchar)
- is_active (boolean)
- last_tested_at (timestamp)
- test_successful (boolean)
- test_error (text)
- timestamps
```

### `admin_email_queue` Table:
```sql
- id
- event_type (varchar)
- subject (varchar)
- body (text)
- recipients (json array)
- event_data (json)
- status (enum: pending, processing, sent, failed)
- attempts (int)
- sent_at (timestamp)
- error_message (text)
- timestamps
- indexes: status, event_type, created_at
```

### `admin_email_preferences` Table:
```sql
- id
- user_id (foreign key)
- shop_suggestion (boolean)
- city_suggestion (boolean)
- shop_rate (boolean)
- service_rate (boolean)
- new_service (boolean)
- new_marketplace (boolean)
- new_user (boolean)
- timestamps
```

## Troubleshooting

### Emails Not Sending?

1. **Check SMTP Settings:**
   - Navigate to `/admin/smtp`
   - Click "Send Test Email"
   - Review error message if test fails

2. **Check Email Queue:**
   - Navigate to `/admin/email-queue`
   - Look for failed emails
   - Check error messages

3. **Verify Cron Job:**
   ```bash
   # Manually run to check for errors
   php artisan email:process-admin-queue
   ```

4. **Check Logs:**
   - Review `storage/logs/laravel.log`
   - Look for mail-related errors

### Common Issues:

#### "No active SMTP settings found"
- Go to `/admin/smtp` and configure SMTP
- Ensure "is_active" is checked
- Test the configuration

#### "Failed to authenticate"
- Gmail: Use App Password, not regular password
- Check username and password are correct
- Verify encryption method (TLS/SSL)

#### "Connection timeout"
- Check firewall settings
- Verify port is not blocked
- Try different encryption (TLS vs SSL)

#### "From address not authorized"
- Ensure from_address matches authenticated email
- Or use an authorized sending address

## Email Template Customization

Email template location:
```
resources/views/emails/admin-notification.blade.php
```

Customize the HTML/CSS as needed. Available variables:
- `$body` - Email body content
- `$eventData` - Associated event data (array)

## Security Considerations

1. **Password Encryption:**
   - SMTP passwords are encrypted using Laravel's encryption
   - Keep `APP_KEY` in `.env` secure

2. **Admin Access Only:**
   - All routes protected by `auth:web` and `admin` middleware
   - Only admins can manage SMTP and email settings

3. **Rate Limiting:**
   - Email queue processes 10 emails per run by default
   - Prevents overwhelming SMTP server
   - Adjust with `--limit` option if needed

## Performance Tips

1. **Queue Processing Frequency:**
   - Default: Every 5 minutes
   - For high-traffic sites, increase frequency
   - Edit `routes/console.php`

2. **Batch Limits:**
   - Default: 10 emails per batch
   - Increase for faster processing: `--limit=50`
   - Balance with SMTP provider limits

3. **Clean Up Sent Emails:**
   - Periodically delete old sent emails
   - Use "Clear Sent Emails" button in admin panel
   - Or schedule cleanup:
   ```php
   // Add to routes/console.php
   Schedule::command('email:cleanup-sent')->daily();
   ```

## API Endpoints (Internal)

All endpoints require authentication and admin role:

- `GET /admin/smtp` - SMTP settings page
- `POST /admin/smtp` - Save SMTP settings
- `POST /admin/smtp/test` - Test SMTP configuration
- `GET /admin/email-queue` - View email queue
- `GET /admin/email-queue/{id}` - View email details
- `POST /admin/email-queue/{id}/retry` - Retry failed email
- `DELETE /admin/email-queue/{id}` - Delete email
- `DELETE /admin/email-queue/clear-sent` - Clear all sent emails
- `GET /admin/email-queue/preferences/edit` - Edit preferences
- `PUT /admin/email-queue/preferences` - Update preferences

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Laravel logs: `storage/logs/laravel.log`
3. Test SMTP configuration manually
4. Verify cron job is running

## Future Enhancements

Potential improvements:
- [ ] Email templates per event type
- [ ] HTML email editor
- [ ] Email scheduling (delayed sending)
- [ ] Attachment support
- [ ] Email analytics dashboard
- [ ] Multiple SMTP profiles
- [ ] Webhook notifications as alternative to email

---

**Last Updated:** December 15, 2025
**Version:** 1.0.0
