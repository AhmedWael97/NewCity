# Admin Dashboard - Quick Access Guide

## 🎯 Main Features

### 1. City Banners (إعلانات المدن)
**Location:** تخصيص المدن → إعلانات المدن

**Quick Actions:**
- ➕ Create Banner: Click "إضافة إعلان جديد"
- ✏️ Edit: Click edit icon in actions column
- 🔄 Toggle Status: Click status badge
- 🗑️ Delete: Click delete icon (confirms first)

**Recommended Image Size:** 800x400px (2:1 ratio)

---

### 2. Featured Shops (المتاجر المميزة)
**Location:** المتاجر → Actions → إدارة الميزة

**Quick Actions:**
- ⭐ Quick Feature: Actions → "إبراز" (30 days default)
- ⚙️ Manage Featured: Actions → "إدارة الميزة" (full control)
- 📅 Set Duration: Use quick buttons (7/14/30/60/90 days)
- 🎯 Set Priority: 0-100 (higher = shown first)

**Priority Levels:**
- 80-100: Critical (special offers)
- 50-79: High (paid featured)
- 20-49: Medium (active shops)
- 0-19: Low (new shops)

---

### 3. Landing Page Theme (الصفحة الرئيسية)
**Location:** تخصيص المدن → تصاميم المدن → الصفحة الرئيسية

**Quick Actions:**
- 🎨 Colors: Use color pickers (live preview)
- 👁️ Sections: Toggle on/off with switches
- 📊 Limits: Adjust featured/latest shop counts
- 💾 Save: Changes apply immediately

**Available Options:**
- Colors: Primary, Secondary, Accent
- Banner Style: Carousel / Slider / Grid
- Category Style: Grid / List / Carousel
- Section Toggles: Featured / Latest / Statistics

---

## 🚀 Quick Workflows

### Create a Promotional Banner:
1. Navigate to إعلانات المدن
2. Click "إضافة إعلان جديد"
3. Select city from dropdown
4. Enter title (e.g., "Summer Sale 2024")
5. Upload image (max 2MB)
6. Choose link type:
   - Internal: `/shops/featured`
   - External: `https://example.com`
   - None: Display only
7. Set date range (or leave end date empty for permanent)
8. Set priority (10 = default)
9. Check "تفعيل الإعلان"
10. Click "حفظ الإعلان"

### Make a Shop Featured:
1. Go to المتاجر
2. Find shop in list
3. Click Actions dropdown (⋮)
4. Click "إدارة الميزة"
5. Check "متجر مميز"
6. Set priority (default: 10)
7. Click quick duration button (e.g., "30 يوم")
8. Click "حفظ الإعدادات"

### Customize City Landing Page:
1. Go to تخصيص المدن → تصاميم المدن
2. Find city card
3. Click "الصفحة الرئيسية"
4. Pick colors using color pickers
5. Toggle sections on/off
6. Adjust shop limits
7. Select display styles
8. Click "حفظ الإعدادات"

---

## 📍 Navigation Paths

```
Admin Dashboard
└── تخصيص المدن (City Customization)
    ├── تصاميم المدن (City Designs)
    │   ├── تصميم المدينة (City Theme)
    │   ├── الصفحة الرئيسية (Landing Page) ⭐ NEW
    │   └── الإعلانات (Banners)
    └── إعلانات المدن (City Banners) ⭐ NEW

└── المتاجر (Shops)
    └── Actions
        ├── إدارة الميزة (Manage Featured) ⭐ NEW
        └── إبراز (Feature - Quick)
```

---

## 🎨 City Landing Page Structure

```
┌─────────────────────────────────┐
│     City Header (Theme Colors)  │
├─────────────────────────────────┤
│  📸 Promotional Banners         │  ← City Banners
│  (Managed in إعلانات المدن)     │
├─────────────────────────────────┤
│  📊 Statistics Widget           │  ← Toggle in Landing Page
├─────────────────────────────────┤
│  ⭐ Featured Shops              │  ← Featured Shops
│  (Managed in إدارة الميزة)      │
├─────────────────────────────────┤
│  🆕 Latest Shops                │  ← Latest Shops
├─────────────────────────────────┤
│  📑 Categories                  │  ← Category Style
└─────────────────────────────────┘
```

---

## 💡 Tips & Best Practices

### For Banners:
- ✅ Use high-quality images (800x400px)
- ✅ Set appropriate priority (10 = standard)
- ✅ Add end date for time-limited promotions
- ✅ Use descriptive titles
- ✅ Test links before activating

### For Featured Shops:
- ✅ Rotate featured shops regularly
- ✅ Use higher priority for partners/paid listings
- ✅ Set expiration dates for fairness
- ✅ Monitor featured shop count per city
- ✅ Feature high-rated shops for better UX

### For Landing Page Theme:
- ✅ Choose contrasting colors for readability
- ✅ Enable statistics for engagement
- ✅ Keep featured limit reasonable (10-15)
- ✅ Test on mobile after changes
- ✅ Use carousel for multiple banners

---

## 🔍 Finding Features

### Can't find something?
- **City Banners:** تخصيص المدن → إعلانات المدن
- **Featured Shops:** المتاجر → Shop Actions → إدارة الميزة
- **Landing Page:** تخصيص المدن → تصاميم المدن → الصفحة الرئيسية
- **Mobile App Settings:** Already exists (إعدادات التطبيق)

### Need help?
- Check `ADMIN_DASHBOARD_CONTROLS.md` for detailed docs
- View `CITY_LANDING_PAGE_IMPLEMENTATION.md` for API info
- Access Swagger docs at `/api/documentation`

---

## ⚡ Keyboard Shortcuts

While no custom shortcuts are implemented, standard browser shortcuts work:
- `Ctrl + S` - Save form (when in input field)
- `Ctrl + Click` - Open in new tab (for preview links)
- `Esc` - Close modals/dialogs

---

## 📊 Quick Stats

### Check Current Status:
- **City Banners:** See count in admin.city-banners.index
- **Featured Shops:** Filter shops by is_featured=1
- **Theme Config:** View in landing page editor sidebar

### View City Statistics:
1. Go to Landing Page editor
2. Check "معلومات" card in sidebar
3. See:
   - Featured shops count
   - Latest shops (30 days)
   - Active banners count

---

## ✅ Quick Checklist

Before launching a city landing page:
- [ ] Upload at least 1 active banner
- [ ] Feature at least 5-10 shops
- [ ] Configure theme colors
- [ ] Enable all sections
- [ ] Test banner links
- [ ] Preview on mobile
- [ ] Check API response

---

**Last Updated:** November 10, 2025  
**Version:** 1.0  
**Status:** Production Ready
