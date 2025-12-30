# Google Maps Diagnostic Test - Quick Reference

## 🎯 Purpose
A comprehensive diagnostic tool to identify exactly what's wrong with your Google Maps integration.

## 🔗 Access
Navigate to: **`http://127.0.0.1:8000/admin/shops-map-test`**

Or click the **"اختبار تشخيصي"** button on the shops map page.

---

## 🧪 What This Test Does

### 1. **Configuration Tests** ✅
- Checks if `GOOGLE_MAPS_API_KEY` exists in `.env`
- Validates API key length (should be 39 characters)
- Shows your environment configuration
- Displays app URL and environment

### 2. **API Loading Tests** 🗺️
- Verifies Google Maps SDK loads correctly
- Tests map initialization
- Checks Places Service availability
- Checks Geocoding Service availability

### 3. **Geocoding API Test** 🌍
- Searches for "الرياض، السعودية"
- Shows if Geocoding API is enabled and working
- Displays full API response in JSON

### 4. **Places Text Search Test** 🏪
- Searches for "مطاعم في الرياض"
- Tests Places API (New) functionality
- Shows multiple results on map
- Full JSON response displayed

### 5. **Places Nearby Search Test** 📍
- **This is the critical test** - same as your main page
- Searches for stores within 2km of Riyadh center
- Uses `type: ['store']` parameter
- Shows exactly what the API returns

---

## 📊 What to Look For

### ✅ **Success Indicators**
- Green checkmarks (✓) for all tests
- Map loads with markers
- Console shows status: `OK`
- JSON responses with place data

### ❌ **Error Indicators**

#### 1. **REQUEST_DENIED**
**What it means:** Places API not enabled or billing not set up

**Fix:**
```
1. Go to: https://console.cloud.google.com/apis/library/places-backend.googleapis.com
2. Click "Enable" button
3. Go to: https://console.cloud.google.com/billing
4. Add payment method (gets $200 free/month)
5. Wait 5-10 minutes
6. Reload test page
```

#### 2. **ZERO_RESULTS**
**What it means:** API works, but no places found

**Fix:**
- Try different location
- Expand search radius
- Change search type

#### 3. **OVER_QUERY_LIMIT**
**What it means:** Too many requests, exceeded quota

**Fix:**
- Check quota in Google Cloud Console
- Enable billing if not already enabled
- Wait for quota to reset (daily)

#### 4. **INVALID_REQUEST**
**What it means:** Request parameters are wrong

**Fix:**
- Check the request JSON in API responses section
- Verify latitude/longitude format
- Ensure radius is a number

#### 5. **API Key Missing**
**What it means:** `GOOGLE_MAPS_API_KEY` not in `.env`

**Fix:**
```bash
# Add to .env file:
GOOGLE_MAPS_API_KEY=AIzaSy...your_key_here

# Clear cache:
php artisan config:clear
```

---

## 🎛️ Test Buttons

### 1. **اختبار Geocoding API**
- Converts address to coordinates
- Tests: Geocoding API enabled
- Required for: Address search

### 2. **اختبار Places API** 
- Text-based search
- Tests: Places API (text search)
- Required for: City/restaurant search

### 3. **اختبار Nearby Search**
- Location + radius search
- Tests: Places API (nearby search)
- **THIS IS WHAT YOUR MAIN PAGE USES**

---

## 📋 Understanding the Output

### Live Log Console
Real-time events with color coding:
- 🟢 **Green**: Success
- 🔴 **Red**: Error
- 🟡 **Yellow**: Warning
- 🔵 **Blue**: Info

### Configuration Details (JSON)
```json
{
  "apiKey": "AIzaSy...",
  "apiKeyLength": 39,
  "hasApiKey": true,
  "appUrl": "http://127.0.0.1:8000",
  "appEnv": "local"
}
```

### API Responses (JSON)
Full raw responses from Google APIs showing:
- Place names, addresses, ratings
- Geometry (latitude/longitude)
- Place IDs
- Business status
- Types (restaurant, cafe, etc.)

---

## 🔍 Common Issues & Solutions

### Issue: "No data loaded and no issue"

**Diagnosis Steps:**

1. **Run Nearby Search Test**
   - Click "اختبار Nearby Search" button
   - Check console status

2. **If status = REQUEST_DENIED:**
   ```
   Problem: Places API not enabled or no billing
   Solution: Enable Places API + Add billing account
   ```

3. **If status = ZERO_RESULTS:**
   ```
   Problem: No shops in search area
   Solution: Normal - API is working
   ```

4. **If status = OVER_QUERY_LIMIT:**
   ```
   Problem: Exceeded free quota
   Solution: Enable billing or wait 24 hours
   ```

5. **If no status appears:**
   ```
   Problem: JavaScript error
   Solution: Check browser console (F12)
   ```

---

## 🛠️ Technical Details

### API Key Configuration
```php
// config/services.php
'google_maps' => [
    'api_key' => env('GOOGLE_MAPS_API_KEY', ''),
],

// .env
GOOGLE_MAPS_API_KEY=AIzaSyDBzVcjTSeIpdUHh8fyIiMcsw1nmFRExrc
```

### Test Request Format
```javascript
{
  location: { lat: 24.7136, lng: 46.6753 },
  radius: 2000, // meters
  type: ['store']
}
```

### Expected Response
```javascript
{
  name: "Shop Name",
  vicinity: "123 Street, City",
  geometry: {
    location: { lat: 24.7136, lng: 46.6753 }
  },
  rating: 4.5,
  user_ratings_total: 120
}
```

---

## 📞 Getting Help

### If test shows all green checkmarks but main page doesn't work:

1. **Compare console logs** between test page and main page
2. **Check circle size** on main page (might be too small)
3. **Verify city selection** is correct
4. **Check category selection** has shops assigned

### If test shows red errors:

1. **Screenshot the error** from Live Log Console
2. **Copy the API Responses** section
3. **Note which test failed** (Geocoding, Places, Nearby)
4. **Check Google Cloud Console** for API status

---

## ✅ Success Checklist

Before leaving the test page, verify:

- [ ] All configuration tests pass (green checkmarks)
- [ ] Map loads without errors
- [ ] Geocoding test returns results
- [ ] Places text search returns results
- [ ] **Nearby search returns stores** (critical!)
- [ ] Console shows no red errors
- [ ] API responses show valid JSON data

---

## 🔄 Next Steps After Testing

### If all tests pass:
1. Return to main shops map page
2. Draw a circle on the map
3. Check debug console at bottom
4. Should see shops appear

### If tests fail:
1. Fix the specific failing test first
2. Follow error-specific solutions above
3. Re-run tests until all pass
4. Then try main page

---

## 💡 Pro Tips

1. **Open browser console (F12)** while testing for detailed errors
2. **Use a fresh incognito window** to avoid cache issues
3. **Test on multiple browsers** (Chrome, Edge, Firefox)
4. **Check Network tab** to see actual API requests
5. **Wait 10 minutes** after enabling APIs in Google Cloud

---

## 📚 Related Documentation

- Main setup guide: `GOOGLE_MAPS_SETUP_GUIDE.md`
- Google Cloud Console: https://console.cloud.google.com
- Places API Docs: https://developers.google.com/maps/documentation/places/web-service

---

## 🎓 Understanding Results

### Scenario 1: All Green ✅
```
✓ API Key exists
✓ Google Maps SDK loaded
✓ Map created
✓ Places Service ready
✓ Nearby Search works! Found 15 shops
```
**Result:** Everything working perfectly!

### Scenario 2: REQUEST_DENIED ❌
```
✓ API Key exists
✓ Google Maps SDK loaded
✓ Map created
✗ Nearby Search failed: REQUEST_DENIED
```
**Result:** Places API not enabled or no billing

### Scenario 3: ZERO_RESULTS ⚠️
```
✓ API Key exists
✓ Google Maps SDK loaded
✓ Map created
⚠ Nearby Search: No results in this area
```
**Result:** API works, just no shops in test location

---

**Created:** December 4, 2025  
**Version:** 1.0  
**Purpose:** Diagnose Google Maps Places API issues
