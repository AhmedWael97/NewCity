<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopOwner\ShopOwnerDashboardController;
use App\Http\Controllers\ShopOwner\ShopOwnerShopController;
use App\Http\Controllers\ShopOwner\ShopOwnerRatingController;
use App\Http\Controllers\ShopOwner\ShopOwnerAnalyticsController;
use App\Http\Controllers\ShopOwner\ShopOwnerProfileController;

/*
|--------------------------------------------------------------------------
| Shop Owner Routes
|--------------------------------------------------------------------------
|
| Here is where you can register shop owner routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "shop-owner" middleware group.
|
*/

Route::prefix('shop-owner')->name('shop-owner.')->middleware(['auth:web', 'shop-owner'])->group(function () {
    
    // Dashboard
    Route::get('/', [ShopOwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [ShopOwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [ShopOwnerDashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/chart', [ShopOwnerDashboardController::class, 'getMonthlyChart'])->name('dashboard.chart');
    Route::get('/dashboard/activities', [ShopOwnerDashboardController::class, 'getRecentActivities'])->name('dashboard.activities');
    Route::get('/dashboard/analytics', [ShopOwnerDashboardController::class, 'getShopAnalytics'])->name('dashboard.analytics');
    Route::get('/dashboard/notifications', [ShopOwnerDashboardController::class, 'getNotifications'])->name('dashboard.notifications');
    
    // Shop Management
    Route::resource('shops', ShopOwnerShopController::class);
    Route::patch('shops/{shop}/toggle-status', [ShopOwnerShopController::class, 'toggleStatus'])->name('shops.toggle-status');
    Route::post('shops/{shop}/upload-gallery', [ShopOwnerShopController::class, 'uploadGallery'])->name('shops.upload-gallery');
    Route::delete('shops/{shop}/delete-gallery-image', [ShopOwnerShopController::class, 'deleteGalleryImage'])->name('shops.delete-gallery-image');
    Route::get('shops/{shop}/analytics', [ShopOwnerShopController::class, 'getAnalytics'])->name('shops.analytics');
    
    // Ratings Management
    Route::resource('ratings', ShopOwnerRatingController::class)->only(['index', 'show', 'update']);
    Route::post('ratings/{rating}/reply', [ShopOwnerRatingController::class, 'reply'])->name('ratings.reply');
    Route::get('ratings/stats', [ShopOwnerRatingController::class, 'getStats'])->name('ratings.stats');
    Route::get('ratings/export', [ShopOwnerRatingController::class, 'export'])->name('ratings.export');
    
    // Analytics & Reports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [ShopOwnerAnalyticsController::class, 'index'])->name('index');
        Route::get('/shops', [ShopOwnerAnalyticsController::class, 'shops'])->name('shops');
        Route::get('/ratings', [ShopOwnerAnalyticsController::class, 'ratings'])->name('ratings');
        Route::get('/performance', [ShopOwnerAnalyticsController::class, 'performance'])->name('performance');
        Route::get('/export/{type}', [ShopOwnerAnalyticsController::class, 'export'])->name('export');
    });
    
    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ShopOwnerProfileController::class, 'edit'])->name('edit');
        Route::patch('/update', [ShopOwnerProfileController::class, 'update'])->name('update');
        Route::patch('/update-password', [ShopOwnerProfileController::class, 'updatePassword'])->name('update-password');
        Route::delete('/delete-account', [ShopOwnerProfileController::class, 'deleteAccount'])->name('delete-account');
    });
    
});