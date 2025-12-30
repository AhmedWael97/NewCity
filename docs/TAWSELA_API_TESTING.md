# Tawsela API Testing Guide

## Base URL
```
http://localhost:8000/api/v1/tawsela
```

## Authentication
Most endpoints require Bearer token authentication:
```
Authorization: Bearer {your_token_here}
```

---

## 📋 Rides Endpoints

### 1. Get All Rides (Public)
```http
GET /api/v1/tawsela/rides
```

**Query Parameters:**
- `city_id` (optional) - integer
- `start_lat` (optional) - decimal
- `start_lng` (optional) - decimal
- `dest_lat` (optional) - decimal
- `dest_lng` (optional) - decimal
- `max_distance` (optional, default: 10) - integer (km)

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/tawsela/rides?city_id=1&max_distance=15"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "user": {
          "id": 1,
          "name": "أحمد محمد",
          "phone": "01234567890",
          "avatar": null
        },
        "city": {
          "id": 1,
          "name": "القاهرة"
        },
        "car_model": "تويوتا كورولا",
        "car_year": 2020,
        "car_color": "أبيض",
        "available_seats": 3,
        "remaining_seats": 2,
        "start_address": "مصر الجديدة، القاهرة",
        "destination_address": "المعادي، القاهرة",
        "price": 50.00,
        "price_type": "fixed",
        "price_unit": "per_person",
        "departure_time": "2025-12-31 10:00:00"
      }
    ]
  }
}
```

---

### 2. Get Ride by ID (Public)
```http
GET /api/v1/tawsela/rides/{id}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/v1/tawsela/rides/1"
```

---

### 3. Create Ride (Auth Required)
```http
POST /api/v1/tawsela/rides
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "city_id": 1,
  "car_model": "تويوتا كورولا",
  "car_year": 2020,
  "car_color": "أبيض",
  "available_seats": 3,
  "start_latitude": 30.0444,
  "start_longitude": 31.2357,
  "start_address": "مصر الجديدة، القاهرة",
  "destination_latitude": 29.9602,
  "destination_longitude": 31.2569,
  "destination_address": "المعادي، القاهرة",
  "stop_points": [
    {
      "latitude": 30.0131,
      "longitude": 31.2089,
      "address": "وسط البلد، القاهرة"
    }
  ],
  "price": 50.00,
  "price_type": "fixed",
  "price_unit": "per_person",
  "departure_time": "2025-12-31 10:00:00",
  "notes": "ممنوع التدخين"
}
```

**cURL Example:**
```bash
curl -X POST "http://localhost:8000/api/v1/tawsela/rides" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "city_id": 1,
    "car_model": "تويوتا كورولا",
    "car_year": 2020,
    "car_color": "أبيض",
    "available_seats": 3,
    "start_latitude": 30.0444,
    "start_longitude": 31.2357,
    "start_address": "مصر الجديدة، القاهرة",
    "destination_latitude": 29.9602,
    "destination_longitude": 31.2569,
    "destination_address": "المعادي، القاهرة",
    "price": 50.00,
    "price_type": "fixed",
    "price_unit": "per_person",
    "departure_time": "2025-12-31 10:00:00"
  }'
```

---

### 4. Update Ride (Auth Required)
```http
PUT /api/v1/tawsela/rides/{id}
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body (partial update):**
```json
{
  "price": 45.00,
  "notes": "تحديث: متاح مقاعد إضافية"
}
```

---

### 5. Delete Ride (Auth Required)
```http
DELETE /api/v1/tawsela/rides/{id}
Authorization: Bearer {token}
```

---

### 6. Get My Rides (Auth Required)
```http
GET /api/v1/tawsela/my-rides
Authorization: Bearer {token}
```

---

## 📝 Requests Endpoints

### 7. Request to Join Ride (Auth Required)
```http
POST /api/v1/tawsela/rides/{id}/request
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "pickup_latitude": 30.0500,
  "pickup_longitude": 31.2400,
  "pickup_address": "النزهة الجديدة، القاهرة",
  "dropoff_latitude": 29.9700,
  "dropoff_longitude": 31.2600,
  "dropoff_address": "دجلة، المعادي",
  "passengers_count": 2,
  "offered_price": 45.00,
  "message": "مرحباً، أود الانضمام إلى رحلتك. هل يمكنني الصعود من النزهة؟"
}
```

---

### 8. Get My Requests (Auth Required)
```http
GET /api/v1/tawsela/my-requests
Authorization: Bearer {token}
```

---

### 9. Get Ride Requests (Auth Required - Ride Owner Only)
```http
GET /api/v1/tawsela/rides/{id}/requests
Authorization: Bearer {token}
```

---

### 10. Accept Request (Auth Required - Ride Owner Only)
```http
POST /api/v1/tawsela/requests/{id}/accept
Authorization: Bearer {token}
```

---

### 11. Reject Request (Auth Required - Ride Owner Only)
```http
POST /api/v1/tawsela/requests/{id}/reject
Authorization: Bearer {token}
```

---

### 12. Cancel Request (Auth Required - Request Owner Only)
```http
POST /api/v1/tawsela/requests/{id}/cancel
Authorization: Bearer {token}
```

---

## 💬 Messages Endpoints

### 13. Get Messages (Auth Required)
```http
GET /api/v1/tawsela/messages?ride_id={ride_id}&user_id={user_id}
Authorization: Bearer {token}
```

**Query Parameters:**
- `ride_id` (optional) - Filter by ride
- `user_id` (optional) - Filter by conversation with user

---

### 14. Send Message (Auth Required)
```http
POST /api/v1/tawsela/messages
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "ride_id": 1,
  "receiver_id": 2,
  "request_id": 1,
  "message": "مرحباً، متى ستبدأ الرحلة؟"
}
```

---

### 15. Get Conversations (Auth Required)
```http
GET /api/v1/tawsela/conversations
Authorization: Bearer {token}
```

---

## 🧪 Testing Workflow

### Complete Test Scenario

#### 1. Create User 1 (Driver)
```bash
# Register or login to get token
POST /api/v1/auth/login
{
  "email": "driver@example.com",
  "password": "password"
}
# Save the token
```

#### 2. Driver Creates a Ride
```bash
POST /api/v1/tawsela/rides
Authorization: Bearer {driver_token}
{
  "city_id": 1,
  "car_model": "تويوتا كورولا",
  "car_year": 2020,
  "car_color": "أبيض",
  "available_seats": 3,
  "start_latitude": 30.0444,
  "start_longitude": 31.2357,
  "start_address": "مصر الجديدة، القاهرة",
  "destination_latitude": 29.9602,
  "destination_longitude": 31.2569,
  "destination_address": "المعادي، القاهرة",
  "price": 50.00,
  "price_type": "negotiable",
  "price_unit": "per_person",
  "departure_time": "2025-12-31 10:00:00"
}
# Note the ride ID from response
```

#### 3. Create User 2 (Passenger)
```bash
POST /api/v1/auth/login
{
  "email": "passenger@example.com",
  "password": "password"
}
# Save the token
```

#### 4. Passenger Searches for Rides
```bash
GET /api/v1/tawsela/rides?city_id=1
# No auth required
```

#### 5. Passenger Views Ride Details
```bash
GET /api/v1/tawsela/rides/1
# No auth required
```

#### 6. Passenger Requests to Join
```bash
POST /api/v1/tawsela/rides/1/request
Authorization: Bearer {passenger_token}
{
  "pickup_latitude": 30.0500,
  "pickup_longitude": 31.2400,
  "pickup_address": "النزهة الجديدة",
  "passengers_count": 2,
  "offered_price": 45.00,
  "message": "أود الانضمام"
}
# Note the request ID from response
```

#### 7. Driver Views Requests
```bash
GET /api/v1/tawsela/rides/1/requests
Authorization: Bearer {driver_token}
```

#### 8. Driver Accepts Request
```bash
POST /api/v1/tawsela/requests/1/accept
Authorization: Bearer {driver_token}
```

#### 9. Passenger Sends Message
```bash
POST /api/v1/tawsela/messages
Authorization: Bearer {passenger_token}
{
  "ride_id": 1,
  "receiver_id": 1,
  "request_id": 1,
  "message": "شكراً على القبول. متى نلتقي؟"
}
```

#### 10. Driver Views Messages
```bash
GET /api/v1/tawsela/messages?ride_id=1&user_id=2
Authorization: Bearer {driver_token}
```

---

## 📊 Response Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## ✅ Validation Rules

### Create Ride
- `city_id`: required, exists
- `car_model`: required, string, max:255
- `car_year`: required, integer, 1900-2026
- `car_color`: required, string, max:50
- `available_seats`: required, integer, 1-10
- `start_latitude`: required, numeric, -90 to 90
- `start_longitude`: required, numeric, -180 to 180
- `start_address`: required, string, max:500
- `destination_latitude`: required, numeric, -90 to 90
- `destination_longitude`: required, numeric, -180 to 180
- `destination_address`: required, string, max:500
- `price`: required, numeric, min:0
- `price_type`: required, in:fixed,negotiable
- `price_unit`: required, in:per_person,per_trip
- `departure_time`: required, date, after:now
- `notes`: nullable, string, max:1000

### Request to Join
- `pickup_latitude`: required, numeric, -90 to 90
- `pickup_longitude`: required, numeric, -180 to 180
- `pickup_address`: required, string, max:500
- `passengers_count`: required, integer, min:1
- `message`: required, string, max:1000
- `offered_price`: nullable, numeric, min:0

---

## 🔧 Postman Collection

You can import this into Postman for easier testing:

```json
{
  "info": {
    "name": "Tawsela API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Get Rides",
      "request": {
        "method": "GET",
        "url": "{{baseUrl}}/tawsela/rides"
      }
    },
    {
      "name": "Create Ride",
      "request": {
        "method": "POST",
        "url": "{{baseUrl}}/tawsela/rides",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"city_id\": 1,\n  \"car_model\": \"تويوتا كورولا\"\n}"
        }
      }
    }
  ],
  "variable": [
    {
      "key": "baseUrl",
      "value": "http://localhost:8000/api/v1"
    }
  ]
}
```

---

**Happy Testing! 🚀**
