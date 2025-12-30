# 📱 Mobile API Quick Reference Card

## 🚀 Base URL
```
https://your-domain.com/api/v1
```

## 📧 Newsletter API

### Subscribe
```http
POST /newsletter/subscribe
Content-Type: application/json

{
  "email": "user@example.com",
  "name": "User Name"  // optional
}

Response 201:
{
  "success": true,
  "message": "تم الاشتراك بنجاح! سنرسل لك أحدث العروض والمتاجر",
  "data": {...}
}
```

### Unsubscribe
```http
POST /newsletter/unsubscribe
Content-Type: application/json

{
  "email": "user@example.com"
}

Response 200:
{
  "success": true,
  "message": "تم إلغاء الاشتراك بنجاح"
}
```

### Check Status
```http
GET /newsletter/status?email=user@example.com

Response 200:
{
  "success": true,
  "data": {
    "is_subscribed": true,
    "subscribed_at": "2025-12-03T10:30:00.000000Z"
  }
}
```

---

## 💬 Feedback API

### Submit Feedback
```http
POST /feedback/submit
Content-Type: application/json
Authorization: Bearer {token}  // optional

{
  "rating": 5,                    // required (1-5)
  "message": "Great app!",        // optional
  "email": "user@example.com",    // optional
  "page_url": "mobile-app"        // optional
}

Response 201:
{
  "success": true,
  "message": "شكراً لك! تقييمك يساعدنا على التحسين",
  "data": {...}
}
```

### Get Statistics
```http
GET /feedback/statistics

Response 200:
{
  "success": true,
  "data": {
    "total_feedback": 234,
    "average_rating": 4.3,
    "rating_distribution": {"5": 152, "4": 70, ...},
    "positive_count": 222,
    "negative_count": 5,
    "recent_feedback": [...]
  }
}
```

### Get User History (Auth Required)
```http
GET /feedback/history
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "rating": 5,
      "message": "Great!",
      "page_url": "mobile-app",
      "submitted_at": "2025-12-03T10:30:00.000000Z"
    }
  ]
}
```

### Update Feedback (Auth Required)
```http
PUT /feedback/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "rating": 4,
  "message": "Updated message"
}

Response 200:
{
  "success": true,
  "message": "تم تحديث التقييم بنجاح",
  "data": {...}
}
```

### Delete Feedback (Auth Required)
```http
DELETE /feedback/{id}
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "message": "تم حذف التقييم بنجاح"
}
```

---

## 📊 Response Codes

| Code | Meaning |
|------|---------|
| 200 | OK |
| 201 | Created |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 409 | Already Exists |
| 422 | Validation Error |

---

## ⚠️ Common Errors

```json
// Validation Error (422)
{
  "success": false,
  "message": "فشل التحقق من البيانات",
  "errors": {
    "email": ["البريد الإلكتروني مطلوب"]
  }
}

// Already Subscribed (409)
{
  "success": false,
  "message": "هذا البريد الإلكتروني مشترك بالفعل"
}

// Unauthorized (401)
{
  "success": false,
  "message": "يجب تسجيل الدخول أولاً"
}
```

---

## 🧪 Test Commands (PowerShell)

### Newsletter Subscribe
```powershell
Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/v1/newsletter/subscribe" `
  -Method POST `
  -Headers @{"Content-Type"="application/json"} `
  -Body '{"email":"test@example.com","name":"Test User"}'
```

### Feedback Submit
```powershell
Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/v1/feedback/submit" `
  -Method POST `
  -Headers @{"Content-Type"="application/json"} `
  -Body '{"rating":5,"message":"Great app!"}'
```

---

## 📱 Flutter Examples

### Newsletter Subscribe
```dart
final response = await http.post(
  Uri.parse('$baseUrl/newsletter/subscribe'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({'email': email, 'name': name}),
);
```

### Submit Feedback
```dart
final response = await http.post(
  Uri.parse('$baseUrl/feedback/submit'),
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer $token',
  },
  body: jsonEncode({
    'rating': rating,
    'message': message,
    'page_url': 'mobile-app',
  }),
);
```

---

## 📁 Resources

- **Full Documentation**: `NEWSLETTER_FEEDBACK_API_DOCUMENTATION.md`
- **Postman Collection**: `Newsletter_Feedback_API.postman_collection.json`
- **Admin Dashboard**: `/admin/newsletter`, `/admin/feedback`

---

## ✅ Status

**Routes**: 8 endpoints  
**Auth Required**: 3 endpoints (history, update, delete)  
**Public**: 5 endpoints  
**Tested**: ✅ All working  
**Production Ready**: ✅ Yes

---

**Last Updated**: December 3, 2025  
**Version**: 1.0  
🎉 **Ready for mobile app integration!**
