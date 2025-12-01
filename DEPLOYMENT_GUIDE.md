# Deployment Guide for Shared Hosting

## Important: Building Assets Before Upload

Since you cannot run `npm run build` on shared hosting, you must build assets **locally** before uploading.

## Step-by-Step Deployment Process

### 1. Build Assets Locally (Before Each Upload)

```bash
# Run this command in your local project directory
npm run build
```

This will create/update files in: `public/build/`

### 2. Files to Upload to Server

Upload these folders/files via FTP/cPanel File Manager:

#### Required Build Files (MUST upload every time you change CSS/JS):
```
public/build/
├── manifest.json
└── assets/
    ├── app-[hash].css
    └── app-[hash].js
```

#### Updated View Files:
```
resources/views/layouts/app.blade.php
(and any other views you modified)
```

#### Configuration Files:
```
.env (with APP_ENV=production)
```

### 3. Server Configuration

Make sure your `.env` file on the server has:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://senueg.com

# This tells Vite to use compiled assets instead of dev server
VITE_APP_URL=https://senueg.com
```

### 4. Clear Cache on Server

If your hosting provides PHP command access through cPanel, run:

```bash
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

Or create a file `clear-cache.php` in your public folder and visit it once:

```php
<?php
// clear-cache.php - Delete after use!
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('optimize:clear');
echo "Cache cleared successfully!";
```

### 5. Verify Upload

After uploading, check that these files exist on server:
- `public/build/manifest.json`
- `public/build/assets/app-[hash].css`
- `public/build/assets/app-[hash].js`

## Troubleshooting

### CSS/JS Not Loading (MIME Type Error)

**Symptoms:**
```
Refused to execute script because its MIME type ('text/html') is not executable
```

**Causes:**
1. ❌ `/public/build/` folder not uploaded
2. ❌ `.htaccess` file missing or incorrect
3. ❌ Vite dev server URL in production

**Solutions:**

1. **Upload build folder completely**
   - Make sure `public/build/` and all its contents are on the server
   - Check file permissions (644 for files, 755 for folders)

2. **Verify .htaccess exists in public folder**
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [L]
   </IfModule>
   ```

3. **Check APP_ENV in .env**
   ```env
   APP_ENV=production  # NOT local
   ```

### Styles Not Matching

If localhost looks different from server:

1. **Rebuild assets locally:**
   ```bash
   npm run build
   ```

2. **Re-upload entire `public/build/` folder**

3. **Hard refresh browser:** `Ctrl+Shift+R` (Windows) or `Cmd+Shift+R` (Mac)

## Quick Deployment Checklist

Before uploading to server, ensure:

- [ ] Run `npm run build` locally
- [ ] Check `public/build/manifest.json` exists
- [ ] Check `public/build/assets/` has CSS and JS files
- [ ] Upload `public/build/` folder completely
- [ ] Upload modified view files
- [ ] Verify `.env` has `APP_ENV=production`
- [ ] Clear cache on server
- [ ] Test website with hard refresh (Ctrl+Shift+R)

## File Upload Methods

### Option 1: FTP Client (FileZilla, WinSCP)
1. Connect to your server
2. Navigate to your Laravel root
3. Upload `public/build/` folder
4. Maintain folder structure

### Option 2: cPanel File Manager
1. Login to cPanel
2. Open File Manager
3. Navigate to `public_html` (or your Laravel public folder)
4. Upload `build.zip` (create zip of build folder locally)
5. Extract on server
6. Delete zip file

### Option 3: Git Deployment (if available)
```bash
# Locally
git add public/build/
git commit -m "Build assets for production"
git push origin master

# On server (if you have git deployment setup)
git pull origin master
```

## Important Notes

⚠️ **Never edit files directly on the server**
- Always edit locally, build, then upload

⚠️ **Build before every deployment**
- CSS/JS changes won't reflect without rebuilding

⚠️ **Don't upload node_modules**
- Only upload `public/build/` folder

⚠️ **Keep .gitignore updated**
- But DO upload `public/build/` to server manually

## Current Build Files

Your current build includes:
```
public/build/manifest.json
public/build/assets/app-BKTqAgWQ.css
public/build/assets/app-CAiCLEjY.js
```

These files MUST be on your server for the website to work properly.
