# API Documentation Access Guide

## Available API Documentation

### 1. Swagger/OpenAPI Documentation

**Interactive API Documentation with Swagger UI**

- **URL:** `http://127.0.0.1:8000/api/documentation`
- **File:** `/public/api-docs.json`
- **Format:** OpenAPI 3.0
- **Features:**
  - Interactive API testing
  - Complete endpoint documentation
  - Request/response examples
  - Authentication testing

**New Endpoints Added:**
- `POST /api/v1/suggestions/shop` - Submit shop suggestion
- `POST /api/v1/suggestions/city` - Submit city suggestion

### 2. Postman Collection

**Suggestions API Postman Collection**

- **File:** `/public/Suggestions_API.postman_collection.json`
- **Download URL:** `http://127.0.0.1:8000/Suggestions_API.postman_collection.json`

**How to Import:**
1. Open Postman
2. Click "Import" button
3. Choose "Link" tab
4. Paste: `http://127.0.0.1:8000/Suggestions_API.postman_collection.json`
5. Click "Continue" then "Import"

**Included Requests:**
- Submit Shop Suggestion (with success/error examples)
- Submit City Suggestion (with success/error examples)

### 3. Markdown Documentation

**Detailed API Documentation**

- **File:** `/SUGGESTIONS_API_DOCUMENTATION.md`
- **Includes:**
  - Complete endpoint specifications
  - Request/response examples
  - Validation rules
  - Error codes
  - cURL examples
  - JavaScript examples

---

## Quick Start

### Test the APIs

#### Using cURL:

```bash
# Shop Suggestion
curl -X POST http://127.0.0.1:8000/api/v1/suggestions/shop \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "shop_name": "مطعم الأصالة",
    "city_id": 1,
    "category_id": 5,
    "phone": "0551234567"
  }'

# City Suggestion
curl -X POST http://127.0.0.1:8000/api/v1/suggestions/city \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "city_name": "الرياض",
    "phone": "0551234567",
    "group_url": "https://chat.whatsapp.com/example"
  }'
```

#### Using Postman:
1. Import collection from: `http://127.0.0.1:8000/Suggestions_API.postman_collection.json`
2. Set base_url variable to: `http://127.0.0.1:8000`
3. Run the requests

#### Using Swagger UI:
1. Visit: `http://127.0.0.1:8000/api/documentation`
2. Find "Suggestions" section
3. Click "Try it out" on any endpoint
4. Fill in the parameters
5. Click "Execute"

---

## Regenerate Documentation

If you make changes to the API annotations, regenerate the documentation:

```bash
php artisan l5-swagger:generate
```

---

## API Endpoints Summary

| Endpoint | Method | Description | Auth Required |
|----------|--------|-------------|---------------|
| `/api/v1/suggestions/shop` | POST | Submit shop suggestion | No |
| `/api/v1/suggestions/city` | POST | Submit city suggestion | No |

---

## Response Format

### Success Response (201 Created)
```json
{
    "success": true,
    "message": "شكراً لك! تم إرسال اقتراحك بنجاح وسنقوم بمراجعته قريباً.",
    "data": {
        "suggestion_id": 1,
        "status": "pending"
    }
}
```

### Error Response (422 Validation Error)
```json
{
    "success": false,
    "message": "بيانات غير صحيحة",
    "errors": {
        "field_name": [
            "Error message in Arabic"
        ]
    }
}
```

---

## Admin Access

View submitted suggestions in admin dashboard:
- Shop Suggestions: `/admin/shop-suggestions`
- City Suggestions: `/admin/city-suggestions`

---

## Support

For API support or questions:
- Email: admin@cityshops.com
- Documentation: See `/SUGGESTIONS_API_DOCUMENTATION.md`
