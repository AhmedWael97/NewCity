# Google Maps Setup Guide

## Issue Fixed ✅

The `/admin/shops-map` page was not working because the Google Maps API key was **hardcoded** in the view file. This has been fixed to use Laravel's environment configuration.

## What Was Changed

### 1. Configuration Structure (`config/services.php`)
Added Google Maps configuration:
```php
'google_maps' => [
    'api_key' => env('GOOGLE_MAPS_API_KEY', ''),
],
```

### 2. View File (`resources/views/admin/shops/map.blade.php`)
- ❌ **Before**: Hardcoded API key `AIzaSyCeaKlnTU88qhTp7za2H301HWPPT7zhGyo`
- ✅ **After**: Dynamic key from config with validation

### 3. Environment Template (`.env.example`)
Added reference for Google Maps API key configuration.

---

## Setup Instructions

### Step 1: Get Your Google Maps API Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Go to **APIs & Services** → **Credentials**
4. Click **Create Credentials** → **API Key**
5. Copy your new API key

### Step 2: Enable Required APIs

Go to **APIs & Services** → **Library** and enable these APIs:

1. **Maps JavaScript API** ✅
2. **Places API (New)** ✅
3. **Geocoding API** ✅
4. **Drawing API** (optional, already enabled by Maps JavaScript API)

**Important**: It may take a few minutes for newly enabled APIs to become active.

### Step 3: Configure Billing

**Required**: The Places API requires a billing account to work (even with free tier).

1. Go to **Billing** in Google Cloud Console
2. Add a payment method
3. Google provides **$200 free credit per month**
4. The free tier should be sufficient for most usage

### Step 4: Add API Key to .env File

Open your `.env` file and add:

```env
GOOGLE_MAPS_API_KEY=AIzaSy...your_actual_key_here
```

**Note**: Replace `AIzaSy...your_actual_key_here` with your actual API key from Step 1.

### Step 5: Clear Configuration Cache

Run this command in your terminal:

```bash
php artisan config:clear
```

### Step 6: Test the Map

1. Navigate to: `http://127.0.0.1:8000/admin/shops-map`
2. Click **Auto-Detect My Location** or search for a city
3. Draw a circle on the map
4. Select shop types and click **Search Places**
5. Click **Import** to save shops to your database

---

## Troubleshooting

### Problem: API Key Error Message Shows

**Check:**
- Is `GOOGLE_MAPS_API_KEY` in your `.env` file?
- Did you run `php artisan config:clear`?
- Is the API key correct (no extra spaces)?

### Problem: "REQUEST_DENIED" in Debug Console

**Solutions:**
- Enable all required APIs (see Step 2)
- Add billing to your Google Cloud project
- Wait 5-10 minutes after enabling APIs
- Check if API key has restrictions (should allow localhost)

### Problem: "OVER_QUERY_LIMIT"

**Solutions:**
- You've exceeded the free tier quota
- Check usage in Google Cloud Console
- Enable billing if not already enabled
- Wait until quota resets (daily limit)

### Problem: "ZERO_RESULTS"

**This is normal** - it means:
- No shops found in the selected area
- Try a different location or larger circle
- Try different shop types

### Problem: Map Doesn't Load at All

**Check:**
1. Browser console for JavaScript errors (F12)
2. Network tab shows Maps API script loading
3. `.env` file has the correct API key
4. Config cache is cleared

---

## Debug Console

The shops map page has a built-in **debug console** (black box at the bottom) that shows:

- ✅ Configuration status
- 📍 Location coordinates
- 🔍 Search parameters (radius, types)
- 📊 Search results
- ❌ Error messages with details

**Use this console to diagnose issues in real-time.**

---

## API Key Security (Optional)

For production, consider restricting your API key:

### HTTP Referrer Restrictions
1. Go to **APIs & Services** → **Credentials**
2. Click your API key
3. Under **Application restrictions**, select **HTTP referrers**
4. Add your domain: `yourdomain.com/*`

### API Restrictions
1. Under **API restrictions**, select **Restrict key**
2. Select only the APIs you need:
   - Maps JavaScript API
   - Places API (New)
   - Geocoding API

---

## Features Available

Once configured, you can:

1. **Auto-detect location** using browser geolocation
2. **Search for cities** worldwide
3. **Draw search radius** on map (adjustable circle)
4. **Select shop types** (restaurants, cafes, gyms, etc.)
5. **Search Places API** within the selected area
6. **Preview results** before importing
7. **Bulk import shops** to your database
8. **View imported shops** on the map with green markers

---

## Cost Estimates

### Free Tier (Monthly)
- **$200 free credit** from Google
- Maps JavaScript API: $7 per 1,000 loads
- Places API: $17 per 1,000 requests
- Geocoding API: $5 per 1,000 requests

### Typical Usage
For a small to medium application:
- ~5,000 map loads = $35
- ~1,000 place searches = $17
- ~500 geocoding requests = $2.50
- **Total**: ~$54.50/month (covered by $200 credit)

**You'll stay within the free tier for most use cases.**

---

## Testing Checklist

After setup, verify these work:

- [ ] Map loads without errors
- [ ] Auto-detect location works
- [ ] City search works
- [ ] Drawing circle on map works
- [ ] Shop type selection works
- [ ] Search Places button returns results
- [ ] Import button saves to database
- [ ] Debug console shows no errors

---

## Need Help?

If you're still experiencing issues after following this guide:

1. **Check the debug console** on the page
2. **Check browser console** (F12) for JavaScript errors
3. **Verify Google Cloud Console** shows enabled APIs
4. **Wait 10 minutes** after enabling APIs (propagation time)
5. **Check billing** is configured correctly

---

## Summary

**Quick Start:**
```bash
# 1. Add to .env
GOOGLE_MAPS_API_KEY=your_key_here

# 2. Clear cache
php artisan config:clear

# 3. Test
# Navigate to http://127.0.0.1:8000/admin/shops-map
```

**The hardcoded API key issue has been fixed. You now have full control over your Google Maps configuration through the `.env` file.**
