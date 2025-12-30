# Endpoint Coverage Report
**Generated:** November 10, 2025  
**API Base URL:** `http://127.0.0.1:8000/api/v1`

## ✅ Implemented Endpoints

### Authentication Endpoints
| Endpoint | Method | Status | Service Method | Used In Screen |
|----------|--------|--------|----------------|----------------|
| `/api/v1/auth/register` | POST | ✅ | `AuthService.register()` | `register_screen.dart` |
| `/api/v1/auth/login` | POST | ✅ | `AuthService.login()` | `login_screen.dart` |
| `/api/v1/auth/me` | GET | ✅ | `AuthService.getMe()` | Profile screens |
| `/api/v1/auth/logout` | POST | ✅ | `AuthService.logout()` | Profile/Settings |

### City Endpoints
| Endpoint | Method | Status | Service Method | Used In Screen |
|----------|--------|--------|----------------|----------------|
| `/api/v1/cities` | GET | ✅ | `CityService.getCities()` | `city_selection_screen.dart` |
| `/api/v1/cities/{city}` | GET | ✅ | `CityService.getCityById()` | `city_landing_page.dart` |
| `/api/v1/cities/{city}/featured-shops` | GET | ✅ | `CityService.getCityFeaturedShops()` | Ready (not yet used in UI) |
| `/api/v1/cities/{city}/latest-shops` | GET | ✅ | `CityService.getCityLatestShops()` | Ready (not yet used in UI) |
| `/api/v1/cities/{city}/statistics` | GET | ✅ | `CityService.getCityStatistics()` | Ready (not yet used in UI) |
| `/api/v1/cities/{city}/banners` | GET | ✅ | `CityService.getCityBanners()` | Ready (not yet used in UI) |

### Shop Endpoints
| Endpoint | Method | Status | Service Method | Used In Screen |
|----------|--------|--------|----------------|----------------|
| `/api/v1/shops` | GET | ✅ | `ShopService.getShops()` | `shops_screen.dart`, `city_landing_page.dart` |
| `/api/v1/shops/{id}` | GET | ⚠️ | `ShopService.getShopById()` | `shop_details_screen.dart` (needs testing) |

### Category Endpoints
| Endpoint | Method | Status | Service Method | Used In Screen |
|----------|--------|--------|----------------|----------------|
| `/api/v1/categories` | GET | ✅ | `CategoryService.getCategories()` | `shops_screen.dart` (filters) |

### Search Endpoints
| Endpoint | Method | Status | Service Method | Used In Screen |
|----------|--------|--------|----------------|----------------|
| `/api/v1/search` | GET | ✅ | `SearchService.search()` | `search_screen.dart` |
| `/api/v1/search/suggestions` | GET | ✅ | `SearchService.getSuggestions()` | `search_screen.dart` |

### User Services Endpoints
| Endpoint | Method | Status | Service Method | Used In Screen |
|----------|--------|--------|----------------|----------------|
| `/api/v1/user-services` | GET | ✅ | `UserServiceApi.getUserServices()` | `services_screen.dart` |
| `/api/v1/user-services` | POST | ✅ | `UserServiceApi.createService()` | Service creation form |
| `/api/v1/user-services/{id}` | GET | ✅ | `UserServiceApi.getServiceById()` | Service details |
| `/api/v1/user-services/{id}` | PUT | ✅ | `UserServiceApi.updateService()` | Service edit form |
| `/api/v1/user-services/{id}` | DELETE | ✅ | `UserServiceApi.deleteService()` | `my_services_screen.dart` |
| `/api/v1/user-services/{id}/contact` | POST | ✅ | `UserServiceApi.contactService()` | Service contact form |
| `/api/my-services` | GET | ✅ | `UserServiceApi.getMyServices()` | `my_services_screen.dart` |
| `/api/service-categories` | GET | ✅ | `UserServiceApi.getServiceCategories()` | Service forms |

---

## ❌ Missing Endpoints (Not in API Docs but might be needed)

### Shop-Related
| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/api/v1/shops/{id}/reviews` | GET | Get shop reviews | ✅ **IMPLEMENTED** |
| `/api/v1/shops/{id}/reviews` | POST | Submit shop review | ✅ **IMPLEMENTED** |
| `/api/v1/shops/{id}/reviews/{reviewId}` | PUT | Update shop review | ✅ **IMPLEMENTED** |
| `/api/v1/shops/{id}/reviews/{reviewId}` | DELETE | Delete shop review | ✅ **IMPLEMENTED** |
| `/api/v1/shops/{id}/favorite` | POST | Add to favorites | ✅ **IMPLEMENTED** |
| `/api/v1/shops/{id}/favorite` | DELETE | Remove from favorites | ✅ **IMPLEMENTED** |
| `/api/v1/shops/{id}/is-favorite` | GET | Check if shop is favorited | ✅ **IMPLEMENTED** |
| `/api/v1/shops/{id}/coupons` | GET | Get shop coupons/offers | ❌ Not Implemented |
| `/api/v1/shops/{id}/products` | GET | Get shop products | ❌ Not Implemented |

### User Profile
| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/api/v1/user/profile` | GET | Get user profile | ✅ **IMPLEMENTED** |
| `/api/v1/user/profile` | PUT | Update user profile | ✅ **IMPLEMENTED** |
| `/api/v1/user/avatar` | POST | Upload user avatar | ✅ **IMPLEMENTED** |
| `/api/v1/user/avatar` | DELETE | Delete user avatar | ✅ **IMPLEMENTED** |
| `/api/v1/user/password` | PUT | Change password | ✅ **IMPLEMENTED** |
| `/api/v1/user/statistics` | GET | Get user statistics | ✅ **IMPLEMENTED** |
| `/api/v1/user/account` | DELETE | Delete user account | ✅ **IMPLEMENTED** |
| `/api/v1/user/favorites` | GET | Get user favorites | ✅ **IMPLEMENTED** |
| `/api/v1/user/orders` | GET | Get order history | ❌ Not Implemented |

### Notifications
| Endpoint | Method | Purpose | Priority |
|----------|--------|---------|----------|
| `/api/v1/notifications` | GET | Get user notifications | 🟡 MEDIUM |
| `/api/v1/notifications/{id}/read` | POST | Mark as read | 🟢 LOW |

### Payment Methods
| Endpoint | Method | Purpose | Priority |
|----------|--------|---------|----------|
| `/api/v1/user/payment-methods` | GET | Get saved payment methods | 🟡 MEDIUM |
| `/api/v1/user/payment-methods` | POST | Add payment method | 🟡 MEDIUM |
| `/api/v1/user/payment-methods/{id}` | DELETE | Delete payment method | 🟡 MEDIUM |

---

## 🔧 Screens Requiring Endpoint Updates

### 1. **shop_details_screen.dart** 🔴 CRITICAL
**Current Status:** Likely using mock data or incomplete  
**Missing Endpoints:**
- Shop coupons/offers list
- Shop products list
- Shop reviews with pagination
- Add/Remove favorite functionality

**Recommendation:** 
```dart
// Add to ShopService
Future<Map<String, dynamic>> getShopCoupons(int shopId) async { ... }
Future<Map<String, dynamic>> getShopProducts(int shopId) async { ... }
Future<Map<String, dynamic>> getShopReviews(int shopId, {int page = 1}) async { ... }
Future<Map<String, dynamic>> submitReview(int shopId, int rating, String comment) async { ... }
```

### 2. **favorites_screen.dart** 🟡 MEDIUM
**Current Status:** Not connected to API  
**Missing Endpoints:**
- Get user favorites
- Add/Remove from favorites

### 3. **order_history_screen.dart** 🟡 MEDIUM
**Current Status:** Not connected to API  
**Missing Endpoints:**
- Get order history with pagination
- Get order details

### 4. **profile_screen.dart** & **edit_profile_screen.dart** 🔴 HIGH
**Current Status:** Using `auth/me` endpoint  
**Recommendation:** Use dedicated `/user/profile` endpoints if available

### 5. **notifications_screen.dart** 🟡 MEDIUM
**Current Status:** Not connected to API  
**Missing Endpoints:**
- Get notifications
- Mark as read/unread

### 6. **payment_methods_screen.dart** 🟡 MEDIUM
**Current Status:** Not connected to API  
**Missing Endpoints:**
- CRUD operations for payment methods

### 7. **city_landing_page.dart** ⚠️ NEEDS UPDATE
**Current Status:** Using general `/shops` endpoint  
**Recommendation:** Use new city-specific endpoints:
- Replace shop fetching with `getCityFeaturedShops()` and `getCityLatestShops()`
- Add city statistics display using `getCityStatistics()`
- Add promotional banners using `getCityBanners()`

---

## 📊 Implementation Status Summary

| Category | Implemented | Ready (Not Used) | Missing |
|----------|-------------|------------------|---------|
| **Authentication** | 4/4 | 0 | 0 |
| **Cities** | 6/6 | 4 (new endpoints) | 0 |
| **Shops** | 2/2 | 0 | 2 (coupons, products) |
| **Shop Reviews** | 4/4 ✅ **NEW** | 0 | 0 |
| **Shop Favorites** | 4/4 ✅ **NEW** | 0 | 0 |
| **Categories** | 1/1 | 0 | 0 |
| **Search** | 2/2 | 0 | 0 |
| **User Services** | 8/8 | 0 | 0 |
| **User Profile** | 7/7 ✅ **NEW** | 0 | 0 |
| **Notifications** | 0 | 0 | 2 |
| **Orders** | 0 | 0 | 2 |
| **Payment** | 0 | 0 | 3 |
| **TOTAL** | **39** ✅ (+15) | **4** | **9** |

---

## 🎯 Priority Action Items

### Immediate (Current Sprint)
1. ✅ **Integrate city-specific endpoints** in `city_landing_page.dart`
   - Use `getCityFeaturedShops()` instead of filtering all shops
   - Use `getCityLatestShops()` for recent additions
   - Display city statistics
   - Add promotional banners

2. � **Complete shop_details_screen.dart**
   - 🔲 Add coupons/offers section (need backend endpoint)
   - 🔲 Add products section (need backend endpoint)
   - ✅ Add reviews section (**backend ready**)
   - ✅ Implement favorite toggle (**backend ready**)

3. 🔴 **Test authentication flow end-to-end**
   - Register → Login → Profile → Logout
   - Token persistence
   - Protected route handling

### Short Term (Next Sprint)
4. ✅ **Implement favorites functionality** - **COMPLETED**
   - ✅ Add favorites service
   - ✅ Backend endpoints ready
   - 🔲 Connect favorites_screen.dart (Flutter side)
   - 🔲 Add favorite toggle UI in shop cards (Flutter side)

5. ✅ **Connect profile management** - **COMPLETED**
   - ✅ Edit profile with avatar upload
   - ✅ Password change
   - ✅ Account settings
   - ✅ User statistics endpoint
   - 🔲 Connect to Flutter UI

6. ✅ **Shop reviews functionality** - **COMPLETED**
   - ✅ Get shop reviews
   - ✅ Submit shop review
   - ✅ Update/Delete shop review
   - 🔲 Connect to Flutter UI

7. 🟡 **Add notifications**
   - Fetch and display notifications
   - Mark as read
   - Push notification setup (if applicable)

### Medium Term (Future)
7. 🟢 **Order history** (if e-commerce features exist)
8. 🟢 **Payment methods management**
9. 🟢 **Help & Support** with API integration

---

## 🧪 Testing Checklist

### City Selection Flow ✅
- [x] Cities load from API
- [x] Search filters cities
- [x] Selection persists in SharedPreferences
- [x] Navigation to home works
- [x] Change city button works

### City Landing Page ⚠️
- [x] City header displays correctly
- [x] Shops load from API
- [x] Featured shops filter by `isFeatured` flag
- [ ] Use dedicated `getCityFeaturedShops()` endpoint
- [ ] Use dedicated `getCityLatestShops()` endpoint
- [ ] Display city statistics
- [ ] Display promotional banners

### Shop List & Filters ⚠️
- [ ] Shop list loads with pagination
- [ ] Category filter works
- [ ] Search works
- [ ] City filter works
- [ ] Featured toggle works
- [ ] Location-based filtering

### Shop Details ⚠️ NEEDS FLUTTER INTEGRATION
- [ ] Shop info displays correctly
- [ ] **Coupons section** (endpoint missing)
- [ ] **Products section** (endpoint missing)
- [x] **Reviews section** ✅ **Backend Ready** (Flutter integration pending)
- [x] **Favorite toggle** ✅ **Backend Ready** (Flutter integration pending)
- [ ] Contact shop button
- [ ] Share functionality

### Authentication Flow ⚠️
- [ ] Registration works
- [ ] Login works with token storage
- [ ] Token sent with authenticated requests
- [ ] Logout clears token
- [ ] Protected routes check auth
- [ ] Token refresh (if implemented)

### Search ⚠️
- [ ] Search suggestions work
- [ ] Full search returns results
- [ ] Filters apply correctly
- [ ] Results navigate to shop details

### User Services ⚠️
- [ ] Services list loads
- [ ] Create service works
- [ ] Edit service works
- [ ] Delete service works
- [ ] My services displays user's services
- [ ] Contact service sends message

---

## 📝 Notes

1. **API Documentation Completeness**: The api-docs.json file is well-structured but missing several expected endpoints for shop details (coupons, products, reviews).

2. **City-Specific Endpoints**: Four new city endpoints are implemented in services but not yet utilized in the UI. This is good preparation for future optimization.

3. **Authentication Token**: Verify that the app is correctly sending the Bearer token with all authenticated requests. Check `AuthService` implementation.

4. **Error Handling**: All services have basic error handling, but UI screens should display user-friendly error messages.

5. **Pagination**: Most list endpoints support pagination. Ensure UI implements infinite scroll or "Load More" buttons.

6. **Image Handling**: Check if all image URLs from API are properly displayed. Add fallback images for missing/broken URLs.

7. **Arabic Support**: All endpoints return Arabic data. Verify RTL layout and Arabic font rendering.

8. **Caching Strategy**: Consider implementing local caching for:
   - Categories (rarely change)
   - City list (rarely change)
   - Featured shops (cache for 1 hour)

---

## 🔗 Backend Requirements

### Must Ask Backend Team For:

1. **Shop Details Endpoints** (Remaining):
   ```
   ❌ GET /api/v1/shops/{id}/coupons (Still needed)
   ❌ GET /api/v1/shops/{id}/products (Still needed)
   ✅ GET /api/v1/shops/{id}/reviews?page=1 (DONE)
   ✅ POST /api/v1/shops/{id}/reviews (DONE)
   ✅ PUT /api/v1/shops/{id}/reviews/{reviewId} (DONE)
   ✅ DELETE /api/v1/shops/{id}/reviews/{reviewId} (DONE)
   ```

2. **Favorites Endpoints**: ✅ **ALL COMPLETED**
   ```
   ✅ GET /api/v1/user/favorites (DONE)
   ✅ POST /api/v1/shops/{id}/favorite (DONE)
   ✅ DELETE /api/v1/shops/{id}/favorite (DONE)
   ✅ GET /api/v1/shops/{id}/is-favorite (BONUS - DONE)
   ```

3. **User Profile Endpoints**: ✅ **ALL COMPLETED**
   ```
   ✅ GET /api/v1/user/profile (DONE)
   ✅ PUT /api/v1/user/profile (DONE)
   ✅ POST /api/v1/user/avatar (DONE)
   ✅ DELETE /api/v1/user/avatar (DONE)
   ✅ PUT /api/v1/user/password (DONE)
   ✅ GET /api/v1/user/statistics (BONUS - DONE)
   ✅ DELETE /api/v1/user/account (BONUS - DONE)
   ```

4. **Clarifications Needed**:
   - Is there an order/booking system? If yes, need order endpoints
   - Are push notifications implemented? Need notification endpoints
   - Payment methods - are they saved in backend or handled by payment gateway?
   - ✅ Reviews - can users edit/delete their reviews? **YES - Implemented!**

---

**Last Updated:** November 11, 2025  
**Status:** 39 endpoints implemented (+15 new), 4 ready for use, 9 endpoints still needed  

**Recent Additions (November 11, 2025):**
- ✅ Shop Reviews API (4 endpoints)
- ✅ Shop Favorites API (4 endpoints)
- ✅ User Profile API (7 endpoints)
- 📄 Full Swagger documentation available at `/api/documentation`
