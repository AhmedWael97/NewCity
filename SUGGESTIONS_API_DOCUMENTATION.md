# Suggestions API Documentation

## Overview
API endpoints for submitting shop and city suggestions to the platform. These endpoints are public and don't require authentication.

**Base URL:** `/api/v1`

---

## Endpoints

### 1. Suggest Shop

Submit a suggestion for a new shop to be added to the platform.

**Endpoint:** `POST /suggestions/shop`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "shop_name": "مطعم الأصالة",
    "city_id": 1,
    "category_id": 5,
    "location": "حي النزهة، شارع الملك فهد",
    "phone": "0551234567",
    "whatsapp": "0551234567",
    "description": "مطعم يقدم أشهى المأكولات التقليدية",
    "google_maps_link": "https://maps.google.com/?q=24.7136,46.6753"
}
```

**Required Fields:**
- `shop_name` (string, max: 255) - Name of the shop
- `city_id` (integer, exists in cities table) - City ID
- `category_id` (integer, exists in categories table) - Category ID
- `phone` (string, max: 20) - Contact phone number

**Optional Fields:**
- `location` (string, max: 500) - Shop location/address
- `whatsapp` (string, max: 20) - WhatsApp number
- `description` (string, max: 1000) - Shop description
- `google_maps_link` (url, max: 500) - Google Maps link

**Success Response (201 Created):**
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

**Error Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "message": "بيانات غير صحيحة",
    "errors": {
        "shop_name": [
            "اسم المتجر مطلوب"
        ],
        "city_id": [
            "المدينة المحددة غير موجودة"
        ],
        "category_id": [
            "التصنيف مطلوب"
        ],
        "phone": [
            "رقم الهاتف مطلوب"
        ]
    }
}
```

**Error Response (500 Internal Server Error):**
```json
{
    "success": false,
    "message": "حدث خطأ أثناء حفظ الاقتراح. الرجاء المحاولة مرة أخرى."
}
```

---

### 2. Suggest City

Submit a suggestion for a new city to be added to the platform.

**Endpoint:** `POST /suggestions/city`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "city_name": "الرياض",
    "phone": "0551234567",
    "group_url": "https://chat.whatsapp.com/example123"
}
```

**Required Fields:**
- `city_name` (string, max: 255) - Name of the city
- `phone` (string, max: 20) - Contact phone number
- `group_url` (url, max: 500) - Main group URL (WhatsApp, Telegram, etc.)

**Success Response (201 Created):**
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

**Error Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "message": "بيانات غير صحيحة",
    "errors": {
        "city_name": [
            "اسم المدينة مطلوب"
        ],
        "phone": [
            "رقم الهاتف مطلوب"
        ],
        "group_url": [
            "رابط المجموعة غير صحيح"
        ]
    }
}
```

**Error Response (500 Internal Server Error):**
```json
{
    "success": false,
    "message": "حدث خطأ أثناء حفظ الاقتراح. الرجاء المحاولة مرة أخرى."
}
```

---

## Error Codes

| Status Code | Description |
|------------|-------------|
| 201 | Created - Suggestion submitted successfully |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error - Server error |

---

## Validation Rules

### Shop Suggestion
| Field | Type | Required | Max Length | Additional Rules |
|-------|------|----------|------------|------------------|
| shop_name | string | Yes | 255 | - |
| city_id | integer | Yes | - | Must exist in cities table |
| category_id | integer | Yes | - | Must exist in categories table |
| location | string | No | 500 | - |
| phone | string | Yes | 20 | - |
| whatsapp | string | No | 20 | - |
| description | string | No | 1000 | - |
| google_maps_link | url | No | 500 | Must be valid URL |

### City Suggestion
| Field | Type | Required | Max Length | Additional Rules |
|-------|------|----------|------------|------------------|
| city_name | string | Yes | 255 | - |
| phone | string | Yes | 20 | - |
| group_url | url | Yes | 500 | Must be valid URL |

---

## Usage Examples

### cURL Example - Shop Suggestion
```bash
curl -X POST http://127.0.0.1:8000/api/v1/suggestions/shop \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "shop_name": "مطعم الأصالة",
    "city_id": 1,
    "category_id": 5,
    "location": "حي النزهة، شارع الملك فهد",
    "phone": "0551234567",
    "whatsapp": "0551234567",
    "description": "مطعم يقدم أشهى المأكولات التقليدية",
    "google_maps_link": "https://maps.google.com/?q=24.7136,46.6753"
  }'
```

### cURL Example - City Suggestion
```bash
curl -X POST http://127.0.0.1:8000/api/v1/suggestions/city \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "city_name": "الرياض",
    "phone": "0551234567",
    "group_url": "https://chat.whatsapp.com/example123"
  }'
```

### JavaScript (Fetch) Example - Shop Suggestion
```javascript
fetch('http://127.0.0.1:8000/api/v1/suggestions/shop', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    shop_name: 'مطعم الأصالة',
    city_id: 1,
    category_id: 5,
    location: 'حي النزهة، شارع الملك فهد',
    phone: '0551234567',
    whatsapp: '0551234567',
    description: 'مطعم يقدم أشهى المأكولات التقليدية',
    google_maps_link: 'https://maps.google.com/?q=24.7136,46.6753'
  })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

### JavaScript (Fetch) Example - City Suggestion
```javascript
fetch('http://127.0.0.1:8000/api/v1/suggestions/city', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    city_name: 'الرياض',
    phone: '0551234567',
    group_url: 'https://chat.whatsapp.com/example123'
  })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

---

## Notes

1. **No Authentication Required:** These endpoints are public and don't require authentication tokens.

2. **IP Tracking:** The system automatically tracks the IP address and user agent of the requester for security and analytics purposes.

3. **Status:** All suggestions are created with a `pending` status and will be reviewed by administrators.

4. **Rate Limiting:** Consider implementing rate limiting on the client side to prevent abuse.

5. **Duplicate Detection:** The system doesn't automatically check for duplicate suggestions. Manual review by administrators is required.

6. **Response Language:** All messages and validation errors are in Arabic.

---

## Admin Review

After submission:
1. Suggestions appear in the admin dashboard under "اقتراحات المتاجر" or "اقتراحات المدن"
2. Admins can review, approve, reject, or delete suggestions
3. Admin can add notes to each suggestion
4. Approved suggestions can be used to create actual shops or cities in the system
