# Support Ticket API - Mobile Application Documentation

## 📋 Table of Contents
- [Overview](#overview)
- [Authentication](#authentication)
- [Endpoints](#endpoints)
  - [Get Ticket Categories](#get-ticket-categories)
  - [List User Tickets](#list-user-tickets)
  - [Create Ticket](#create-ticket)
  - [Get Ticket Details](#get-ticket-details)
  - [Reply to Ticket](#reply-to-ticket)
  - [Rate Ticket](#rate-ticket)
  - [Get Statistics](#get-statistics)
- [Error Handling](#error-handling)
- [Examples](#examples)

---

## 🎯 Overview

The Support Ticket API allows mobile users to:
- Create and manage support tickets
- Submit issues, complaints, feature requests, or bug reports
- Reply to tickets and track conversations
- Rate support experience
- View ticket statistics and history

**Base URL**: `https://your-domain.com/api/v1`

---

## 🔐 Authentication

Most endpoints require authentication using **Laravel Sanctum** tokens.

### Headers Required:
```http
Authorization: Bearer {your-access-token}
Accept: application/json
Content-Type: application/json
```

### Public Endpoints (No Auth):
- `GET /tickets/categories` - Get ticket categories

---

## 📡 Endpoints

### 1. Get Ticket Categories

Get available ticket categories with labels in both English and Arabic.

**Endpoint**: `GET /api/v1/tickets/categories`  
**Auth Required**: No

#### Request:
```http
GET /api/v1/tickets/categories
Accept: application/json
```

#### Response (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "value": "technical_issue",
      "label_en": "Technical Issue",
      "label_ar": "مشكلة تقنية"
    },
    {
      "value": "shop_complaint",
      "label_en": "Shop Complaint",
      "label_ar": "شكوى متجر"
    },
    {
      "value": "payment_issue",
      "label_en": "Payment Issue",
      "label_ar": "مشكلة دفع"
    },
    {
      "value": "account_problem",
      "label_en": "Account Problem",
      "label_ar": "مشكلة حساب"
    },
    {
      "value": "feature_request",
      "label_en": "Feature Request",
      "label_ar": "طلب ميزة"
    },
    {
      "value": "bug_report",
      "label_en": "Bug Report",
      "label_ar": "بلاغ خطأ"
    },
    {
      "value": "content_issue",
      "label_en": "Content Issue",
      "label_ar": "مشكلة محتوى"
    },
    {
      "value": "other",
      "label_en": "Other",
      "label_ar": "أخرى"
    }
  ]
}
```

---

### 2. List User Tickets

Get all tickets created by the authenticated user with optional filters.

**Endpoint**: `GET /api/v1/tickets`  
**Auth Required**: Yes

#### Query Parameters:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | string | No | Filter by status: `open`, `in_progress`, `waiting_user`, `resolved`, `closed` |
| priority | string | No | Filter by priority: `low`, `medium`, `high`, `urgent` |
| category | string | No | Filter by category (see categories endpoint) |
| page | integer | No | Page number (default: 1) |

#### Request:
```http
GET /api/v1/tickets?status=open&priority=high&page=1
Authorization: Bearer {token}
Accept: application/json
```

#### Response (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 15,
      "ticket_number": "TICK-2025-015",
      "subject": "App crashes on startup",
      "description": "The app crashes immediately when I open it...",
      "category": "technical_issue",
      "category_label": "مشكلة تقنية",
      "priority": "high",
      "priority_label": "عالية",
      "status": "open",
      "status_label": "مفتوح",
      "city": {
        "id": 1,
        "name": "الرياض"
      },
      "shop": null,
      "assigned_admin": {
        "id": 2,
        "name": "Ahmed Support"
      },
      "attachments_count": 2,
      "replies_count": 3,
      "unread_replies_count": 1,
      "created_at": "2025-12-06T10:30:00+03:00",
      "updated_at": "2025-12-06T14:25:00+03:00",
      "created_at_human": "4 hours ago"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```

---

### 3. Create Ticket

Create a new support ticket with optional attachments.

**Endpoint**: `POST /api/v1/tickets`  
**Auth Required**: Yes  
**Content-Type**: `multipart/form-data`

#### Request Body:
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| subject | string | Yes | Ticket subject (max 255 chars) |
| category | string | Yes | One of the category values |
| priority | string | Yes | `low`, `medium`, `high`, or `urgent` |
| description | string | Yes | Detailed description of the issue |
| city_id | integer | No | Related city ID |
| shop_id | integer | No | Related shop ID |
| attachments[] | file[] | No | Array of files (max 10MB each, jpg/png/pdf/doc/docx) |

#### Request:
```http
POST /api/v1/tickets
Authorization: Bearer {token}
Content-Type: multipart/form-data

subject=App crashes on startup
category=technical_issue
priority=high
description=When I open the app, it immediately crashes...
city_id=1
attachments[]=@screenshot1.jpg
attachments[]=@error_log.txt
```

#### Response (201 Created):
```json
{
  "success": true,
  "message": "Ticket created successfully",
  "data": {
    "id": 15,
    "ticket_number": "TICK-2025-015",
    "subject": "App crashes on startup",
    "description": "When I open the app, it immediately crashes...",
    "category": "technical_issue",
    "category_label": "مشكلة تقنية",
    "priority": "high",
    "priority_label": "عالية",
    "status": "open",
    "status_label": "مفتوح",
    "city": {
      "id": 1,
      "name": "الرياض"
    },
    "shop": null,
    "attachments": [
      {
        "name": "screenshot1.jpg",
        "path": "support_tickets/xyz123.jpg",
        "url": "/storage/support_tickets/xyz123.jpg",
        "size": 245678,
        "mime": "image/jpeg"
      },
      {
        "name": "error_log.txt",
        "path": "support_tickets/abc456.txt",
        "url": "/storage/support_tickets/abc456.txt",
        "size": 12345,
        "mime": "text/plain"
      }
    ],
    "created_at": "2025-12-06T10:30:00+03:00",
    "created_at_human": "just now"
  }
}
```

#### Validation Errors (422):
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "subject": ["The subject field is required."],
    "category": ["The selected category is invalid."],
    "priority": ["The priority field is required."]
  }
}
```

---

### 4. Get Ticket Details

Get detailed information about a specific ticket including all replies.

**Endpoint**: `GET /api/v1/tickets/{id}`  
**Auth Required**: Yes

#### Request:
```http
GET /api/v1/tickets/15
Authorization: Bearer {token}
Accept: application/json
```

#### Response (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 15,
    "ticket_number": "TICK-2025-015",
    "subject": "App crashes on startup",
    "description": "When I open the app, it immediately crashes...",
    "category": "technical_issue",
    "category_label": "مشكلة تقنية",
    "priority": "high",
    "priority_label": "عالية",
    "status": "in_progress",
    "status_label": "قيد المعالجة",
    "city": {
      "id": 1,
      "name": "الرياض"
    },
    "shop": null,
    "assigned_admin": {
      "id": 2,
      "name": "Ahmed Support",
      "email": "ahmed@example.com"
    },
    "attachments": [
      {
        "name": "screenshot1.jpg",
        "path": "support_tickets/xyz123.jpg",
        "url": "/storage/support_tickets/xyz123.jpg",
        "size": 245678,
        "mime": "image/jpeg"
      }
    ],
    "replies": [
      {
        "id": 1,
        "message": "Thank you for reporting this. Can you please share which device you're using?",
        "is_admin_reply": true,
        "attachments": null,
        "user": {
          "id": 2,
          "name": "Ahmed Support",
          "email": "ahmed@example.com"
        },
        "read_at": "2025-12-06T11:00:00+03:00",
        "created_at": "2025-12-06T10:45:00+03:00",
        "created_at_human": "3 hours ago"
      },
      {
        "id": 2,
        "message": "I'm using Samsung Galaxy S21, Android 13",
        "is_admin_reply": false,
        "attachments": null,
        "user": {
          "id": 5,
          "name": "User Name",
          "email": "user@example.com"
        },
        "read_at": null,
        "created_at": "2025-12-06T11:15:00+03:00",
        "created_at_human": "2 hours ago"
      }
    ],
    "resolved_at": null,
    "closed_at": null,
    "resolution_notes": null,
    "satisfaction_rating": null,
    "satisfaction_feedback": null,
    "created_at": "2025-12-06T10:30:00+03:00",
    "updated_at": "2025-12-06T11:15:00+03:00",
    "created_at_human": "3 hours ago"
  }
}
```

#### Error Responses:

**Not Found (404):**
```json
{
  "success": false,
  "message": "Ticket not found"
}
```

**Forbidden (403):**
```json
{
  "success": false,
  "message": "Unauthorized access to this ticket"
}
```

---

### 5. Reply to Ticket

Add a reply to an existing ticket with optional attachments.

**Endpoint**: `POST /api/v1/tickets/{id}/reply`  
**Auth Required**: Yes  
**Content-Type**: `multipart/form-data`

#### Request Body:
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| message | string | Yes | Reply message |
| attachments[] | file[] | No | Array of files (max 10MB each) |

#### Request:
```http
POST /api/v1/tickets/15/reply
Authorization: Bearer {token}
Content-Type: multipart/form-data

message=I'm using Samsung Galaxy S21, Android 13
attachments[]=@device_info.png
```

#### Response (201 Created):
```json
{
  "success": true,
  "message": "Reply added successfully",
  "data": {
    "id": 3,
    "message": "I'm using Samsung Galaxy S21, Android 13",
    "is_admin_reply": false,
    "attachments": [
      {
        "name": "device_info.png",
        "path": "ticket_replies/def789.png",
        "url": "/storage/ticket_replies/def789.png",
        "size": 98765,
        "mime": "image/png"
      }
    ],
    "user": {
      "id": 5,
      "name": "User Name",
      "email": "user@example.com"
    },
    "created_at": "2025-12-06T11:15:00+03:00",
    "created_at_human": "just now"
  }
}
```

#### Error Responses:

**Closed Ticket (400):**
```json
{
  "success": false,
  "message": "Cannot reply to a closed ticket"
}
```

---

### 6. Rate Ticket

Rate a resolved or closed ticket and provide feedback.

**Endpoint**: `POST /api/v1/tickets/{id}/rate`  
**Auth Required**: Yes  
**Content-Type**: `application/json`

#### Request Body:
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| rating | integer | Yes | Rating from 1 to 5 stars |
| feedback | string | No | Optional feedback text (max 500 chars) |

#### Request:
```http
POST /api/v1/tickets/15/rate
Authorization: Bearer {token}
Content-Type: application/json

{
  "rating": 5,
  "feedback": "Excellent support! The issue was resolved quickly."
}
```

#### Response (200 OK):
```json
{
  "success": true,
  "message": "Thank you for your feedback!",
  "data": {
    "ticket_number": "TICK-2025-015",
    "rating": 5,
    "feedback": "Excellent support! The issue was resolved quickly."
  }
}
```

#### Error Responses:

**Invalid Status (400):**
```json
{
  "success": false,
  "message": "Can only rate resolved or closed tickets"
}
```

**Already Rated (400):**
```json
{
  "success": false,
  "message": "Ticket has already been rated"
}
```

---

### 7. Get Statistics

Get user's ticket statistics and counts.

**Endpoint**: `GET /api/v1/tickets/statistics`  
**Auth Required**: Yes

#### Request:
```http
GET /api/v1/tickets/statistics
Authorization: Bearer {token}
Accept: application/json
```

#### Response (200 OK):
```json
{
  "success": true,
  "data": {
    "total": 15,
    "open": 3,
    "in_progress": 5,
    "waiting_user": 2,
    "resolved": 4,
    "closed": 1,
    "unread_replies": 2
  }
}
```

---

## 🚨 Error Handling

### Standard Error Response Format:
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

### Common HTTP Status Codes:
| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

---

## 💡 Examples

### Flutter/Dart Example

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class TicketService {
  final String baseUrl = 'https://your-domain.com/api/v1';
  final String token;

  TicketService(this.token);

  // Get all tickets
  Future<List<Ticket>> getTickets({String? status}) async {
    var url = Uri.parse('$baseUrl/tickets');
    if (status != null) {
      url = Uri.parse('$baseUrl/tickets?status=$status');
    }

    final response = await http.get(
      url,
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return (data['data'] as List)
          .map((ticket) => Ticket.fromJson(ticket))
          .toList();
    }
    throw Exception('Failed to load tickets');
  }

  // Create ticket
  Future<Ticket> createTicket({
    required String subject,
    required String category,
    required String priority,
    required String description,
    int? cityId,
    int? shopId,
    List<File>? attachments,
  }) async {
    var request = http.MultipartRequest(
      'POST',
      Uri.parse('$baseUrl/tickets'),
    );

    request.headers.addAll({
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    });

    request.fields['subject'] = subject;
    request.fields['category'] = category;
    request.fields['priority'] = priority;
    request.fields['description'] = description;
    if (cityId != null) request.fields['city_id'] = cityId.toString();
    if (shopId != null) request.fields['shop_id'] = shopId.toString();

    if (attachments != null) {
      for (var file in attachments) {
        request.files.add(
          await http.MultipartFile.fromPath('attachments[]', file.path),
        );
      }
    }

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);

    if (response.statusCode == 201) {
      final data = json.decode(response.body);
      return Ticket.fromJson(data['data']);
    }
    throw Exception('Failed to create ticket');
  }

  // Get ticket details
  Future<TicketDetail> getTicketDetails(int ticketId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/tickets/$ticketId'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return TicketDetail.fromJson(data['data']);
    }
    throw Exception('Failed to load ticket details');
  }

  // Reply to ticket
  Future<Reply> replyToTicket(int ticketId, String message,
      {List<File>? attachments}) async {
    var request = http.MultipartRequest(
      'POST',
      Uri.parse('$baseUrl/tickets/$ticketId/reply'),
    );

    request.headers.addAll({
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    });

    request.fields['message'] = message;

    if (attachments != null) {
      for (var file in attachments) {
        request.files.add(
          await http.MultipartFile.fromPath('attachments[]', file.path),
        );
      }
    }

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);

    if (response.statusCode == 201) {
      final data = json.decode(response.body);
      return Reply.fromJson(data['data']);
    }
    throw Exception('Failed to send reply');
  }

  // Rate ticket
  Future<void> rateTicket(int ticketId, int rating, {String? feedback}) async {
    final response = await http.post(
      Uri.parse('$baseUrl/tickets/$ticketId/rate'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'rating': rating,
        if (feedback != null) 'feedback': feedback,
      }),
    );

    if (response.statusCode != 200) {
      throw Exception('Failed to rate ticket');
    }
  }

  // Get statistics
  Future<TicketStatistics> getStatistics() async {
    final response = await http.get(
      Uri.parse('$baseUrl/tickets/statistics'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return TicketStatistics.fromJson(data['data']);
    }
    throw Exception('Failed to load statistics');
  }
}
```

### JavaScript/React Native Example

```javascript
const API_URL = 'https://your-domain.com/api/v1';

class TicketAPI {
  constructor(token) {
    this.token = token;
  }

  // Get all tickets
  async getTickets(filters = {}) {
    const params = new URLSearchParams(filters);
    const response = await fetch(`${API_URL}/tickets?${params}`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json',
      },
    });
    return await response.json();
  }

  // Create ticket
  async createTicket(data) {
    const formData = new FormData();
    formData.append('subject', data.subject);
    formData.append('category', data.category);
    formData.append('priority', data.priority);
    formData.append('description', data.description);
    if (data.cityId) formData.append('city_id', data.cityId);
    if (data.shopId) formData.append('shop_id', data.shopId);
    
    if (data.attachments) {
      data.attachments.forEach((file) => {
        formData.append('attachments[]', file);
      });
    }

    const response = await fetch(`${API_URL}/tickets`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json',
      },
      body: formData,
    });
    return await response.json();
  }

  // Get ticket details
  async getTicketDetails(ticketId) {
    const response = await fetch(`${API_URL}/tickets/${ticketId}`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json',
      },
    });
    return await response.json();
  }

  // Reply to ticket
  async replyToTicket(ticketId, message, attachments = null) {
    const formData = new FormData();
    formData.append('message', message);
    
    if (attachments) {
      attachments.forEach((file) => {
        formData.append('attachments[]', file);
      });
    }

    const response = await fetch(`${API_URL}/tickets/${ticketId}/reply`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json',
      },
      body: formData,
    });
    return await response.json();
  }

  // Rate ticket
  async rateTicket(ticketId, rating, feedback = null) {
    const response = await fetch(`${API_URL}/tickets/${ticketId}/rate`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ rating, feedback }),
    });
    return await response.json();
  }

  // Get statistics
  async getStatistics() {
    const response = await fetch(`${API_URL}/tickets/statistics`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json',
      },
    });
    return await response.json();
  }

  // Get categories
  async getCategories() {
    const response = await fetch(`${API_URL}/tickets/categories`, {
      headers: {
        'Accept': 'application/json',
      },
    });
    return await response.json();
  }
}

// Usage Example
const ticketAPI = new TicketAPI('your-user-token');

// Create a ticket
ticketAPI.createTicket({
  subject: 'App crashes on startup',
  category: 'technical_issue',
  priority: 'high',
  description: 'The app crashes when I try to open it...',
  cityId: 1,
}).then(result => console.log(result));

// Get tickets
ticketAPI.getTickets({ status: 'open' })
  .then(result => console.log(result));
```

---

## 📱 Mobile UI Recommendations

### Ticket List Screen
- Show ticket number, subject, status badge, and last update
- Use color coding for priorities (red=urgent, orange=high, blue=medium, gray=low)
- Display unread replies badge
- Pull-to-refresh functionality
- Infinite scroll or pagination

### Ticket Detail Screen
- Display all ticket information at the top
- Show conversation thread (chat-style UI)
- Mark admin replies with different background
- File attachment previews with download option
- Reply input at the bottom with file picker
- Auto-scroll to latest message

### Create Ticket Screen
- Category dropdown/picker
- Priority selector (radio buttons or slider)
- Subject and description text fields
- Optional city/shop selectors
- File attachment picker (multiple selection)
- Preview selected files before submit
- Clear form validation messages

### Statistics Dashboard
- Display ticket counts with icons
- Show charts/graphs for ticket trends
- Quick filters for status

---

## 🔄 Status Flow

```
open → in_progress → waiting_user → resolved → closed
        ↓                ↓             ↓
       resolved      resolved       closed
```

- **open**: Newly created ticket
- **in_progress**: Admin is working on it
- **waiting_user**: Waiting for user response
- **resolved**: Issue resolved, awaiting user confirmation
- **closed**: Ticket closed (can be rated)

---

## 📝 Notes

1. **File Uploads**: Maximum 10MB per file, supported formats: jpg, jpeg, png, pdf, doc, docx
2. **Pagination**: Default 15 items per page
3. **Timestamps**: All timestamps are in ISO 8601 format
4. **Localization**: Labels are provided in both English and Arabic
5. **Read Receipts**: Admin replies are automatically marked as read when user views ticket details
6. **Ticket Numbers**: Auto-generated in format `TICK-{YEAR}-{NUMBER}`

---

## 🆘 Support

For API issues or questions, please contact:
- **Email**: api-support@your-domain.com
- **Phone**: +966 XX XXX XXXX

---

**Last Updated**: December 6, 2025  
**API Version**: 1.0
