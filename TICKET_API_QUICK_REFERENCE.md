# Support Ticket API - Quick Reference

## 🚀 Quick Start

### Base URL
```
https://your-domain.com/api/v1
```

### Authentication Header
```http
Authorization: Bearer {your-token}
```

---

## 📋 Endpoints Summary

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/tickets/categories` | ❌ No | Get ticket categories |
| GET | `/tickets` | ✅ Yes | List user's tickets |
| POST | `/tickets` | ✅ Yes | Create new ticket |
| GET | `/tickets/{id}` | ✅ Yes | Get ticket details |
| POST | `/tickets/{id}/reply` | ✅ Yes | Reply to ticket |
| POST | `/tickets/{id}/rate` | ✅ Yes | Rate ticket |
| GET | `/tickets/statistics` | ✅ Yes | Get ticket statistics |

---

## 📊 Ticket Categories

```
technical_issue      - مشكلة تقنية
shop_complaint       - شكوى متجر
payment_issue        - مشكلة دفع
account_problem      - مشكلة حساب
feature_request      - طلب ميزة
bug_report          - بلاغ خطأ
content_issue       - مشكلة محتوى
other               - أخرى
```

---

## 🎯 Priority Levels

```
low     - منخفضة (Low Priority)
medium  - متوسطة (Medium Priority)
high    - عالية (High Priority)
urgent  - عاجلة (Urgent)
```

---

## 📌 Status Values

```
open          - مفتوح (Open)
in_progress   - قيد المعالجة (In Progress)
waiting_user  - في انتظار المستخدم (Waiting for User)
resolved      - تم الحل (Resolved)
closed        - مغلق (Closed)
```

---

## 💻 Quick Examples

### 1. Get Categories (No Auth)
```bash
curl -X GET "https://your-domain.com/api/v1/tickets/categories" \
  -H "Accept: application/json"
```

### 2. List Tickets
```bash
curl -X GET "https://your-domain.com/api/v1/tickets?status=open" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### 3. Create Ticket
```bash
curl -X POST "https://your-domain.com/api/v1/tickets" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "subject=App crashes on startup" \
  -F "category=technical_issue" \
  -F "priority=high" \
  -F "description=The app crashes immediately..." \
  -F "city_id=1" \
  -F "attachments[]=@screenshot.jpg"
```

### 4. Get Ticket Details
```bash
curl -X GET "https://your-domain.com/api/v1/tickets/15" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### 5. Reply to Ticket
```bash
curl -X POST "https://your-domain.com/api/v1/tickets/15/reply" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "message=I'm using Samsung Galaxy S21" \
  -F "attachments[]=@device_info.png"
```

### 6. Rate Ticket
```bash
curl -X POST "https://your-domain.com/api/v1/tickets/15/rate" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"rating":5,"feedback":"Excellent support!"}'
```

### 7. Get Statistics
```bash
curl -X GET "https://your-domain.com/api/v1/tickets/statistics" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

## 📱 Flutter Quick Implementation

```dart
// Service Class
class TicketService {
  final String baseUrl = 'https://your-domain.com/api/v1';
  final String token;

  TicketService(this.token);

  Future<List<Ticket>> getTickets() async {
    final response = await http.get(
      Uri.parse('$baseUrl/tickets'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    final data = json.decode(response.body);
    return (data['data'] as List)
        .map((ticket) => Ticket.fromJson(ticket))
        .toList();
  }

  Future<Ticket> createTicket(TicketData ticketData) async {
    var request = http.MultipartRequest(
      'POST',
      Uri.parse('$baseUrl/tickets'),
    );
    request.headers['Authorization'] = 'Bearer $token';
    request.fields['subject'] = ticketData.subject;
    request.fields['category'] = ticketData.category;
    request.fields['priority'] = ticketData.priority;
    request.fields['description'] = ticketData.description;
    
    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    final data = json.decode(response.body);
    return Ticket.fromJson(data['data']);
  }
}

// Model
class Ticket {
  final int id;
  final String ticketNumber;
  final String subject;
  final String status;
  final String priority;
  final DateTime createdAt;

  Ticket.fromJson(Map<String, dynamic> json)
      : id = json['id'],
        ticketNumber = json['ticket_number'],
        subject = json['subject'],
        status = json['status'],
        priority = json['priority'],
        createdAt = DateTime.parse(json['created_at']);
}
```

---

## 🔧 Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

---

## ⚠️ Important Notes

1. **Max File Size**: 10MB per file
2. **File Types**: jpg, jpeg, png, pdf, doc, docx
3. **Pagination**: 15 items per page
4. **Rate Limit**: Standard Laravel throttling applies
5. **Closed Tickets**: Cannot reply to closed tickets
6. **Rating**: Only for resolved/closed tickets, one-time only

---

## 📦 Sample Payloads

### Create Ticket Request
```json
{
  "subject": "App crashes on startup",
  "category": "technical_issue",
  "priority": "high",
  "description": "Detailed description...",
  "city_id": 1,
  "shop_id": null
}
```

### Reply to Ticket Request
```json
{
  "message": "Thank you for your response..."
}
```

### Rate Ticket Request
```json
{
  "rating": 5,
  "feedback": "Excellent support!"
}
```

---

## 🎨 UI Status Colors

| Status | Color | Badge |
|--------|-------|-------|
| open | Blue | 🔵 |
| in_progress | Orange | 🟠 |
| waiting_user | Purple | 🟣 |
| resolved | Green | 🟢 |
| closed | Gray | ⚪ |

| Priority | Color | Badge |
|----------|-------|-------|
| low | Gray | ⚪ |
| medium | Blue | 🔵 |
| high | Orange | 🟠 |
| urgent | Red | 🔴 |

---

## 🔍 Filter Examples

```bash
# Filter by status
GET /tickets?status=open

# Filter by priority
GET /tickets?priority=high

# Filter by category
GET /tickets?category=technical_issue

# Multiple filters
GET /tickets?status=in_progress&priority=urgent

# Pagination
GET /tickets?page=2
```

---

## 📞 Support

**Technical Support**: api-support@your-domain.com  
**Documentation**: See TICKET_API_DOCUMENTATION.md

---

**Last Updated**: December 6, 2025
