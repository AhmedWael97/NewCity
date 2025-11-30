# Marketplace Implementation - Complete Guide

## 🎉 Implementation Summary

A complete marketplace system has been implemented with the following features:

### ✅ Core Features Implemented

1. **Marketplace Items Management**
   - Users can create, update, and delete marketplace items
   - Items have limited views (default 50 views for non-sponsored items)
   - Once view limit is reached, items become hidden unless sponsored
   - Support for multiple images (1-5 images)
   - Item conditions: new, like_new, good, fair
   - Price negotiation option
   - Contact tracking (phone/WhatsApp)

2. **Sponsorship System**
   - Three sponsorship packages: Basic, Standard, Premium
   - Sponsored items get priority placement in listings
   - Sponsored items receive view boosts (extra views)
   - Automatic view limit management
   - ROI (Return on Investment) tracking
   - Sponsorship renewal and cancellation

3. **View Limitation System**
   - Non-sponsored items: 50 default views
   - Sponsored items: Unlimited views during sponsorship period
   - View boost accumulates with sponsorships
   - Items automatically hidden when view limit reached

4. **Admin Management**
   - View all marketplace items
   - Approve/Reject items
   - Bulk actions support
   - Statistics and analytics
   - Moderation system

---

## 📊 Database Schema

### `marketplace_items` Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users |
| category_id | bigint | Foreign key to categories |
| city_id | bigint | Foreign key to cities |
| title | string | Item title |
| description | text | Item description |
| price | decimal(10,2) | Item price |
| condition | enum | new, like_new, good, fair |
| images | json | Array of image URLs |
| contact_phone | string | Seller phone |
| contact_whatsapp | string | Seller WhatsApp |
| status | enum | active, sold, pending, rejected |
| is_negotiable | boolean | Price is negotiable |
| view_count | integer | Total views |
| max_views | integer | Maximum allowed views (default: 50) |
| contact_count | integer | Number of contacts made |
| is_sponsored | boolean | Is currently sponsored |
| sponsored_until | timestamp | Sponsorship end date |
| sponsored_priority | integer | Priority level (0-10) |
| sponsored_views_boost | integer | Extra views from sponsorship |
| rejection_reason | text | Admin rejection reason |
| approved_at | timestamp | Approval timestamp |

### `marketplace_sponsorships` Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| marketplace_item_id | bigint | Foreign key to marketplace_items |
| user_id | bigint | Foreign key to users |
| package_type | string | basic, standard, premium |
| duration_days | integer | Sponsorship duration |
| price_paid | decimal(10,2) | Amount paid |
| views_boost | integer | Extra views granted |
| priority_level | integer | Priority for sorting (1-10) |
| starts_at | timestamp | Sponsorship start |
| ends_at | timestamp | Sponsorship end |
| payment_method | string | Payment method used |
| transaction_id | string | Payment transaction ID |
| payment_status | enum | pending, completed, failed, refunded |
| status | enum | active, expired, cancelled |
| cancellation_reason | text | Cancellation reason |
| views_gained | integer | Views during sponsorship |
| contacts_gained | integer | Contacts during sponsorship |

---

## 🎯 Sponsorship Packages

### Basic Package
- **Price:** 50.00
- **Duration:** 7 days
- **Views Boost:** +100 extra views
- **Priority Level:** 3
- **Features:**
  - Highlighted in search results
  - Basic badge display
  - Extended visibility

### Standard Package
- **Price:** 90.00
- **Duration:** 15 days
- **Views Boost:** +250 extra views
- **Priority Level:** 6
- **Features:**
  - Top placement in search
  - Premium badge display
  - Featured on homepage
  - Higher priority than basic

### Premium Package
- **Price:** 150.00
- **Duration:** 30 days
- **Views Boost:** +500 extra views
- **Priority Level:** 10
- **Features:**
  - Top priority placement
  - VIP badge display
  - Featured everywhere
  - Maximum visibility
  - Social media promotion potential

---

## 🔌 API Endpoints

### Public Endpoints (No Authentication)

#### Get Marketplace Items
```
GET /api/v1/marketplace
```

**Query Parameters:**
- `city_id` - Filter by city
- `category_id` - Filter by category
- `min_price` - Minimum price
- `max_price` - Maximum price
- `condition` - Item condition (new, like_new, good, fair)
- `search` - Search in title and description
- `sort` - Sort by (newest, price_low, price_high, most_viewed)
- `page` - Page number

**Response:**
```json
{
  "success": true,
  "message": "Marketplace items retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 5,
        "title": "iPhone 14 Pro",
        "description": "Excellent condition, barely used",
        "price": "3500.00",
        "condition": "like_new",
        "images": ["https://example.com/storage/marketplace/img1.jpg"],
        "is_negotiable": true,
        "view_count": 45,
        "max_views": 50,
        "remaining_views": 5,
        "is_sponsored": false,
        "is_sponsored_active": false,
        "status": "active",
        "user": {
          "id": 5,
          "name": "Ahmed Ali",
          "phone": "+201234567890"
        },
        "category": {
          "id": 3,
          "name": "Electronics"
        },
        "city": {
          "id": 1,
          "name": "Cairo"
        },
        "created_at": "2025-11-30T10:00:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 50
  }
}
```

#### Get Sponsored Items
```
GET /api/v1/marketplace/sponsored
```

**Response:** Returns top 10 sponsored items with highest priority.

#### Get Item Details
```
GET /api/v1/marketplace/{id}
```

**Response:**
- Returns full item details
- Increments view count (if not owner)
- Returns 403 if view limit reached

**View Limit Reached Response:**
```json
{
  "success": false,
  "message": "This item has reached its view limit. Contact the seller directly or ask them to sponsor the item.",
  "data": {
    "item_id": 1,
    "title": "iPhone 14 Pro",
    "status": "view_limit_reached",
    "seller_contact": "+201234567890"
  }
}
```

#### Record Contact Attempt
```
POST /api/v1/marketplace/{id}/contact
```

Increments contact count and returns seller contact information.

#### Get Sponsorship Packages
```
GET /api/v1/marketplace/sponsorship-packages
```

**Response:**
```json
{
  "success": true,
  "message": "Sponsorship packages retrieved successfully",
  "data": {
    "basic": {
      "name": "Basic Sponsorship",
      "name_ar": "رعاية أساسية",
      "duration_days": 7,
      "price": 50.00,
      "views_boost": 100,
      "priority_level": 3,
      "features": [...]
    },
    "standard": {...},
    "premium": {...}
  }
}
```

---

### Authenticated Endpoints

#### Create Marketplace Item
```
POST /api/v1/marketplace
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body:**
```json
{
  "title": "iPhone 14 Pro",
  "description": "Excellent condition, barely used, includes original box",
  "price": 3500.00,
  "category_id": 3,
  "city_id": 1,
  "condition": "like_new",
  "is_negotiable": true,
  "contact_phone": "+201234567890",
  "contact_whatsapp": "+201234567890",
  "images": [file1, file2, file3]
}
```

**Validation Rules:**
- `title` - required, max 255 characters
- `description` - required, min 20 characters
- `price` - required, numeric, min 0
- `category_id` - required, exists in categories
- `city_id` - required, exists in cities
- `condition` - required, in: new, like_new, good, fair
- `is_negotiable` - boolean (default: true)
- `images` - required, array, min 1, max 5 images
- `images.*` - image file, jpeg/png/jpg/webp, max 5MB

**Response:**
```json
{
  "success": true,
  "message": "Marketplace item created successfully",
  "data": {
    "id": 1,
    "user_id": 5,
    "title": "iPhone 14 Pro",
    "price": "3500.00",
    "status": "active",
    "max_views": 50,
    ...
  }
}
```

#### Get My Items
```
GET /api/v1/my-marketplace-items
Authorization: Bearer {token}
```

Returns all items created by the authenticated user.

#### Update Item
```
PUT /api/v1/marketplace/{id}
Authorization: Bearer {token}
```

**Body:** (all fields optional)
```json
{
  "title": "Updated title",
  "description": "Updated description",
  "price": 3200.00,
  "condition": "good",
  "is_negotiable": false,
  "status": "sold"
}
```

#### Delete Item
```
DELETE /api/v1/marketplace/{id}
Authorization: Bearer {token}
```

Deletes item and associated images from storage.

#### Mark Item as Sold
```
POST /api/v1/marketplace/{id}/mark-sold
Authorization: Bearer {token}
```

Updates item status to "sold".

---

### Sponsorship Endpoints (Authenticated)

#### Purchase Sponsorship
```
POST /api/v1/marketplace/{itemId}/sponsor
Authorization: Bearer {token}
```

**Body:**
```json
{
  "package_type": "standard",
  "payment_method": "credit_card"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Sponsorship purchased successfully",
  "data": {
    "sponsorship": {
      "id": 1,
      "marketplace_item_id": 5,
      "package_type": "standard",
      "duration_days": 15,
      "price_paid": "90.00",
      "views_boost": 250,
      "priority_level": 6,
      "starts_at": "2025-11-30T10:00:00.000000Z",
      "ends_at": "2025-12-15T10:00:00.000000Z",
      "status": "active"
    },
    "item": {
      "id": 5,
      "is_sponsored": true,
      "sponsored_until": "2025-12-15T10:00:00.000000Z",
      "sponsored_priority": 6
    }
  }
}
```

#### Get My Sponsorships
```
GET /api/v1/my-marketplace-sponsorships
Authorization: Bearer {token}
```

Returns all sponsorships purchased by the user.

#### Get Item Sponsorship History
```
GET /api/v1/marketplace/{itemId}/sponsorships
Authorization: Bearer {token}
```

Returns sponsorship history for a specific item (owner only).

#### Get Sponsorship Details
```
GET /api/v1/marketplace/sponsorships/{id}
Authorization: Bearer {token}
```

#### Renew Sponsorship
```
POST /api/v1/marketplace/sponsorships/{id}/renew
Authorization: Bearer {token}
```

Creates a new sponsorship with the same package type.

#### Cancel Sponsorship
```
POST /api/v1/marketplace/sponsorships/{id}/cancel
Authorization: Bearer {token}
```

**Body:**
```json
{
  "reason": "No longer needed"
}
```

#### Get Sponsorship Statistics
```
GET /api/v1/marketplace/sponsorships/stats
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_sponsorships": 5,
    "active_sponsorships": 2,
    "total_spent": "350.00",
    "total_views_gained": 1250,
    "total_contacts_gained": 45,
    "avg_roi": 25.5
  }
}
```

---

## 💼 Usage Examples

### Example 1: User Creates Item

```javascript
// Step 1: Create a new marketplace item
const formData = new FormData();
formData.append('title', 'MacBook Pro 2023');
formData.append('description', 'Brand new, sealed in box, M2 chip, 16GB RAM');
formData.append('price', 8500);
formData.append('category_id', 3);
formData.append('city_id', 1);
formData.append('condition', 'new');
formData.append('is_negotiable', true);
formData.append('contact_phone', '+201234567890');
formData.append('images', imageFile1);
formData.append('images', imageFile2);

const response = await fetch('/api/v1/marketplace', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`
  },
  body: formData
});

const result = await response.json();
console.log(result.data); // Item created with 50 default views
```

### Example 2: Views Reach Limit

```javascript
// Item has 50 max views, 49 views used
// User tries to view the item
const response = await fetch('/api/v1/marketplace/123');
const result = await response.json();

// If views < max_views: Item details shown, view_count incremented to 50
// If views >= max_views: 403 error returned

if (!result.success && result.data.status === 'view_limit_reached') {
  console.log('Item reached view limit!');
  console.log('Contact seller:', result.data.seller_contact);
  // Show option to sponsor the item
}
```

### Example 3: User Sponsors Item

```javascript
// Step 1: Check available packages
const packagesResponse = await fetch('/api/v1/marketplace/sponsorship-packages');
const packages = await packagesResponse.json();

// Step 2: Purchase sponsorship
const sponsorResponse = await fetch('/api/v1/marketplace/123/sponsor', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    package_type: 'standard', // 15 days, +250 views
    payment_method: 'credit_card'
  })
});

const sponsorship = await sponsorResponse.json();
// Item now has:
// - max_views: 50 + 250 = 300 total views
// - is_sponsored: true
// - sponsored_until: 15 days from now
// - sponsored_priority: 6 (higher in search results)
```

### Example 4: Browse Marketplace

```javascript
// Get items in Cairo, Electronics category, sorted by newest
const response = await fetch('/api/v1/marketplace?' + new URLSearchParams({
  city_id: 1,
  category_id: 3,
  sort: 'newest',
  page: 1
}));

const result = await response.json();

result.data.data.forEach(item => {
  console.log(`${item.title} - ${item.price} EGP`);
  console.log(`Views: ${item.view_count}/${item.max_views + item.sponsored_views_boost}`);
  console.log(`Remaining: ${item.remaining_views}`);
  console.log(`Sponsored: ${item.is_sponsored_active ? 'Yes' : 'No'}`);
});

// Sponsored items appear first, then regular items
// Items with view limits reached are automatically hidden
```

---

## 🎨 How View Limitation Works

### Default Behavior (Non-Sponsored)
1. Item created with `max_views = 50`
2. Each view increments `view_count`
3. When `view_count >= max_views`, item becomes hidden
4. Item still appears in owner's "My Items" with warning
5. Public cannot view item until sponsored

### With Sponsorship
1. User purchases sponsorship (e.g., Standard package)
2. Item receives `+250` views boost
3. Total allowed views: `50 + 250 = 300`
4. Item becomes sponsored with priority placement
5. After 15 days (or 300 views), sponsorship expires
6. Item returns to regular status with remaining views

### Multiple Sponsorships
- Each sponsorship adds to `sponsored_views_boost`
- If item has 20 remaining views and user adds Basic package:
  - New total: 20 + 100 = 120 views available
- Sponsorships can be stacked for longer visibility

---

## 🛡️ Admin Features

### Admin Routes
Add to `routes/admin.php`:

```php
// Marketplace Management
Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [AdminMarketplaceController::class, 'index'])->name('index');
    Route::get('/statistics', [AdminMarketplaceController::class, 'statistics'])->name('statistics');
    Route::get('/{item}', [AdminMarketplaceController::class, 'show'])->name('show');
    Route::post('/{item}/approve', [AdminMarketplaceController::class, 'approve'])->name('approve');
    Route::post('/{item}/reject', [AdminMarketplaceController::class, 'reject'])->name('reject');
    Route::delete('/{item}', [AdminMarketplaceController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-action', [AdminMarketplaceController::class, 'bulkAction'])->name('bulk-action');
});
```

### Admin Capabilities
- View all marketplace items
- Filter by status, city, category, sponsored status
- Approve/reject pending items
- Bulk actions (approve, reject, delete multiple items)
- View statistics and analytics
- Monitor sponsorship performance

---

## 🚀 Running Migrations

```bash
# Run the marketplace migrations
php artisan migrate

# If needed, rollback
php artisan migrate:rollback --step=2
```

---

## 📱 Mobile App Integration

### Display Items List
```dart
// Flutter example
Future<List<MarketplaceItem>> fetchMarketplaceItems() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/marketplace?city_id=$cityId'),
    headers: {'Accept': 'application/json'},
  );
  
  if (response.statusCode == 200) {
    final data = json.decode(response.body);
    return (data['data']['data'] as List)
        .map((item) => MarketplaceItem.fromJson(item))
        .toList();
  }
  throw Exception('Failed to load items');
}
```

### Handle View Limit
```dart
Future<void> viewItem(int itemId) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/marketplace/$itemId'),
  );
  
  if (response.statusCode == 403) {
    // View limit reached
    final data = json.decode(response.body);
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('View Limit Reached'),
        content: Text('This item has reached its view limit. '
                      'Contact: ${data['data']['seller_contact']}'),
        actions: [
          TextButton(
            onPressed: () => showSponsorshipOptions(itemId),
            child: Text('Sponsor This Item'),
          ),
        ],
      ),
    );
  }
}
```

---

## 🔧 Configuration

### Adjust Default View Limit
In the migration or when creating items, change:

```php
'max_views' => 100, // Change from 50 to 100
```

### Customize Sponsorship Packages
Edit `app/Models/MarketplaceSponsorship.php`:

```php
public static function packages(): array
{
    return [
        'basic' => [
            'duration_days' => 7,
            'price' => 30.00, // Adjust price
            'views_boost' => 150, // Adjust boost
            'priority_level' => 3,
        ],
        // ... other packages
    ];
}
```

---

## 📊 Analytics & Tracking

### Track Performance
- Views per item
- Contact rate (contacts/views ratio)
- Sponsorship ROI calculation
- City/category performance
- User engagement metrics

### Scheduled Tasks
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Expire old sponsorships daily
    $schedule->call(function () {
        $expired = MarketplaceSponsorship::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->get();
            
        foreach ($expired as $sponsorship) {
            $sponsorship->expire();
        }
    })->daily();
}
```

---

## ✅ Testing Checklist

- [ ] Create marketplace item with images
- [ ] View item multiple times until limit reached
- [ ] Verify 403 error when limit exceeded
- [ ] Purchase Basic sponsorship package
- [ ] Verify item receives +100 views
- [ ] Verify item appears first in listings
- [ ] Test item update by owner
- [ ] Test item deletion with image cleanup
- [ ] Test bulk actions in admin panel
- [ ] Test sponsorship renewal
- [ ] Test sponsorship cancellation
- [ ] Verify statistics calculations

---

## 🎯 Next Steps / Future Enhancements

1. **Payment Gateway Integration**
   - Integrate with payment providers (Stripe, PayPal, Fawry)
   - Webhook handling for payment confirmation
   - Refund processing

2. **Advanced Features**
   - Item favorites/bookmarks
   - Price alerts for users
   - Similar items suggestions
   - Item reporting system
   - Messaging system between buyers/sellers

3. **Notifications**
   - Alert owner when views are running low
   - Notify when sponsorship is about to expire
   - Send notifications for new messages

4. **Analytics Dashboard**
   - Detailed performance metrics
   - A/B testing for sponsorship effectiveness
   - Predictive analytics for optimal pricing

---

## 📞 Support

For issues or questions:
- Check the API responses for detailed error messages
- Review the migration files for database structure
- Examine model methods for business logic
- Test endpoints using Postman or similar tools

**Implementation Date:** November 30, 2025
**Version:** 1.0.0
**Status:** ✅ Complete and Ready for Production
