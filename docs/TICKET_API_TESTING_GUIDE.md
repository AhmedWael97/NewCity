# Support Ticket API - Testing Guide

## 🧪 Quick Testing Guide

This guide will help you test the Support Ticket API endpoints quickly.

---

## 📋 Prerequisites

1. ✅ Laravel application is running
2. ✅ Database migrations are complete
3. ✅ You have a valid user account
4. ✅ You have obtained an authentication token

---

## 🔐 Step 1: Get Authentication Token

### Login Request:
```bash
curl -X POST "https://your-domain.com/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

### Expected Response:
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "1|abcdefghijklmnopqrstuvwxyz..."
  }
}
```

**Save the token** - you'll need it for all subsequent requests!

---

## 📝 Step 2: Test Public Endpoints

### Get Ticket Categories (No Auth Required)

```bash
curl -X GET "https://your-domain.com/api/v1/tickets/categories" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "value": "technical_issue",
      "label_en": "Technical Issue",
      "label_ar": "مشكلة تقنية"
    },
    ...
  ]
}
```

✅ **Pass**: You see a list of categories  
❌ **Fail**: Check if routes are registered

---

## 🎫 Step 3: Create Your First Ticket

```bash
curl -X POST "https://your-domain.com/api/v1/tickets" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json" \
  -F "subject=Test Ticket - App Issue" \
  -F "category=technical_issue" \
  -F "priority=medium" \
  -F "description=This is a test ticket to verify the API is working correctly." \
  -F "city_id=1"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Ticket created successfully",
  "data": {
    "id": 1,
    "ticket_number": "TICK-2025-001",
    "subject": "Test Ticket - App Issue",
    "status": "open",
    ...
  }
}
```

✅ **Pass**: Ticket created with ticket_number  
❌ **Fail**: Check validation errors in response

**Save the ticket ID** for the next steps!

---

## 📋 Step 4: List Your Tickets

```bash
curl -X GET "https://your-domain.com/api/v1/tickets" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "ticket_number": "TICK-2025-001",
      "subject": "Test Ticket - App Issue",
      ...
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

✅ **Pass**: You see your created ticket in the list  
❌ **Fail**: Check authentication token

---

## 🔍 Step 5: Get Ticket Details

Replace `{TICKET_ID}` with your ticket ID:

```bash
curl -X GET "https://your-domain.com/api/v1/tickets/{TICKET_ID}" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_number": "TICK-2025-001",
    "subject": "Test Ticket - App Issue",
    "replies": [],
    ...
  }
}
```

✅ **Pass**: You see full ticket details with empty replies  
❌ **Fail**: Check if ticket ID exists and belongs to your user

---

## 💬 Step 6: Reply to Ticket

```bash
curl -X POST "https://your-domain.com/api/v1/tickets/{TICKET_ID}/reply" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json" \
  -F "message=This is a test reply to the ticket."
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Reply added successfully",
  "data": {
    "id": 1,
    "message": "This is a test reply to the ticket.",
    "is_admin_reply": false,
    ...
  }
}
```

✅ **Pass**: Reply added successfully  
❌ **Fail**: Check if ticket is closed (cannot reply to closed tickets)

---

## ⭐ Step 7: Get Statistics

```bash
curl -X GET "https://your-domain.com/api/v1/tickets/statistics" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "total": 1,
    "open": 1,
    "in_progress": 0,
    "waiting_user": 0,
    "resolved": 0,
    "closed": 0,
    "unread_replies": 0
  }
}
```

✅ **Pass**: Statistics match your tickets  
❌ **Fail**: Check if query is counting correctly

---

## 🎯 Step 8: Rate Ticket (Admin Must Resolve First)

**Note**: You can only rate a ticket after an admin marks it as "resolved" or "closed"

```bash
curl -X POST "https://your-domain.com/api/v1/tickets/{TICKET_ID}/rate" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "rating": 5,
    "feedback": "Excellent support!"
  }'
```

**Expected Response (if resolved/closed):**
```json
{
  "success": true,
  "message": "Thank you for your feedback!",
  "data": {
    "ticket_number": "TICK-2025-001",
    "rating": 5,
    "feedback": "Excellent support!"
  }
}
```

**Expected Error (if not resolved/closed):**
```json
{
  "success": false,
  "message": "Can only rate resolved or closed tickets"
}
```

---

## 🧪 Testing With File Attachments

### Create Ticket with Attachment:

```bash
curl -X POST "https://your-domain.com/api/v1/tickets" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json" \
  -F "subject=Test with Attachment" \
  -F "category=technical_issue" \
  -F "priority=high" \
  -F "description=Testing file upload" \
  -F "city_id=1" \
  -F "attachments[]=@/path/to/screenshot.jpg" \
  -F "attachments[]=@/path/to/document.pdf"
```

### Reply with Attachment:

```bash
curl -X POST "https://your-domain.com/api/v1/tickets/{TICKET_ID}/reply" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json" \
  -F "message=Here are the files you requested" \
  -F "attachments[]=@/path/to/file.jpg"
```

---

## 🔄 Testing Filters

### Filter by Status:
```bash
curl -X GET "https://your-domain.com/api/v1/tickets?status=open" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Filter by Priority:
```bash
curl -X GET "https://your-domain.com/api/v1/tickets?priority=high" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Filter by Category:
```bash
curl -X GET "https://your-domain.com/api/v1/tickets?category=technical_issue" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Multiple Filters:
```bash
curl -X GET "https://your-domain.com/api/v1/tickets?status=open&priority=high&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## 📱 Testing with Postman

### 1. Import Collection
- Open Postman
- Click "Import"
- Select `Support_Ticket_API.postman_collection.json`
- Collection imported ✅

### 2. Set Environment Variables
Create new environment with:
- `base_url`: `https://your-domain.com/api/v1`
- `auth_token`: Your authentication token

### 3. Run Requests
- Navigate through the collection
- Run each request in order
- Verify responses

---

## ❌ Common Issues & Solutions

### Issue 1: 401 Unauthenticated
**Cause**: Invalid or missing token  
**Solution**: 
- Get a fresh token from login endpoint
- Ensure token is in Authorization header
- Format: `Bearer {token}`

### Issue 2: 403 Forbidden
**Cause**: Trying to access another user's ticket  
**Solution**: 
- Only access tickets created by your user
- Check ticket ownership

### Issue 3: 404 Not Found
**Cause**: Ticket ID doesn't exist  
**Solution**: 
- Verify ticket ID from list endpoint
- Check if ticket was deleted

### Issue 4: 422 Validation Error
**Cause**: Invalid input data  
**Solution**: 
- Check required fields: subject, category, priority, description
- Verify category and priority values are valid
- Check file size (max 10MB) and types (jpg, png, pdf, doc, docx)

### Issue 5: Cannot Reply to Closed Ticket
**Cause**: Ticket status is "closed"  
**Solution**: 
- Cannot add replies to closed tickets
- Create a new ticket if needed

### Issue 6: Cannot Rate Ticket
**Cause**: Ticket not resolved/closed or already rated  
**Solution**: 
- Wait for admin to mark as resolved
- Can only rate once

---

## ✅ Test Checklist

- [ ] Login and get auth token
- [ ] Get ticket categories (no auth)
- [ ] Create ticket without attachment
- [ ] Create ticket with attachment
- [ ] List all tickets
- [ ] Filter tickets by status
- [ ] Filter tickets by priority
- [ ] Get ticket details
- [ ] Reply to ticket
- [ ] Reply with attachment
- [ ] Get statistics
- [ ] Rate ticket (after admin resolves)
- [ ] Test pagination (create 20+ tickets)
- [ ] Test error responses
- [ ] Test unauthorized access

---

## 🎓 Expected Test Results

| Test | Expected Result |
|------|----------------|
| Get Categories | Returns 8 categories |
| Create Ticket | Returns ticket with TICK-YYYY-NNN format |
| List Tickets | Returns paginated list |
| Get Details | Returns ticket with replies array |
| Add Reply | Returns reply object |
| Get Statistics | Returns counts object |
| Rate Ticket | Success if resolved/closed |

---

## 📊 Performance Testing

### Test with Multiple Tickets:
```bash
# Create 10 tickets
for i in {1..10}; do
  curl -X POST "https://your-domain.com/api/v1/tickets" \
    -H "Authorization: Bearer YOUR_TOKEN_HERE" \
    -H "Accept: application/json" \
    -F "subject=Test Ticket $i" \
    -F "category=technical_issue" \
    -F "priority=medium" \
    -F "description=Performance test ticket $i"
done

# Test pagination
curl -X GET "https://your-domain.com/api/v1/tickets?page=1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 🐛 Debugging Tips

### Enable Laravel Debug Mode:
```env
APP_DEBUG=true
```

### Check Laravel Logs:
```bash
tail -f storage/logs/laravel.log
```

### Verify Routes:
```bash
php artisan route:list | grep ticket
```

### Check Database:
```sql
SELECT * FROM support_tickets WHERE user_id = YOUR_USER_ID;
SELECT * FROM ticket_replies WHERE ticket_id = YOUR_TICKET_ID;
```

---

## 📞 Need Help?

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database tables exist
3. Ensure migrations are run
4. Check .env configuration
5. Verify user authentication is working

---

## 🎉 Success Criteria

✅ All endpoints return expected responses  
✅ File uploads work correctly  
✅ Filters and pagination work  
✅ Authorization prevents unauthorized access  
✅ Validation catches invalid input  
✅ Statistics are accurate  

---

**Happy Testing!** 🚀

If all tests pass, the API is ready for mobile app integration!
