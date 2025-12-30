# Firebase Server Key Missing - How to Fix

## Problem
Notifications are failing because the `FIREBASE_SERVER_KEY` is not configured.

## ⚠️ Important: Legacy API Deprecated

Google has deprecated the **Cloud Messaging API (Legacy)** and removed the Server Key from newer Firebase projects. You need to enable it manually or use an alternative method.

## Solution 1: Enable Legacy Cloud Messaging API

### Step 1: Enable Cloud Messaging API (Legacy)

The Server Key is no longer shown by default. You need to enable it:

1. Go to https://console.cloud.google.com/apis/dashboard
2. Make sure your project `senu-66fb2` is selected (check the dropdown at the top)
3. Click **"+ ENABLE APIS AND SERVICES"** button
4. Search for **"Firebase Cloud Messaging API"**
5. Click on it
6. Click the **"ENABLE"** button
7. Wait a few moments for it to enable

### Step 2: Get Server Key from Firebase Console

Now go back to Firebase Console:

1. Go to https://console.firebase.google.com
2. Select your project: `senu-66fb2`
3. Click the gear icon ⚙️ → **Project settings**
4. Go to the **Cloud Messaging** tab
5. Scroll down - you should now see **Cloud Messaging API (Legacy)** section
6. Copy the **Server key** (starts with `AAAA...`)

### Step 2: Update .env File

Open `.env` and update line 68:

```env
FIREBASE_SERVER_KEY=YOUR_ACTUAL_SERVER_KEY_HERE
```

Example:
```env
FIREBASE_SERVER_KEY=AAAAxxxxxxx:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Step 3: Clear Config Cache

```powershell
php artisan config:clear
```

### Step 4: Test Again

```powershell
php test-send.php
```

You should now see:
```
Success Count: 1
Failure Count: 0
```

---

## ⚠️ Important Note

The **Cloud Messaging API (Legacy)** will be deprecated by Google. For production, you should migrate to **Firebase Cloud Messaging HTTP v1 API** which uses a service account JSON file.

---

## Alternative: If You Can't Find Server Key

If you still don't see the Server Key after enabling the API:

### Option A: Use Sender ID as Temporary Solution

**NOT RECOMMENDED but can work as a test:**

The Sender ID you see in Firebase Console is actually the **Messaging Sender ID**, not the server key. However, you can try using your project's API key instead:

1. In Firebase Console → Project Settings → General tab
2. Under "Your apps" section, find your Web App
3. Look for "Web API Key" 
4. Copy that key and use it temporarily

### Option B: Create a New Server Key

1. Go to https://console.cloud.google.com
2. Select your project
3. Go to **APIs & Services** → **Credentials**
4. Click **"+ CREATE CREDENTIALS"**
5. Select **"API key"**
6. Copy the generated API key
7. Click "Edit API key" (pencil icon)
8. Under "API restrictions", select "Restrict key"
9. Choose "Firebase Cloud Messaging API"
10. Save
11. Use this API key as your FIREBASE_SERVER_KEY

---

## How to Get Your Server Key

The server key looks like this:
```
AAAAxxxxxxx:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

It starts with `AAAA` and contains letters, numbers, and special characters.

---

## Quick Test After Setup

```powershell
# Run the diagnostic
php artisan notifications:check

# Should show server key configured
# Then test sending
php test-send.php

# Check if notification was received in browser
```

---

## Troubleshooting

### Error: "Cloud Messaging API (Legacy) is disabled"

If you get a 403 error or see that the API is disabled:

1. Go to https://console.cloud.google.com
2. Select your project
3. Go to **APIs & Services** → **Library**
4. Search for "Firebase Cloud Messaging API"
5. Click on it and click **Enable**

### Error: "Server Key not found"

Make sure you're looking at the correct project in Firebase Console.

### Still Getting 404 Error?

1. Verify the server key is correct (starts with AAAA)
2. Check if Cloud Messaging API is enabled
3. Make sure you saved the `.env` file
4. Run `php artisan config:clear` again

---

## Current Setup Status

✅ VAPID Key: Configured  
❌ Server Key: **NOT CONFIGURED** ← Fix this now

Once you add the server key, notifications will work!
