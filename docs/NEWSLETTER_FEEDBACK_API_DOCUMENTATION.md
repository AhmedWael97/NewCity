# 📱 Newsletter & Feedback API Documentation

## 📋 Table of Contents
- [Newsletter API](#newsletter-api)
- [Feedback API](#feedback-api)
- [Request Examples](#request-examples)
- [Response Codes](#response-codes)
- [Error Handling](#error-handling)

---

## 📧 Newsletter API

### Base URL
```
https://your-domain.com/api/v1/newsletter
```

### 1. Subscribe to Newsletter

**Endpoint:** `POST /api/v1/newsletter/subscribe`

**Description:** Subscribe a user to the newsletter with optional name and discount incentive.

**Authentication:** None (Public)

**Request Body:**
```json
{
  "email": "user@example.com",
  "name": "أحمد محمد"  // Optional
}
```

**Success Response (201 Created):**
```json
{
  "success": true,
  "message": "تم الاشتراك بنجاح! سنرسل لك أحدث العروض والمتاجر",
  "data": {
    "id": 1,
    "email": "user@example.com",
    "name": "أحمد محمد",
    "subscribed_at": "2025-12-03T10:30:00.000000Z"
  }
}
```

**Re-activation Response (200 OK):**
```json
{
  "success": true,
  "message": "تم إعادة تفعيل اشتراكك بنجاح!",
  "data": {
    "id": 1,
    "email": "user@example.com",
    "name": "أحمد محمد",
    "subscribed_at": "2025-12-03T10:30:00.000000Z"
  }
}
```

**Error Response (409 Conflict):**
```json
{
  "success": false,
  "message": "هذا البريد الإلكتروني مشترك بالفعل"
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "فشل التحقق من البيانات",
  "errors": {
    "email": [
      "البريد الإلكتروني مطلوب"
    ]
  }
}
```

---

### 2. Unsubscribe from Newsletter

**Endpoint:** `POST /api/v1/newsletter/unsubscribe`

**Description:** Unsubscribe from the newsletter.

**Authentication:** None (Public)

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "تم إلغاء الاشتراك بنجاح"
}
```

**Error Response (404 Not Found):**
```json
{
  "success": false,
  "message": "البريد الإلكتروني غير مشترك"
}
```

---

### 3. Check Subscription Status

**Endpoint:** `GET /api/v1/newsletter/status?email=user@example.com`

**Description:** Check if an email is subscribed to the newsletter.

**Authentication:** None (Public)

**Query Parameters:**
- `email` (required): The email address to check

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "is_subscribed": true,
    "subscribed_at": "2025-12-03T10:30:00.000000Z"
  }
}
```

**Not Subscribed Response:**
```json
{
  "success": true,
  "data": {
    "is_subscribed": false,
    "subscribed_at": null
  }
}
```

---

## 💬 Feedback API

### Base URL
```
https://your-domain.com/api/v1/feedback
```

### 1. Submit Feedback

**Endpoint:** `POST /api/v1/feedback/submit`

**Description:** Submit user feedback/rating for the application.

**Authentication:** Optional (Can be authenticated or anonymous)

**Request Body:**
```json
{
  "rating": 5,
  "message": "موقع رائع وسهل الاستخدام!",  // Optional
  "email": "user@example.com",             // Optional
  "page_url": "https://senueg.com/city/cairo"  // Optional
}
```

**Parameters:**
- `rating` (required): Integer from 1 to 5
- `message` (optional): String, max 1000 characters
- `email` (optional): Valid email address
- `page_url` (optional): The page where feedback was given

**Success Response (201 Created):**
```json
{
  "success": true,
  "message": "شكراً لك! تقييمك يساعدنا على التحسين",
  "data": {
    "id": 1,
    "rating": 5,
    "message": "موقع رائع وسهل الاستخدام!",
    "submitted_at": "2025-12-03T10:30:00.000000Z"
  }
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "فشل التحقق من البيانات",
  "errors": {
    "rating": [
      "التقييم مطلوب",
      "التقييم يجب أن يكون على الأقل 1"
    ]
  }
}
```

---

### 2. Get Feedback Statistics

**Endpoint:** `GET /api/v1/feedback/statistics`

**Description:** Get overall feedback statistics including average rating and distribution.

**Authentication:** None (Public)

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "total_feedback": 234,
    "average_rating": 4.3,
    "rating_distribution": {
      "5": 152,
      "4": 70,
      "3": 7,
      "2": 3,
      "1": 2
    },
    "positive_count": 222,
    "negative_count": 5,
    "recent_feedback": [
      {
        "id": 1,
        "rating": 5,
        "message": "موقع رائع!",
        "submitted_at": "2025-12-03T10:30:00.000000Z"
      }
    ]
  }
}
```

---

### 3. Get User's Feedback History

**Endpoint:** `GET /api/v1/feedback/history`

**Description:** Get all feedback submitted by the authenticated user.

**Authentication:** Required (Bearer Token)

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "rating": 5,
      "message": "موقع رائع!",
      "page_url": "https://senueg.com/city/cairo",
      "submitted_at": "2025-12-03T10:30:00.000000Z"
    },
    {
      "id": 2,
      "rating": 4,
      "message": "جيد جداً",
      "page_url": "mobile-app",
      "submitted_at": "2025-12-02T15:20:00.000000Z"
    }
  ]
}
```

**Unauthorized Response (401):**
```json
{
  "success": false,
  "message": "يجب تسجيل الدخول أولاً"
}
```

---

### 4. Update User's Feedback

**Endpoint:** `PUT /api/v1/feedback/{id}`

**Description:** Update a previously submitted feedback (only by the owner).

**Authentication:** Required (Bearer Token)

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**URL Parameters:**
- `id`: The feedback ID to update

**Request Body:**
```json
{
  "rating": 4,
  "message": "موقع جيد بعد التحديثات"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "تم تحديث التقييم بنجاح",
  "data": {
    "id": 1,
    "rating": 4,
    "message": "موقع جيد بعد التحديثات",
    "submitted_at": "2025-12-03T10:30:00.000000Z"
  }
}
```

**Forbidden Response (403):**
```json
{
  "success": false,
  "message": "غير مصرح لك بتحديث هذا التقييم"
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "التقييم غير موجود"
}
```

---

### 5. Delete User's Feedback

**Endpoint:** `DELETE /api/v1/feedback/{id}`

**Description:** Delete a previously submitted feedback (only by the owner).

**Authentication:** Required (Bearer Token)

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**URL Parameters:**
- `id`: The feedback ID to delete

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "تم حذف التقييم بنجاح"
}
```

**Forbidden Response (403):**
```json
{
  "success": false,
  "message": "غير مصرح لك بحذف هذا التقييم"
}
```

---

## 📝 Request Examples

### cURL Examples

#### Subscribe to Newsletter
```bash
curl -X POST "https://your-domain.com/api/v1/newsletter/subscribe" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com",
    "name": "أحمد محمد"
  }'
```

#### Submit Feedback
```bash
curl -X POST "https://your-domain.com/api/v1/feedback/submit" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "rating": 5,
    "message": "تطبيق ممتاز!",
    "page_url": "mobile-app"
  }'
```

#### Get Feedback History (Authenticated)
```bash
curl -X GET "https://your-domain.com/api/v1/feedback/history" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

---

### Flutter/Dart Examples

#### Subscribe to Newsletter
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<void> subscribeNewsletter(String email, String? name) async {
  final response = await http.post(
    Uri.parse('https://your-domain.com/api/v1/newsletter/subscribe'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode({
      'email': email,
      'name': name,
    }),
  );

  if (response.statusCode == 201) {
    final data = jsonDecode(response.body);
    print('Success: ${data['message']}');
  } else if (response.statusCode == 409) {
    print('Already subscribed');
  } else {
    print('Error: ${response.body}');
  }
}
```

#### Submit Feedback
```dart
Future<void> submitFeedback(int rating, String? message) async {
  final response = await http.post(
    Uri.parse('https://your-domain.com/api/v1/feedback/submit'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode({
      'rating': rating,
      'message': message,
      'page_url': 'mobile-app',
    }),
  );

  if (response.statusCode == 201) {
    final data = jsonDecode(response.body);
    print('Success: ${data['message']}');
  } else {
    print('Error: ${response.body}');
  }
}
```

#### Get Feedback History (with auth token)
```dart
Future<List<dynamic>> getFeedbackHistory(String token) async {
  final response = await http.get(
    Uri.parse('https://your-domain.com/api/v1/feedback/history'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    return data['data'];
  } else {
    throw Exception('Failed to load feedback history');
  }
}
```

---

## 📊 Response Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request parameters |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | No permission to access resource |
| 404 | Not Found | Resource not found |
| 409 | Conflict | Resource already exists |
| 422 | Unprocessable Entity | Validation error |
| 500 | Internal Server Error | Server error |

---

## ⚠️ Error Handling

### Standard Error Response Format
```json
{
  "success": false,
  "message": "Error message in Arabic",
  "errors": {
    "field_name": [
      "Error description"
    ]
  }
}
```

### Common Validation Errors

**Newsletter Subscribe:**
- `email.required`: "البريد الإلكتروني مطلوب"
- `email.email`: "البريد الإلكتروني غير صالح"
- `email.max`: "البريد الإلكتروني طويل جداً"

**Feedback Submit:**
- `rating.required`: "التقييم مطلوب"
- `rating.min`: "التقييم يجب أن يكون على الأقل 1"
- `rating.max`: "التقييم يجب ألا يتجاوز 5"
- `message.max`: "الرسالة طويلة جداً (الحد الأقصى 1000 حرف)"

---

## 🔒 Security Notes

1. **CSRF Protection**: Not required for API endpoints
2. **Rate Limiting**: Apply rate limiting on subscription endpoints
3. **Input Validation**: All inputs are validated server-side
4. **XSS Prevention**: Automatic Laravel escaping
5. **SQL Injection**: Protected by Eloquent ORM

---

## 🧪 Testing Endpoints

### Test Newsletter Subscribe
```bash
curl -X POST http://127.0.0.1:8000/api/v1/newsletter/subscribe \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","name":"Test User"}'
```

### Test Feedback Submit
```bash
curl -X POST http://127.0.0.1:8000/api/v1/feedback/submit \
  -H "Content-Type: application/json" \
  -d '{"rating":5,"message":"Great app!","page_url":"mobile-app"}'
```

### Test Feedback Statistics
```bash
curl http://127.0.0.1:8000/api/v1/feedback/statistics
```

---

## 📱 Mobile App Integration Checklist

- [ ] Add newsletter subscription UI
- [ ] Add rating/feedback dialog
- [ ] Store subscription status locally
- [ ] Handle network errors gracefully
- [ ] Show success/error messages
- [ ] Track user interactions
- [ ] Test on iOS and Android
- [ ] Add loading indicators
- [ ] Implement retry logic

---

## 🎯 Best Practices

1. **Newsletter Subscription:**
   - Show benefits of subscribing (10% discount)
   - Make unsubscribe easy
   - Respect user's choice
   - Don't spam subscription requests

2. **Feedback Collection:**
   - Ask at appropriate moments (after transaction, after using feature)
   - Keep form simple (rating + optional message)
   - Thank users for feedback
   - Act on negative feedback
   - Show appreciation for positive feedback

3. **Error Handling:**
   - Always show user-friendly messages
   - Log errors for debugging
   - Provide fallback options
   - Don't expose sensitive information

---

## 📞 Support

For API issues or questions, contact the development team or check the admin dashboard at:
- Newsletter: `/admin/newsletter`
- Feedback: `/admin/feedback`

---

**Last Updated:** December 3, 2025  
**API Version:** v1  
**Status:** Production Ready ✅
