# Admin Email Notification System - Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Access SMTP Settings
1. Login to admin panel
2. Navigate to: `/admin/smtp`

### Step 2: Configure Gmail SMTP
```
Host: smtp.gmail.com
Port: 587
Encryption: TLS
Username: your-email@gmail.com
Password: [Your App Password]
From Address: noreply@yourapp.com
From Name: City App
```

**Get Gmail App Password:**
1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Enable 2-Step Verification
3. Generate [App Password](https://myaccount.google.com/apppasswords)
4. Use this password in SMTP settings

### Step 3: Test Configuration
1. Enter your email address in "Test Email" field
2. Click "Send Test Email"
3. Check your inbox

### Step 4: Set Up Cron Job

**Windows (Easy Method):**
```powershell
# Run this PowerShell script every 5 minutes via Task Scheduler
cd E:\coupons\githubs\City\City
php artisan email:process-admin-queue
```

**Linux/Mac:**
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## 📧 What Gets Sent

Admins receive email notifications for:
- ✅ New shop suggestions
- ✅ New city suggestions  
- ✅ Shop ratings/reviews
- ✅ Service ratings/reviews
- ✅ New user services
- ✅ New marketplace items
- ✅ New user registrations

## 🎛️ Admin Controls

### View Email Queue
**URL:** `/admin/email-queue`
- See all pending, sent, and failed emails
- Retry failed emails
- Delete old emails

### Customize Preferences
**URL:** `/admin/email-queue/preferences/edit`
- Turn notifications on/off per event type
- Each admin has individual preferences

## 🧪 Manual Testing

### Queue a Test Email (via Tinker):
```bash
php artisan tinker
```

```php
use App\Services\AdminEmailQueueService;

AdminEmailQueueService::queueEmail(
    'test_event',
    'Test Email Subject',
    'This is a test email from the system.',
    ['test_data' => 'value']
);
```

### Process Queue Manually:
```bash
php artisan email:process-admin-queue
```

### Check Queue Status:
```bash
php artisan tinker
```

```php
\App\Models\AdminEmailQueue::pending()->count(); // Pending emails
\App\Models\AdminEmailQueue::sent()->count();    // Sent emails
\App\Models\AdminEmailQueue::failed()->count();  // Failed emails
```

## 🔥 Common Commands

```bash
# Process email queue (run this every 5 minutes)
php artisan email:process-admin-queue

# Process more emails at once
php artisan email:process-admin-queue --limit=50

# Check for errors
php artisan email:process-admin-queue --verbose

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 🐛 Troubleshooting

### Issue: "No active SMTP settings found"
**Solution:** Configure SMTP at `/admin/smtp`

### Issue: Test email fails
**Solution:**
1. Check Gmail App Password (not regular password)
2. Verify port 587 is open
3. Try SSL on port 465

### Issue: Emails not sending automatically
**Solution:**
1. Verify cron job is running:
   ```bash
   php artisan email:process-admin-queue
   ```
2. Check queue for errors: `/admin/email-queue`

### Issue: "Authentication failed"
**Solution:**
- Use Gmail App Password
- Enable "Less secure app access" (not recommended)
- Try different email provider

## 📂 File Locations

### Controllers:
- `app/Http/Controllers/Admin/AdminSmtpController.php`
- `app/Http/Controllers/Admin/AdminEmailQueueController.php`

### Models:
- `app/Models/SmtpSettings.php`
- `app/Models/AdminEmailQueue.php`
- `app/Models/AdminEmailPreference.php`

### Services:
- `app/Services/DynamicMailService.php`
- `app/Services/AdminEmailQueueService.php`

### Views:
- `resources/views/admin/smtp/index.blade.php`
- `resources/views/admin/email-queue/index.blade.php`
- `resources/views/admin/email-queue/show.blade.php`
- `resources/views/admin/email-queue/preferences.blade.php`
- `resources/views/emails/admin-notification.blade.php`

### Command:
- `app/Console/Commands/ProcessAdminEmailQueue.php`

### Routes:
- `routes/admin.php` (lines for SMTP and Email Queue)
- `routes/console.php` (scheduled command)

## 📊 Email Queue Dashboard

### Access: `/admin/email-queue`

**Features:**
- 📊 Statistics: Pending, Sent, Failed counts
- 🔍 Filter by status and event type
- 👁️ View email details
- 🔄 Retry failed emails
- 🗑️ Delete emails
- 🧹 Bulk clear sent emails

### Email Details: `/admin/email-queue/{id}`

**Shows:**
- Event type
- Subject and body
- Recipients list
- Status and attempts
- Error messages (if failed)
- Timeline of events

## 🔐 Security Notes

- ✅ Passwords are encrypted in database
- ✅ Only admins can access settings
- ✅ CSRF protection on all forms
- ✅ Rate limiting on email sending
- ⚠️ Keep `APP_KEY` in `.env` secure
- ⚠️ Don't commit `.env` to version control

## 🎨 Customization

### Email Template:
Edit: `resources/views/emails/admin-notification.blade.php`

### Add Custom Event:
```php
use App\Services\AdminEmailQueueService;

AdminEmailQueueService::queueEmail(
    'custom_event_type',
    'Email Subject',
    'Email body...',
    ['any' => 'data']
);
```

### Change Schedule:
Edit: `routes/console.php`
```php
// Change from every 5 minutes to every minute
Schedule::command('email:process-admin-queue')->everyMinute();

// Or hourly
Schedule::command('email:process-admin-queue')->hourly();
```

## 📞 Need Help?

1. Check documentation: `ADMIN_EMAIL_NOTIFICATION_SYSTEM.md`
2. View Laravel logs: `storage/logs/laravel.log`
3. Test SMTP: `/admin/smtp` → Send Test Email
4. Check queue: `/admin/email-queue`

---

**Tip:** Always test with a real email address before going live!

**Last Updated:** December 15, 2025
