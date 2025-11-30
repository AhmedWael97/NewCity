<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes (no authentication required)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('/forgot-password', [App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
    });

    // Public city and shop data
    Route::get('/cities', [App\Http\Controllers\Api\CityController::class, 'index']);
    Route::get('/cities/{city:id}', [App\Http\Controllers\Api\CityController::class, 'show']);
    
    // City landing page endpoints (bind by numeric id)
    Route::get('/cities/{city:id}/featured-shops', [App\Http\Controllers\Api\CityController::class, 'featuredShops']);
    Route::get('/cities/{city:id}/latest-shops', [App\Http\Controllers\Api\CityController::class, 'latestShops']);
    Route::get('/cities/{city:id}/statistics', [App\Http\Controllers\Api\CityController::class, 'statistics']);
    Route::get('/cities/{city:id}/banners', [App\Http\Controllers\Api\CityController::class, 'banners']);
    Route::get('/cities/{city:id}/services', [App\Http\Controllers\Api\CityController::class, 'services']);
    
    Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/shops', [App\Http\Controllers\Api\ShopController::class, 'index']);
    Route::get('/shops/{shop}', [App\Http\Controllers\Api\ShopController::class, 'show']);
    Route::get('/shops/{shop}/ratings', [App\Http\Controllers\Api\ShopController::class, 'ratings']);
    Route::get('/shops/featured', [App\Http\Controllers\Api\ShopController::class, 'featured']);
    Route::get('/shops/search/nearby', [App\Http\Controllers\Api\ShopController::class, 'nearby']);
    
    // Shop reviews (public read)
    Route::get('/shops/{shopId}/reviews', [App\Http\Controllers\Api\ShopReviewController::class, 'index']);
    
    // User Services - Public endpoints
    Route::get('/user-services', [App\Http\Controllers\Api\UserServiceApiController::class, 'index']);
    Route::get('/user-services/{userService}', [App\Http\Controllers\Api\UserServiceApiController::class, 'show']);
    Route::get('/service-categories', [App\Http\Controllers\Api\UserServiceApiController::class, 'categories']);
    Route::post('/user-services/{userService}/contact', [App\Http\Controllers\Api\UserServiceApiController::class, 'recordContact']);
    
    // Marketplace - Public endpoints
    Route::get('/marketplace', [App\Http\Controllers\Api\MarketplaceController::class, 'index']);
    Route::get('/marketplace/sponsored', [App\Http\Controllers\Api\MarketplaceController::class, 'sponsored']);
    Route::get('/marketplace/{id}', [App\Http\Controllers\Api\MarketplaceController::class, 'show']);
    Route::post('/marketplace/{id}/contact', [App\Http\Controllers\Api\MarketplaceController::class, 'recordContact']);
    
    // Marketplace Sponsorship Packages (public)
    Route::get('/marketplace/sponsorship-packages', [App\Http\Controllers\Api\MarketplaceSponsorshipController::class, 'packages']);
    
    // Search endpoints
    Route::get('/search', [App\Http\Controllers\Api\SearchController::class, 'search']);
    Route::get('/search/suggestions', [App\Http\Controllers\Api\SearchController::class, 'suggestions']);
    
    // City selection for modal (optimized)
    Route::get('/cities-selection', [App\Http\Controllers\Api\CityController::class, 'forSelection']);

    // News endpoints (public - no auth required)
    Route::prefix('news')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\NewsController::class, 'index']);
        Route::get('/latest', [App\Http\Controllers\Api\NewsController::class, 'latest']);
        Route::get('/featured', [App\Http\Controllers\Api\NewsController::class, 'featured']);
        Route::get('/categories/list', [App\Http\Controllers\Api\NewsController::class, 'categories']);
        Route::get('/category/{slug}', [App\Http\Controllers\Api\NewsController::class, 'byCategory']);
        Route::get('/{slug}', [App\Http\Controllers\Api\NewsController::class, 'show']);
    });
    
    // News by city
    Route::get('/cities/{city_id}/news', [App\Http\Controllers\Api\NewsController::class, 'byCity']);

    // User tracking (public endpoint - no auth required)
    Route::post('/track', [App\Http\Controllers\Api\TrackingController::class, 'track']);

    // Guest device token registration (public endpoint - no auth required)
    Route::post('/guest-device-tokens', [App\Http\Controllers\Api\DeviceTokenController::class, 'storeGuest']);

    // App Settings - Public endpoints for mobile app
    Route::prefix('app-settings')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\AppSettingsController::class, 'index']);
        Route::get('/check-update', [App\Http\Controllers\Api\AppSettingsController::class, 'checkUpdate']);
        Route::get('/maintenance-status', [App\Http\Controllers\Api\AppSettingsController::class, 'maintenanceStatus']);
    });

    // Advertisement endpoints (public - no auth required)
    Route::prefix('ads')->group(function () {
        // Get advertisements
        Route::get('/', [App\Http\Controllers\Api\AdvertisementController::class, 'index']);
        Route::get('/hero', [App\Http\Controllers\Api\AdvertisementController::class, 'hero']);
        Route::get('/banner', [App\Http\Controllers\Api\AdvertisementController::class, 'banner']);
        Route::get('/sidebar', [App\Http\Controllers\Api\AdvertisementController::class, 'sidebar']);
        
        // Track ad interactions
        Route::post('/{adId}/impression', [App\Http\Controllers\Api\AdvertisementController::class, 'recordImpression']);
        Route::post('/{adId}/click', [App\Http\Controllers\Api\AdvertisementController::class, 'recordClick']);
    });

    // Mobile App Control - Secured endpoints with API key
    Route::prefix('mobile')->middleware(['secure.api'])->group(function () {
        // Public mobile endpoints (no user auth required, but need API key)
        Route::get('/config', [App\Http\Controllers\Api\MobileAppController::class, 'getConfig']);
        Route::post('/status', [App\Http\Controllers\Api\MobileAppController::class, 'checkStatus']);
        Route::get('/health', [App\Http\Controllers\Api\MobileAppController::class, 'health']);
        
        // Device registration (can be public or authenticated)
        Route::post('/device/register', [App\Http\Controllers\Api\MobileAppController::class, 'registerDevice']);
        Route::post('/device/update', [App\Http\Controllers\Api\MobileAppController::class, 'updateDevice']);
        Route::post('/device/unregister', [App\Http\Controllers\Api\MobileAppController::class, 'unregisterDevice']);
        Route::post('/notification/opened', [App\Http\Controllers\Api\MobileAppController::class, 'notificationOpened']);
    });

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth user routes
        Route::prefix('auth')->group(function () {
            Route::get('/user', [App\Http\Controllers\Api\AuthController::class, 'user']);
            Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
            Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
            Route::put('/profile', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
            Route::post('/change-password', [App\Http\Controllers\Api\AuthController::class, 'changePassword']);
        });

        // Shop ratings
        Route::post('/shops/{shop}/ratings', [App\Http\Controllers\Api\ShopController::class, 'rate']);

        // Shop reviews
        Route::prefix('shops/{shopId}')->group(function () {
            Route::post('/reviews', [App\Http\Controllers\Api\ShopReviewController::class, 'store']);
            Route::put('/reviews/{reviewId}', [App\Http\Controllers\Api\ShopReviewController::class, 'update']);
            Route::delete('/reviews/{reviewId}', [App\Http\Controllers\Api\ShopReviewController::class, 'destroy']);
        });

        // Shop favorites
        Route::prefix('shops/{shopId}')->group(function () {
            Route::post('/favorite', [App\Http\Controllers\Api\ShopFavoriteController::class, 'store']);
            Route::delete('/favorite', [App\Http\Controllers\Api\ShopFavoriteController::class, 'destroy']);
            Route::get('/is-favorite', [App\Http\Controllers\Api\ShopFavoriteController::class, 'check']);
        });
        
        // User favorites
        Route::get('/user/favorites', [App\Http\Controllers\Api\ShopFavoriteController::class, 'index']);

        // User profile management
        Route::prefix('user')->group(function () {
            Route::get('/profile', [App\Http\Controllers\Api\UserProfileController::class, 'show']);
            Route::put('/profile', [App\Http\Controllers\Api\UserProfileController::class, 'update']);
            Route::post('/avatar', [App\Http\Controllers\Api\UserProfileController::class, 'uploadAvatar']);
            Route::delete('/avatar', [App\Http\Controllers\Api\UserProfileController::class, 'deleteAvatar']);
            Route::put('/password', [App\Http\Controllers\Api\UserProfileController::class, 'changePassword']);
            Route::get('/statistics', [App\Http\Controllers\Api\UserProfileController::class, 'statistics']);
            Route::delete('/account', [App\Http\Controllers\Api\UserProfileController::class, 'deleteAccount']);
        });

        // User services management
        Route::prefix('my-services')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\UserServiceApiController::class, 'myServices']);
            Route::post('/', [App\Http\Controllers\Api\UserServiceApiController::class, 'store']);
            Route::put('/{userService}', [App\Http\Controllers\Api\UserServiceApiController::class, 'update']);
            Route::delete('/{userService}', [App\Http\Controllers\Api\UserServiceApiController::class, 'destroy']);
        });

        // User shops management
        Route::prefix('my-shops')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\MyShopController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\MyShopController::class, 'store']);
            Route::get('/{shop}', [App\Http\Controllers\Api\MyShopController::class, 'show']);
            Route::put('/{shop}', [App\Http\Controllers\Api\MyShopController::class, 'update']);
            Route::delete('/{shop}', [App\Http\Controllers\Api\MyShopController::class, 'destroy']);
        });

        // Marketplace - Authenticated endpoints
        Route::get('/my-marketplace-items', [App\Http\Controllers\Api\MarketplaceController::class, 'myItems']);
        Route::post('/marketplace', [App\Http\Controllers\Api\MarketplaceController::class, 'store']);
        Route::put('/marketplace/{id}', [App\Http\Controllers\Api\MarketplaceController::class, 'update']);
        Route::delete('/marketplace/{id}', [App\Http\Controllers\Api\MarketplaceController::class, 'destroy']);
        Route::post('/marketplace/{id}/mark-sold', [App\Http\Controllers\Api\MarketplaceController::class, 'markAsSold']);

        // Marketplace Sponsorships - Authenticated endpoints
        Route::get('/my-marketplace-sponsorships', [App\Http\Controllers\Api\MarketplaceSponsorshipController::class, 'mySponsorships']);
        Route::post('/marketplace/{itemId}/sponsor', [App\Http\Controllers\Api\MarketplaceSponsorshipController::class, 'purchase']);
        Route::get('/marketplace/{itemId}/sponsorships', [App\Http\Controllers\Api\MarketplaceSponsorshipController::class, 'itemSponsorships']);
        Route::get('/marketplace/sponsorships/{id}', [App\Http\Controllers\Api\MarketplaceSponsorshipController::class, 'show']);
        Route::post('/marketplace/sponsorships/{id}/renew', [App\Http\Controllers\Api\MarketplaceSponsorshipController::class, 'renew']);
        Route::post('/marketplace/sponsorships/{id}/cancel', [App\Http\Controllers\Api\MarketplaceSponsorshipController::class, 'cancel']);
        Route::get('/marketplace/sponsorships/stats', [App\Http\Controllers\Api\MarketplaceSponsorshipController::class, 'stats']);

        // Device tokens for push notifications
        Route::prefix('device-tokens')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\DeviceTokenController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\DeviceTokenController::class, 'store']);
            Route::delete('/', [App\Http\Controllers\Api\DeviceTokenController::class, 'destroy']);
        });

        // Notification actions
        Route::post('/notifications/opened', [App\Http\Controllers\Api\NotificationController::class, 'opened']);

        // Admin routes
        Route::middleware('role:admin,super_admin')->prefix('admin')->name('api.admin.')->group(function () {
            // Cities management
            Route::apiResource('cities', App\Http\Controllers\Api\Admin\CityController::class);
            
            // Categories management
            Route::apiResource('categories', App\Http\Controllers\Api\Admin\CategoryController::class);
            
            // Shops management
            Route::get('/shops', [App\Http\Controllers\Api\Admin\ShopController::class, 'index']);
            Route::get('/shops/{shop}', [App\Http\Controllers\Api\Admin\ShopController::class, 'show']);
            Route::put('/shops/{shop}/verify', [App\Http\Controllers\Api\Admin\ShopController::class, 'verify']);
            Route::put('/shops/{shop}/feature', [App\Http\Controllers\Api\Admin\ShopController::class, 'feature']);
            Route::put('/shops/{shop}/activate', [App\Http\Controllers\Api\Admin\ShopController::class, 'activate']);
            Route::delete('/shops/{shop}', [App\Http\Controllers\Api\Admin\ShopController::class, 'destroy']);
            
            // Users management
            Route::get('/users', [App\Http\Controllers\Api\Admin\UserController::class, 'index']);
            Route::get('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'show']);
            Route::put('/users/{user}/activate', [App\Http\Controllers\Api\Admin\UserController::class, 'activate']);
            Route::delete('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'destroy']);
            
            // App Settings Management
            Route::prefix('app-settings')->group(function () {
                // Settings CRUD
                Route::get('/', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'index']);
                Route::put('/', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'update']);
                Route::post('/upload-icon', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'uploadIcon']);
                Route::post('/upload-logo', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'uploadLogo']);
                Route::get('/statistics', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'statistics']);
                
                // Push Notifications
                Route::get('/notifications', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'notifications']);
                Route::post('/notifications', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'createNotification']);
                Route::post('/notifications/{notification}/send', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'sendNotification']);
                Route::delete('/notifications/{notification}', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'deleteNotification']);
                Route::post('/test-notification', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'testNotification']);
                
                // Device Tokens
                Route::get('/devices', [App\Http\Controllers\Api\Admin\AppSettingsController::class, 'devices']);
            });
        });
    });
});