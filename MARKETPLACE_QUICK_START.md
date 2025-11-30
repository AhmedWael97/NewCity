# Marketplace Quick Start Guide

## 🚀 Quick API Reference

### For Users Creating Items

**1. Create New Item**
```bash
POST /api/v1/marketplace
Authorization: Bearer YOUR_TOKEN

{
  "title": "iPhone 14 Pro",
  "description": "Excellent condition, barely used",
  "price": 3500.00,
  "category_id": 3,
  "city_id": 1,
  "condition": "like_new",
  "is_negotiable": true,
  "contact_phone": "+201234567890",
  "images": [file1.jpg, file2.jpg]
}
```

**2. View My Items**
```bash
GET /api/v1/my-marketplace-items
Authorization: Bearer YOUR_TOKEN
```

**3. Update Item**
```bash
PUT /api/v1/marketplace/123
Authorization: Bearer YOUR_TOKEN

{
  "price": 3200.00,
  "description": "Updated description"
}
```

**4. Mark as Sold**
```bash
POST /api/v1/marketplace/123/mark-sold
Authorization: Bearer YOUR_TOKEN
```

---

### For Browsing Items

**1. Browse All Items**
```bash
GET /api/v1/marketplace?city_id=1&category_id=3&sort=newest
```

**2. View Item Details**
```bash
GET /api/v1/marketplace/123
```

**3. Get Sponsored Items Only**
```bash
GET /api/v1/marketplace/sponsored
```

---

### For Sponsoring Items

**1. View Available Packages**
```bash
GET /api/v1/marketplace/sponsorship-packages

Response:
{
  "basic": {
    "price": 50.00,
    "duration_days": 7,
    "views_boost": 100
  },
  "standard": {
    "price": 90.00,
    "duration_days": 15,
    "views_boost": 250
  },
  "premium": {
    "price": 150.00,
    "duration_days": 30,
    "views_boost": 500
  }
}
```

**2. Purchase Sponsorship**
```bash
POST /api/v1/marketplace/123/sponsor
Authorization: Bearer YOUR_TOKEN

{
  "package_type": "standard",
  "payment_method": "credit_card"
}
```

**3. View My Sponsorships**
```bash
GET /api/v1/my-marketplace-sponsorships
Authorization: Bearer YOUR_TOKEN
```

**4. Get Sponsorship Stats**
```bash
GET /api/v1/marketplace/sponsorships/stats
Authorization: Bearer YOUR_TOKEN
```

---

## 🎯 Key Features

### View Limitation System
- **Default:** 50 views per item
- **When limit reached:** Item becomes hidden from public
- **Solution:** Sponsor the item to get more views

### Sponsorship Benefits
1. **Unlimited views** during sponsorship period
2. **Priority placement** in search results
3. **Extra view boost** added to item
4. **Featured badge** display
5. **Extended visibility**

### How It Works
```
Regular Item: 50 views → HIDDEN
             ↓
    Purchase Sponsorship
             ↓
Sponsored Item: 50 + 250 = 300 views
Priority: 6/10 (appears first)
Duration: 15 days
```

---

## 📱 Mobile Integration Example

### Flutter/Dart
```dart
// Fetch items
Future<void> fetchItems() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/marketplace?city_id=1'),
    headers: {'Accept': 'application/json'},
  );
  
  if (response.statusCode == 200) {
    final data = json.decode(response.body);
    // Handle items list
  }
}

// Create item with images
Future<void> createItem(List<File> images) async {
  var request = http.MultipartRequest(
    'POST',
    Uri.parse('$baseUrl/api/v1/marketplace'),
  );
  
  request.headers['Authorization'] = 'Bearer $token';
  request.fields['title'] = 'Item Title';
  request.fields['description'] = 'Description here';
  request.fields['price'] = '100.00';
  request.fields['category_id'] = '1';
  request.fields['city_id'] = '1';
  request.fields['condition'] = 'new';
  
  for (var image in images) {
    request.files.add(
      await http.MultipartFile.fromPath('images', image.path)
    );
  }
  
  var response = await request.send();
}
```

### React/JavaScript
```javascript
// Browse items
const fetchItems = async () => {
  const response = await fetch('/api/v1/marketplace?city_id=1');
  const data = await response.json();
  return data.data;
};

// Create item
const createItem = async (formData) => {
  const response = await fetch('/api/v1/marketplace', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    },
    body: formData // FormData with images
  });
  return await response.json();
};

// Sponsor item
const sponsorItem = async (itemId, packageType) => {
  const response = await fetch(`/api/v1/marketplace/${itemId}/sponsor`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      package_type: packageType,
      payment_method: 'credit_card'
    })
  });
  return await response.json();
};
```

---

## 🎨 UI/UX Recommendations

### Item Card Display
```
┌─────────────────────────────────┐
│  [SPONSORED] 🌟                 │
│  ┌───────────┐                  │
│  │   Image   │  iPhone 14 Pro   │
│  └───────────┘  3,500 EGP       │
│                                  │
│  📍 Cairo  📱 Electronics       │
│  👁 45/300 views remaining      │
│  ⏰ Sponsored: 12 days left     │
└─────────────────────────────────┘
```

### View Limit Warning
```
┌─────────────────────────────────┐
│  ⚠️ Low Views Warning           │
│                                  │
│  Only 5 views remaining!        │
│                                  │
│  Sponsor your item to get:      │
│  • Unlimited views              │
│  • Priority placement           │
│  • Featured badge               │
│                                  │
│  [View Packages] [Dismiss]      │
└─────────────────────────────────┘
```

### Sponsorship Success
```
┌─────────────────────────────────┐
│  ✅ Sponsorship Active!         │
│                                  │
│  Your item is now featured!     │
│  • +250 extra views             │
│  • Priority level: 6/10         │
│  • Valid until: Dec 15, 2025    │
│  • ROI tracking enabled         │
│                                  │
│  [View Statistics] [OK]         │
└─────────────────────────────────┘
```

---

## 🔔 Notification Ideas

1. **Low Views Alert**
   - "Your item 'iPhone 14 Pro' has only 5 views left!"
   - Action: "Sponsor Now"

2. **Sponsorship Expiring**
   - "Your sponsorship expires in 2 days"
   - Action: "Renew Now"

3. **Item Popular**
   - "Your item received 10 contacts today!"
   - Action: "View Analytics"

4. **Sponsorship Expired**
   - "Your sponsorship has ended. Renew to stay visible"
   - Action: "Choose Package"

---

## 📊 Analytics Display

### Item Performance
```
Item: iPhone 14 Pro
Status: Sponsored (Premium)

Views:     ██████████ 250/500
Contacts:  ████────── 15 contacts
ROI:       +45% (estimated)

Time Remaining: 12 days
Priority Level: 10/10

[Renew Sponsorship] [View Details]
```

### User Statistics
```
Total Items: 5
Active Sponsorships: 2
Total Spent: 240.00 EGP
Total Views Gained: 750
Total Contacts: 35
Average ROI: +38%
```

---

## 🛠️ Common Issues & Solutions

### Issue: View limit reached
**Solution:** Sponsor the item or wait for current sponsorship to be activated

### Issue: Images not uploading
**Check:** File size < 5MB, format: jpg/png/webp, max 5 images

### Issue: Item not appearing in search
**Check:** View limit, item status, sponsorship status

### Issue: Sponsorship not activating
**Check:** Payment status, starts_at timestamp, item ownership

---

## 🎯 Best Practices

1. **High-Quality Images**
   - Use clear, well-lit photos
   - Show item from multiple angles
   - First image is the thumbnail

2. **Detailed Descriptions**
   - Minimum 20 characters required
   - Include specifications, condition details
   - Mention any defects honestly

3. **Pricing Strategy**
   - Research similar items
   - Enable "negotiable" for flexibility
   - Update price if not getting contacts

4. **Sponsorship Timing**
   - Sponsor when listing new items for maximum exposure
   - Renew before expiration to maintain visibility
   - Choose package based on item value

5. **Response Time**
   - Respond quickly to contacts
   - Keep contact information updated
   - Mark as sold when completed

---

## 🔐 Security Notes

- All images stored securely in `storage/marketplace/`
- User authentication required for creation/editing
- Ownership verification on all modifications
- Input validation on all fields
- XSS protection on text fields
- File upload restrictions enforced

---

## 📞 Support Contact

For technical issues:
- Check API documentation: `MARKETPLACE_IMPLEMENTATION.md`
- Review response error messages
- Verify authentication tokens
- Confirm item ownership

**Status:** ✅ Production Ready
**Version:** 1.0.0
**Last Updated:** November 30, 2025
