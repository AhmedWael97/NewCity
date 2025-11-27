<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminShopController;
use App\Http\Controllers\Admin\AdminCityController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminRatingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminLogsController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\CityStyleController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'admin'])->group(function () {
    
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/system-health', [AdminDashboardController::class, 'systemHealth'])->name('system.health');
    
    // User Management
    Route::resource('users', AdminUserController::class);
    Route::post('users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
    
    // Shop Management
    Route::resource('shops', AdminShopController::class);
    Route::post('shops/bulk-action', [AdminShopController::class, 'bulkAction'])->name('shops.bulk-action');
    Route::post('shops/{shop}/toggle-status', [AdminShopController::class, 'toggleStatus'])->name('shops.toggle-status');
    Route::post('shops/{shop}/verify', [AdminShopController::class, 'verify'])->name('shops.verify');
    Route::post('shops/{shop}/reject', [AdminShopController::class, 'reject'])->name('shops.reject');
    Route::post('shops/{shop}/feature', [AdminShopController::class, 'toggleFeatured'])->name('shops.feature');
    Route::get('shops/pending/review', [AdminShopController::class, 'pendingReview'])->name('shops.pending');
    
    // Shops Map View
    Route::get('shops-map', [AdminShopController::class, 'mapView'])->name('shops.map');
    
    // Import shops from Google Places
    Route::post('shops/import-from-google', [AdminShopController::class, 'importFromGoogle'])->name('shops.import-google');
    
    // Featured Shops Management
    Route::get('shops/{shop}/featured/edit', [AdminShopController::class, 'editFeatured'])->name('shops.featured.edit');
    Route::put('shops/{shop}/featured', [AdminShopController::class, 'updateFeatured'])->name('shops.featured.update');
    
    // City Management
    Route::resource('cities', AdminCityController::class);
    Route::post('cities/{city}/toggle-active', [AdminCityController::class, 'toggleActive'])->name('cities.toggle-active');
    
    // Category Management
    Route::resource('categories', AdminCategoryController::class);
    Route::post('categories/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive'])->name('categories.toggle-active');
    Route::get('categories/hierarchy', [AdminCategoryController::class, 'hierarchy'])->name('categories.hierarchy');
    
    // Rating Management
    Route::resource('ratings', AdminRatingController::class)->except(['create', 'store']);
    Route::post('ratings/{rating}/verify', [AdminRatingController::class, 'verify'])->name('ratings.verify');
    Route::delete('ratings/bulk-delete', [AdminRatingController::class, 'bulkDelete'])->name('ratings.bulk-delete');
    
    // Review Management
    Route::resource('reviews', App\Http\Controllers\Admin\AdminReviewController::class)->only(['index', 'show', 'destroy']);
    Route::patch('reviews/{review}/verify', [App\Http\Controllers\Admin\AdminReviewController::class, 'verify'])->name('reviews.verify');
    Route::patch('reviews/{review}/unverify', [App\Http\Controllers\Admin\AdminReviewController::class, 'unverify'])->name('reviews.unverify');
    
    // Favorites Management
    Route::get('favorites', [App\Http\Controllers\Admin\AdminFavoriteController::class, 'index'])->name('favorites.index');
    Route::get('favorites/statistics', [App\Http\Controllers\Admin\AdminFavoriteController::class, 'statistics'])->name('favorites.statistics');
    Route::delete('favorites', [App\Http\Controllers\Admin\AdminFavoriteController::class, 'destroy'])->name('favorites.destroy');
    
    // Subscription Plans Management
    Route::resource('subscription-plans', SubscriptionController::class)->names([
        'index' => 'subscription-plans.index',
        'create' => 'subscription-plans.create',
        'store' => 'subscription-plans.store',
        'show' => 'subscription-plans.show',
        'edit' => 'subscription-plans.edit',
        'update' => 'subscription-plans.update',
        'destroy' => 'subscription-plans.destroy',
    ]);
    Route::post('subscription-plans/bulk-action', [SubscriptionController::class, 'bulkAction'])->name('subscription-plans.bulk-action');
    Route::post('subscription-plans/{subscriptionPlan}/toggle-status', [SubscriptionController::class, 'toggleStatus'])->name('subscription-plans.toggle-status');

    // Subscription Management
    Route::prefix('subscriptions')->as('subscriptions.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::get('/create', [SubscriptionController::class, 'create'])->name('create');
        Route::post('/', [SubscriptionController::class, 'store'])->name('store');
        Route::get('/{subscription}', [SubscriptionController::class, 'show'])->name('show');
        Route::get('/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('edit');
        Route::put('/{subscription}', [SubscriptionController::class, 'update'])->name('update');
        Route::delete('/{subscription}', [SubscriptionController::class, 'destroy'])->name('destroy');
        
        // Active subscriptions management
        Route::get('/manage/subscriptions', [SubscriptionController::class, 'subscriptions'])->name('subscriptions');
        Route::put('/manage/{subscription}/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('cancel');
        Route::put('/manage/{subscription}/renew', [SubscriptionController::class, 'renewSubscription'])->name('renew');
        
        // Analytics
        Route::get('/analytics/overview', [SubscriptionController::class, 'analytics'])->name('analytics');
    });

    // Payment Management
    Route::prefix('payments')->as('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminPaymentController::class, 'index'])->name('index');
        Route::get('/pending', [App\Http\Controllers\Admin\AdminPaymentController::class, 'pending'])->name('pending');
        Route::get('/{subscription}', [App\Http\Controllers\Admin\AdminPaymentController::class, 'show'])->name('show');
        Route::post('/{subscription}/verify', [App\Http\Controllers\Admin\AdminPaymentController::class, 'verifyPayment'])->name('verify');
        Route::post('/{subscription}/reject', [App\Http\Controllers\Admin\AdminPaymentController::class, 'rejectPayment'])->name('reject');
        Route::post('/{subscription}/refund', [App\Http\Controllers\Admin\AdminPaymentController::class, 'refund'])->name('refund');
    });

    // Shop Approval Management
    Route::prefix('shop-approvals')->as('shop-approvals.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminShopApprovalController::class, 'index'])->name('index');
        Route::get('/{shop}', [App\Http\Controllers\Admin\AdminShopApprovalController::class, 'show'])->name('show');
        Route::post('/{shop}/approve', [App\Http\Controllers\Admin\AdminShopApprovalController::class, 'approve'])->name('approve');
        Route::post('/{shop}/reject', [App\Http\Controllers\Admin\AdminShopApprovalController::class, 'reject'])->name('reject');
        Route::post('/{shop}/request-changes', [App\Http\Controllers\Admin\AdminShopApprovalController::class, 'requestChanges'])->name('request-changes');
    });

    // Support Tickets
    Route::prefix('tickets')->as('tickets.')->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('index');
        Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('show');
        Route::put('/{ticket}', [SupportTicketController::class, 'update'])->name('update');
        Route::post('/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('reply');
        Route::put('/{ticket}/assign', [SupportTicketController::class, 'assign'])->name('assign');
        Route::post('/bulk-action', [SupportTicketController::class, 'bulkAction'])->name('bulk.action');
        Route::get('/analytics/overview', [SupportTicketController::class, 'analytics'])->name('analytics');
        Route::get('/export/csv', [SupportTicketController::class, 'export'])->name('export');
    });

    // Analytics Dashboard
    Route::prefix('analytics')->as('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/shops', [AnalyticsController::class, 'shopPerformance'])->name('shops');
        Route::get('/cities', [AnalyticsController::class, 'cityAnalytics'])->name('cities');
        Route::get('/users', [AnalyticsController::class, 'userBehavior'])->name('users');
        Route::post('/reports', [AnalyticsController::class, 'generateReport'])->name('reports.generate');
    });

    // Reports Management
    Route::prefix('reports')->as('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
        Route::post('/generate', [AdminReportController::class, 'generate'])->name('generate');
    });

    // Settings Management
    Route::prefix('settings')->as('settings.')->group(function () {
        Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
        Route::put('/update', [AdminSettingsController::class, 'update'])->name('update');
        Route::post('/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('clear-cache');
    });

    // Logs Management
    Route::prefix('logs')->as('logs.')->group(function () {
        Route::get('/', [AdminLogsController::class, 'index'])->name('index');
        Route::get('/download', [AdminLogsController::class, 'download'])->name('download');
        Route::post('/clear', [AdminLogsController::class, 'clear'])->name('clear');
        Route::delete('/delete', [AdminLogsController::class, 'delete'])->name('delete');
    });

    // Advertisement Management
    Route::prefix('advertisements')->as('advertisements.')->group(function () {
        Route::get('/', [AdvertisementController::class, 'index'])->name('index');
        Route::get('/create', [AdvertisementController::class, 'create'])->name('create');
        Route::post('/', [AdvertisementController::class, 'store'])->name('store');
        Route::get('/{advertisement}', [AdvertisementController::class, 'show'])->name('show');
        Route::get('/{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('edit');
        Route::put('/{advertisement}', [AdvertisementController::class, 'update'])->name('update');
        Route::delete('/{advertisement}', [AdvertisementController::class, 'destroy'])->name('destroy');
        
        // Ad status management
        Route::patch('/{advertisement}/approve', [AdvertisementController::class, 'approve'])->name('approve');
        Route::patch('/{advertisement}/reject', [AdvertisementController::class, 'reject'])->name('reject');
        Route::patch('/{advertisement}/pause', [AdvertisementController::class, 'pause'])->name('pause');
        Route::patch('/{advertisement}/activate', [AdvertisementController::class, 'activate'])->name('activate');
        
        // Analytics
        Route::get('/analytics/overview', [AdvertisementController::class, 'analytics'])->name('analytics');
    });

    // City Styling Management
    Route::prefix('city-styles')->as('city-styles.')->group(function () {
        Route::get('/', [CityStyleController::class, 'index'])->name('index');
        Route::get('/{city}', [CityStyleController::class, 'show'])->name('show');
        Route::get('/{city}/edit', [CityStyleController::class, 'edit'])->name('edit');
        Route::put('/{city}', [CityStyleController::class, 'update'])->name('update');
        Route::get('/{city}/css', [CityStyleController::class, 'generateCss'])->name('css');
        Route::post('/{city}/preview', [CityStyleController::class, 'previewTheme'])->name('preview');
        Route::patch('/{city}/reset', [CityStyleController::class, 'resetTheme'])->name('reset');
        
        // Landing Page Theme Configuration
        Route::get('/{city}/landing-page', [CityStyleController::class, 'editLandingPage'])->name('landing-page');
        Route::put('/{city}/landing-page', [CityStyleController::class, 'updateLandingPage'])->name('landing-page.update');
    });

    // City Banners Management
    Route::resource('city-banners', App\Http\Controllers\Admin\AdminCityBannerController::class);
    Route::patch('city-banners/{cityBanner}/toggle-status', [App\Http\Controllers\Admin\AdminCityBannerController::class, 'toggleStatus'])->name('city-banners.toggle-status');

    // Mobile App Settings Management
    Route::prefix('app-settings')->as('app-settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'index'])->name('index');
        Route::put('/update', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'update'])->name('update');
        
        // Push Notifications Management
        Route::get('/notifications', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'notifications'])->name('notifications');
        Route::get('/notifications/create', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'createNotification'])->name('notifications.create');
        Route::post('/notifications', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'storeNotification'])->name('notifications.store');
        Route::post('/notifications/{notification}/send', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'sendNotification'])->name('notifications.send');
        Route::delete('/notifications/{notification}', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'deleteNotification'])->name('notifications.delete');
        
        // Device Tokens Management
        Route::get('/devices', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'devices'])->name('devices');
        
        // Test Notification
        Route::post('/test-notification', [App\Http\Controllers\Admin\AdminAppSettingsController::class, 'testNotification'])->name('test-notification');
    });
    
});