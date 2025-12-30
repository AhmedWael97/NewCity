# 🚀 UPLOAD CHECKLIST - Fix Server Issues

## Problem Summary
Server shows broken layout because compiled CSS/JS assets are missing.

## ✅ Files to Upload (In Order)

### 1. **CRITICAL: Upload Build Folder** ⭐
```
public/build/
├── manifest.json
└── assets/
    ├── app-BKTqAgWQ.css
    └── app-CAiCLEjY.js
```
**Upload the ENTIRE folder** via FTP/cPanel File Manager.

### 2. **Upload Updated Layout**
```
resources/views/layouts/app.blade.php
```

### 3. **Upload Test & Clear Files**
```
public/test-assets.php
public/clear-cache.php
```

### 4. **Verify .env on Server**
Make sure your server's `.env` file contains:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://senueg.com
```

## 📋 Step-by-Step Instructions

### Step 1: Upload via FTP/cPanel
1. Open FileZilla, WinSCP, or cPanel File Manager
2. Connect to your server
3. Navigate to your Laravel installation folder (where `public` folder is)
4. Upload `public/build/` folder (keep folder structure)
5. Upload `resources/views/layouts/app.blade.php`
6. Upload `public/test-assets.php`
7. Upload `public/clear-cache.php`

### Step 2: Test Upload
Visit: `https://senueg.com/test-assets.php`

You should see:
- ✅ Build Folder: موجود
- ✅ Manifest File: موجود  
- ✅ CSS File: موجود
- ✅ JS File: موجود
- ✅ APP_ENV Setting: Set to production

### Step 3: Clear Cache
Visit: `https://senueg.com/clear-cache.php?pass=SenuClearCache2024`

(Or change password in the file first)

### Step 4: Test Website
1. Visit: `https://senueg.com`
2. Press `Ctrl + Shift + R` (hard refresh)
3. Website should look like localhost now! 🎉

### Step 5: Cleanup
Delete these files from server (security):
- `public/test-assets.php`
- `public/clear-cache.php`

## 🔧 If Still Not Working

### Check 1: File Permissions
Set correct permissions on server:
```
Folders: 755
Files: 644
```

### Check 2: Verify Files Uploaded
In cPanel File Manager, check:
- `public/build/manifest.json` exists
- `public/build/assets/app-BKTqAgWQ.css` exists
- `public/build/assets/app-CAiCLEjY.js` exists

### Check 3: Browser Cache
- Clear browser cache
- Try incognito/private window
- Try different browser

### Check 4: Server Logs
Check error logs in cPanel for any errors.

## 📞 Quick Debug Commands

If you have SSH access:
```bash
cd /path/to/your/laravel
ls -la public/build/
cat public/build/manifest.json
php artisan optimize:clear
```

## 🎯 Expected Result

After following these steps, your production site at `https://senueg.com` should look exactly like your localhost. The issue is simply that the compiled CSS/JS files weren't on the server.

## ⚠️ Important Notes

- Always run `npm run build` locally before uploading
- Never edit files directly on server
- Always upload the entire `build` folder
- Clear cache after uploading
- Hard refresh browser (Ctrl+Shift+R)

---

**Need Help?**
If still having issues after following all steps, share the output of `test-assets.php`
