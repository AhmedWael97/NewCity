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
    Route::get('/cities/{city}', [App\Http\Controllers\Api\CityController::class, 'show']);
    
    // City landing page endpoints
    Route::get('/cities/{city}/featured-shops', [App\Http\Controllers\Api\CityController::class, 'featuredShops']);
    Route::get('/cities/{city}/latest-shops', [App\Http\Controllers\Api\CityController::class, 'latestShops']);
    Route::get('/cities/{city}/statistics', [App\Http\Controllers\Api\CityController::class, 'statistics']);
    Route::get('/cities/{city}/banners', [App\Http\Controllers\Api\CityController::class, 'banners']);
    
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
    
    // Search endpoints
    Route::get('/search', [App\Http\Controllers\Api\SearchController::class, 'search']);
    Route::get('/search/suggestions', [App\Http\Controllers\Api\SearchController::class, 'suggestions']);
    
    // City selection for modal (optimized)
    Route::get('/cities-selection', [App\Http\Controllers\Api\CityController::class, 'forSelection']);

    // Advertisement tracking (public endpoints)
    Route::prefix('ads')->group(function () {
        Route::post('/impression', [App\Http\Controllers\Admin\AdvertisementController::class, 'recordImpression']);
        Route::post('/click', [App\Http\Controllers\Admin\AdvertisementController::class, 'recordClick']);
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
        });
    });
});