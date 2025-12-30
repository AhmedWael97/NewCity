# Marketplace Quick Start Guide

## 🚀 Getting Started

### Prerequisites
1. Laravel application running
2. Database migrations completed
3. Storage link configured: `php artisan storage:link`
4. User authentication system active
5. Admin role/middleware configured
6. Bootstrap 5 and Font Awesome in your layout

### Access URLs
- **Public Marketplace**: `/marketplace`
- **Create Item** (Auth Required): `/marketplace/create`
- **My Items Dashboard**: `/marketplace/my-items`
- **Admin Panel**: `/admin/marketplace`

---

## 👥 For Users

### How to Create an Item

1. **Login** to your account
2. Navigate to `/marketplace`
3. Click the **"إضافة إعلان جديد"** button
4. Fill out the form:
   - **Title**: Name of your item
   - **Description**: Detailed description (minimum 20 characters)
   - **Price**: Your asking price in EGP
   - **Negotiable**: Check if price is negotiable
   - **City**: Select your city
   - **Category**: Select item category
   - **Condition**: Choose (New/Like New/Good/Fair)
   - **Contact Phone**: Auto-filled from your profile
   - **WhatsApp**: Optional WhatsApp number
   - **Images**: Upload 1-5 images (max 5MB each)
5. Click **"نشر الإعلان"**
6. Your item will have status **"Pending"** until admin approves

### Understanding Item Status

| Status | Meaning | What You Can Do |
|--------|---------|-----------------|
| 🟡 **Pending** | Waiting for admin approval | View, Delete |
| 🟢 **Active** | Live and visible to public | View, Edit, Sponsor, Mark as Sold, Delete |
| 🔴 **Rejected** | Admin rejected (see reason) | View reason, Delete, Recreate |
| ⚫ **Sold** | Item marked as sold | View, Delete |

### Managing Your Items

**Access Your Dashboard**: `/marketplace/my-items`

From here you can:
- ✅ View all your items
- ✅ See view counts and remaining views
- ✅ Check sponsorship status
- ✅ Edit active items
- ✅ Mark items as sold
- ✅ Delete items
- ✅ Sponsor items to boost visibility

### Understanding View Limits

- **Free Views**: Every item gets 50 free views
- **View Tracking**: Each time someone views your item, the counter increases
- **Hidden When Exhausted**: When you reach 50 views, item becomes hidden
- **Solution**: Purchase sponsorship to add more views

**View Counter Colors**:
- 🟢 **Green** (20+ remaining): Healthy
- 🟡 **Yellow** (10-20 remaining): Warning - consider sponsoring
- 🔴 **Red** (<10 remaining): Critical - sponsor soon!

### Sponsorship Packages

When your item needs more visibility, purchase a sponsorship:

#### 📦 Basic Package - 50 EGP
- ⏰ **7 days** duration
- 👁️ **+100 views** added
- ⭐ Priority level: 3
- ✨ "Featured" badge
- 🎯 Best for: Quick boost

#### 📦 Standard Package - 90 EGP ⭐ RECOMMENDED
- ⏰ **15 days** duration
- 👁️ **+250 views** added
- ⭐ Priority level: 6
- ✨ "Featured" badge
- 🎯 Best for: Most users (best value)

#### 💎 Premium Package - 150 EGP
- ⏰ **30 days** duration
- 👁️ **+500 views** added
- ⭐ Priority level: 10
- ✨ "Featured" badge
- 🎯 Best for: High-value items

**How to Sponsor**:
1. Go to `/marketplace/my-items`
2. Find your item
3. Click the **"رعاية"** button (appears when views are low)
4. Choose a package
5. Click **"اشترك الآن"**
6. Your item immediately gets:
   - Additional views
   - Higher ranking in search
   - "Featured" badge
   - Extended visibility

---

## 👨‍💼 For Admins

### Accessing Admin Panel

Navigate to: `/admin/marketplace`

### Review Pending Items

1. Use **Status filter** → Select "Pending"
2. Click **"بحث"** to filter
3. You'll see all items awaiting approval

### Approving an Item

**Method 1: From List**
1. Find the item in the list
2. Click the **green checkmark** button (✓)
3. Item status changes to "Active"
4. Item appears in public marketplace

**Method 2: From Detail View**
1. Click **"View"** (eye icon) on any item
2. Review all details (images, description, user info)
3. Click **"الموافقة على الإعلان"** button
4. Confirm the action

### Rejecting an Item

**Method 1: From List**
1. Find the item
2. Click the **yellow X** button
3. Modal opens
4. Enter **rejection reason** (required)
5. Click **"رفض"**

**Method 2: From Detail View**
1. Click **"View"** on the item
2. Click **"رفض الإعلان"** button
3. Enter rejection reason
4. Submit

**Important**: The rejection reason is visible to the user, so be clear and professional.

### Bulk Actions

For processing multiple items at once:

1. Check the boxes next to items you want to process
2. Or click the header checkbox to select all
3. Choose action from **"الإجراء الجماعي"** dropdown:
   - **Approve**: Approve all selected
   - **Reject**: Reject all (enter reason)
   - **Delete**: Permanently delete all
4. Click **"تنفيذ الإجراء"**
5. Confirm the action

### Filtering Items

Use the filter form to find specific items:

- **Search**: Search by title, description, or user name
- **Status**: Filter by Active/Pending/Rejected/Sold
- **City**: Filter by specific city
- **Category**: Filter by category
- **Sponsored**: Show only sponsored/non-sponsored items

Click **"إعادة"** to reset all filters.

### Deleting Items

**From List**:
1. Click the **red trash** button
2. Confirm deletion

**From Detail View**:
1. Scroll to **"منطقة الخطر"** card
2. Click **"حذف الإعلان نهائياً"**
3. Confirm (cannot be undone)

**Note**: Deletion automatically removes item images from storage.

---

## 🔍 For Visitors (Public)

### Browsing the Marketplace

**URL**: `/marketplace`

### Using Filters

**Left Sidebar**:
- **Search**: Type keywords
- **City**: Select specific city
- **Category**: Select category
- **Price Range**: Set min/max price
- **Condition**: Filter by item condition

Click **"تطبيق الفلاتر"** to apply, **"إعادة تعيين"** to clear.

### Viewing Item Details

1. Click on any item card
2. You'll see:
   - Image gallery
   - Full description
   - Price and condition
   - Seller contact info
   - Location

**Note**: Each view counts toward the item's view limit.

### Contacting Sellers

On item detail page:
- Click **"اتصال"** to call the phone number
- Click **"واتساب"** to message on WhatsApp

Both actions increment the contact counter for the seller.

---

## 📊 Understanding the Dashboard

### User Dashboard Statistics

At `/marketplace/my-items`, bottom cards show:

1. **Active Items**: Items currently live
2. **Pending Review**: Items waiting for admin
3. **Sponsored Items**: Items with active sponsorship
4. **Total Views**: Sum of all views on your items

### Item Table Columns

| Column | What It Shows |
|--------|---------------|
| **الإعلان** | Thumbnail + Title + Location |
| **السعر** | Price + Negotiable flag |
| **الحالة** | Status badge (color coded) |
| **المشاهدات** | Current views / Max views (with progress bar) |
| **الرعاية** | Sponsorship status + days remaining |
| **الإجراءات** | Action buttons (View/Edit/Sponsor/Sold/Delete) |

---

## 🎨 Visual Guide

### Status Badges

- 🟢 **Green (Active)**: Item is live
- 🟡 **Yellow (Pending)**: Waiting for review
- 🔴 **Red (Rejected)**: Admin rejected
- ⚫ **Gray (Sold)**: Item sold

### Condition Badges

- 🟢 **Green (New)**: Brand new
- 🔵 **Blue (Like New)**: Excellent condition
- 🟡 **Yellow (Good)**: Good condition
- 🟠 **Orange (Fair)**: Acceptable condition

### Sponsored Badge

Items with active sponsorship show a **gradient purple badge** with star icon.

---

## ⚠️ Important Notes

1. **Admin Approval Required**: All new items need admin approval before going live
2. **View Limit**: Items become hidden after exhausting free views unless sponsored
3. **Image Limit**: 1-5 images required, max 5MB each
4. **Contact Info**: Your phone number is auto-filled from your profile
5. **Edit Restrictions**: Can only edit items you own, and not after marked as sold
6. **Rejection Reasons**: Visible to item owners, be professional
7. **Sponsorship**: Cannot be cancelled once purchased, but can be renewed
8. **Sold Items**: Cannot be edited after marking as sold

---

## 🐛 Troubleshooting

### "Unable to upload images"
- Check file size (max 5MB per image)
- Ensure storage link is configured: `php artisan storage:link`
- Verify storage/marketplace/ directory exists and is writable

### "403 Forbidden" when editing
- Only the item owner can edit
- Login required for all authenticated actions
- Admins use admin panel, not user edit form

### "Item not appearing in marketplace"
- Check if status is "Active" (needs admin approval)
- Check if view limit reached (becomes hidden)
- Check if city/category filters are hiding it

### "Can't sponsor my item"
- Must be item owner
- Must be logged in
- Item must exist and be owned by you

---

## 📞 Support

If you encounter issues:
1. Check this guide first
2. Verify all prerequisites are met
3. Check Laravel logs: `storage/logs/laravel.log`
4. Contact system administrator

---

## 🎉 Tips for Success

### For Users:
- ✅ Use high-quality images (first image is most important)
- ✅ Write detailed, honest descriptions
- ✅ Price competitively (enable negotiable if flexible)
- ✅ Sponsor items with low remaining views
- ✅ Choose Standard package for best value
- ✅ Respond quickly to contacts

### For Admins:
- ✅ Review items daily to prevent backlog
- ✅ Provide clear rejection reasons
- ✅ Use bulk actions for efficiency
- ✅ Monitor statistics regularly
- ✅ Look for duplicate or spam items
- ✅ Be consistent with approval criteria

---

**Last Updated**: 2025-01-08  
**Version**: 1.0
