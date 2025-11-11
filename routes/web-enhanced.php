<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Auth\ShopOwnerLoginController;

// City-specific routes for better SEO
Route::group(['prefix' => 'city/{city:slug}', 'middleware' => ['city.context']], function () {
    // City homepage - shows all shops and categories in this city
    Route::get('/', [CityController::class, 'show'])->name('city.show');
    
    // City-specific shop listings
    Route::get('/shops', [CityController::class, 'shops'])->name('city.shops.index');
    Route::get('/shops/featured', [CityController::class, 'featuredShops'])->name('city.shops.featured');
    Route::get('/shops/category/{category:slug}', [CityController::class, 'shopsByCategory'])->name('city.shops.category');
    
    // City-specific category listings
    Route::get('/categories', [CityController::class, 'categories'])->name('city.categories.index');
    Route::get('/category/{category:slug}', [CityController::class, 'categoryShops'])->name('city.category.shops');
    
    // City-specific search
    Route::get('/search', [CityController::class, 'search'])->name('city.search');
    
    // City-specific shop details (still accessible at city level for better SEO)
    Route::get('/shop/{shop:slug}', [ShopController::class, 'show'])->name('city.shop.show');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login']);
    });
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
    });
});

// Shop Owner Authentication Routes
Route::prefix('shop-owner')->name('shop-owner.')->group(function () {
    Route::middleware('guest:shop_owner')->group(function () {
        Route::get('/login', [ShopOwnerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [ShopOwnerLoginController::class, 'login']);
    });
    Route::middleware('auth:shop_owner')->group(function () {
        Route::post('/logout', [ShopOwnerLoginController::class, 'logout'])->name('logout');
    });
});

// Regular User Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
    
    // Rating routes
    Route::prefix('ratings')->name('ratings.')->group(function () {
        Route::post('/', [RatingController::class, 'store'])->name('store');
        Route::put('/{rating}', [RatingController::class, 'update'])->name('update');
        Route::delete('/{rating}', [RatingController::class, 'destroy'])->name('destroy');
        Route::post('/{rating}/helpful', [RatingController::class, 'toggleHelpful'])->name('toggle-helpful');
        Route::get('/shop/{shop}/user', [RatingController::class, 'getUserRating'])->name('user-rating');
    });
});

// Shop Owner routes
Route::middleware(['auth'])->prefix('shop-owner')->name('shop-owner.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/create-shop', [DashboardController::class, 'createShop'])->name('create-shop');
    Route::post('/shops', [DashboardController::class, 'storeShop'])->name('shops.store');
    Route::get('/shops/{shop}/edit', [DashboardController::class, 'editShop'])->name('shops.edit');
    Route::put('/shops/{shop}', [DashboardController::class, 'updateShop'])->name('shops.update');
});

// Landing page with city context middleware
Route::get('/', [LandingController::class, 'index'])
    ->name('landing')
    ->middleware(['city.context', 'cache.headers:public;max_age=3600;etag']);

// City selection routes
Route::get('/select-city', [LandingController::class, 'selectCity'])->name('select.city');
Route::post('/set-city', [LandingController::class, 'setCity'])->name('set.city');
Route::get('/change-city', [LandingController::class, 'changeCity'])->name('change.city');

// Test route to clear session (for development)
Route::get('/clear-session', function() {
    session()->flush();
    return redirect()->route('landing')->with('success', 'Session cleared');
})->name('clear.session');

Route::get('/api/city/{slug}', [LandingController::class, 'getCityData'])->name('city.data');

// Global search routes (searches across all cities if no city is selected)
Route::middleware(['city.context'])->group(function () {
    Route::get('/search', [LandingController::class, 'search'])->name('search');
    Route::get('/search/suggestions', [LandingController::class, 'searchSuggestions'])->name('search.suggestions');
});

// Category routes (global or city-specific based on context)
Route::middleware(['city.context'])->group(function () {
    Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.shops');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
});

// Cities routes
Route::get('/cities', [CityController::class, 'index'])->name('cities.index');

// Legacy city route (redirects to new structure)
Route::get('/city/{slug}',[LandingController::class, 'cityLanding'])->name('city.shops');

// Shop detail page (legacy route, redirects to city-specific route if possible)
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');

// Shop ratings (public route)
Route::get('/shop/{shop}/ratings', [RatingController::class, 'index'])->name('shop.ratings');

// Enhanced SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');