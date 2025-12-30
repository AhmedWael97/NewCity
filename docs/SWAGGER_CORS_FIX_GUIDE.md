# Swagger API Documentation - CORS Fix Guide

## 🔴 Problem
After uploading to server, Swagger shows:
```
Failed to fetch.
Possible Reasons:
- CORS
- Network Failure
- URL scheme must be "http" or "https" for CORS request.
```

## ✅ Solution Applied

### 1. CORS Configuration Added
Created `config/cors.php` with proper settings for API documentation access.

### 2. Swagger Configuration Updated
- Updated `config/l5-swagger.php` to use `APP_URL` from environment
- Added proper middleware for API routes
- Configured absolute paths

### 3. Bootstrap App Updated
Added CORS middleware to API routes in `bootstrap/app.php`.

### 4. Server URL Fixed
Updated `ApiDocumentationController.php` to use `L5_SWAGGER_CONST_HOST` constant.

---

## 🚀 Steps to Fix on Server

### Step 1: Update .env File
Add or update these lines in your `.env` file:

```bash
# Replace with your actual domain
APP_URL=https://yourdomain.com

# Enable Swagger generation
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_USE_ABSOLUTE_PATH=true

# Optional: Set base path
L5_SWAGGER_BASE_PATH="${APP_URL}"
```

### Step 2: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 3: Regenerate Swagger Documentation
```bash
php artisan l5-swagger:generate
```

Or use the provided script:
```bash
./regenerate-swagger.ps1
```

### Step 4: Set Proper Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Step 5: Verify Files
Check that these files exist:
- `storage/api-docs/api-docs.json`
- `public/docs/` (if using asset publishing)

---

## 🔍 Common Issues & Solutions

### Issue 1: Still Getting CORS Error
**Solution:**
Check your web server configuration (Apache/Nginx).

**For Nginx:**
Add to your server block:
```nginx
location /api/documentation {
    add_header 'Access-Control-Allow-Origin' '*' always;
    add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS' always;
    add_header 'Access-Control-Allow-Headers' 'DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range,Authorization' always;
    
    if ($request_method = 'OPTIONS') {
        add_header 'Access-Control-Allow-Origin' '*';
        add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
        add_header 'Access-Control-Max-Age' 1728000;
        add_header 'Content-Type' 'text/plain; charset=utf-8';
        add_header 'Content-Length' 0;
        return 204;
    }
}

location /docs {
    add_header 'Access-Control-Allow-Origin' '*' always;
}
```

**For Apache:**
Add to your `.htaccess`:
```apache
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
</IfModule>
```

### Issue 2: HTTP Mixed Content Error
**Problem:** Site is HTTPS but Swagger tries to load HTTP resources.

**Solution:**
Ensure your `.env` has:
```bash
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com
```

### Issue 3: 404 on /docs/api-docs.json
**Problem:** JSON file not found.

**Solution:**
```bash
# Ensure storage directory is writable
chmod -R 755 storage/

# Regenerate docs
php artisan l5-swagger:generate

# Check file exists
ls -la storage/api-docs/api-docs.json
```

### Issue 4: Old URL Still Showing
**Problem:** Swagger still shows old localhost URL.

**Solution:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate
php artisan l5-swagger:generate

# Clear browser cache or use incognito mode
```

---

## 🧪 Testing

### 1. Test API Documentation Access
```bash
curl https://yourdomain.com/api/documentation
# Should return HTML page
```

### 2. Test JSON Endpoint
```bash
curl https://yourdomain.com/docs/api-docs.json
# Should return JSON
```

### 3. Test CORS Headers
```bash
curl -I -X OPTIONS https://yourdomain.com/docs/api-docs.json \
  -H "Origin: https://yourdomain.com" \
  -H "Access-Control-Request-Method: GET"

# Should see Access-Control-Allow-Origin header
```

---

## 📝 Quick Checklist

After deployment, verify:

- [ ] `.env` has correct `APP_URL` (with https://)
- [ ] `.env` has `L5_SWAGGER_GENERATE_ALWAYS=true`
- [ ] Ran `php artisan config:clear`
- [ ] Ran `php artisan l5-swagger:generate`
- [ ] File exists: `storage/api-docs/api-docs.json`
- [ ] Storage directory has proper permissions (755)
- [ ] Web server (Nginx/Apache) allows CORS
- [ ] SSL certificate is valid (if using HTTPS)
- [ ] No mixed content warnings in browser console

---

## 🎯 Expected Result

After applying fixes:

1. Navigate to: `https://yourdomain.com/api/documentation`
2. Swagger UI loads successfully
3. All endpoints are visible
4. "Try it out" buttons work
5. No CORS errors in browser console

---

## 🔐 Security Notes

### Production Settings
For production, consider restricting CORS:

**config/cors.php:**
```php
'paths' => ['api/*', 'docs/*', 'api/documentation'],
'allowed_origins' => [
    env('APP_URL'),
    'https://yourdomain.com',
],
'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
```

### Disable in Production (Optional)
If you want to disable Swagger in production:

**.env:**
```bash
L5_SWAGGER_GENERATE_ALWAYS=false
```

Add middleware protection in `config/l5-swagger.php`:
```php
'middleware' => [
    'api' => ['auth:sanctum', 'role:admin'],
],
```

---

## 📚 Additional Configuration

### Custom Swagger UI Configuration

**config/l5-swagger.php:**
```php
'documentations' => [
    'default' => [
        'api' => [
            'title' => 'City Shop Directory API',
        ],
        'routes' => [
            'api' => 'api/documentation',
        ],
        'paths' => [
            'use_absolute_path' => true,
            'docs_json' => 'api-docs.json',
            'format_to_use_for_docs' => 'json',
        ],
    ],
],
```

---

## 🛠️ Development vs Production

### Development (.env)
```bash
APP_URL=http://localhost:8000
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_USE_ABSOLUTE_PATH=false
```

### Production (.env)
```bash
APP_URL=https://yourdomain.com
L5_SWAGGER_GENERATE_ALWAYS=false
L5_SWAGGER_USE_ABSOLUTE_PATH=true
```

---

## 📞 Support

If issues persist:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Check browser console for detailed errors
4. Verify SSL certificate is valid
5. Test API endpoints directly (without Swagger)

---

## ✅ Summary

The CORS issue has been fixed by:
1. ✅ Adding proper CORS configuration
2. ✅ Updating Swagger to use dynamic server URL
3. ✅ Adding CORS middleware to API routes
4. ✅ Configuring proper base path

**Next Steps:**
1. Update `.env` with your production domain
2. Run: `php artisan l5-swagger:generate`
3. Clear all caches
4. Test the API documentation URL

Your Swagger documentation should now work correctly on the server!
