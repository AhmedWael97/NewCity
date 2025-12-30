# FCM HTTP v1 API Setup (Required for Android/iOS/Web)

## The Problem

- ❌ Legacy FCM API (`/fcm/send`) is **deprecated** and returns 404
- ❌ HTTP v1 API requires **OAuth 2 token**, not API key
- ✅ Need to use **Service Account** for authentication

## Solution: Download Service Account JSON

### Step 1: Download Service Account Key

1. Go to: https://console.firebase.google.com/project/senu-66fb2/settings/serviceaccounts/adminsdk
2. Click **"Generate new private key"**
3. Click **"Generate key"** in the confirmation dialog
4. A JSON file will download (e.g., `senu-66fb2-firebase-adminsdk-xxxxx.json`)
5. **IMPORTANT**: Keep this file secure, never commit to git!

### Step 2: Place the JSON File

Save the file in your Laravel project:
```
storage/app/firebase/
```

Create the directory:
```bash
mkdir storage/app/firebase
```

Move the downloaded JSON file there and rename it:
```bash
# Example:
move Downloads\senu-66fb2-firebase-adminsdk-xxxxx.json storage\app\firebase\service-account.json
```

### Step 3: Update .env

Add this line to your `.env`:
```env
FIREBASE_CREDENTIALS=storage/app/firebase/service-account.json
```

### Step 4: Install Google Client Library

```bash
composer require google/apiclient:"^2.0"
```

### Step 5: I'll Update the NotificationService

Once you complete steps 1-4, I'll update the code to use the service account for OAuth authentication.

---

## Alternative: Simple Workaround (Web Only - Temporary)

For **testing web notifications only** without mobile apps, you can use a **polling system**:

### How it works:
1. Admin creates notification in database
2. Browser checks every 5 seconds for new notifications
3. JavaScript displays the notification using the Web Notification API
4. No FCM server API needed!

### Pros:
- ✅ Works immediately, no additional setup
- ✅ No service account needed
- ✅ Good for testing

### Cons:
- ❌ Won't work for mobile apps (Android/iOS)
- ❌ Slight delay (5 seconds)
- ❌ Requires browser to be open

---

## Recommendation

**For Production (Android + iOS + Web):**
- Use Service Account method (Steps 1-5 above)
- Proper FCM HTTP v1 API
- Real-time push for all platforms

**For Testing (Web Only):**
- Use polling workaround
- Test the notification system
- Migrate to service account later

Which approach do you want to use?
