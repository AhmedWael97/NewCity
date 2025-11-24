<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShopOwner\DashboardController as ShopOwnerDashboardController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Auth\ShopOwnerLoginController;

//Include debug routes
require __DIR__.'/debug.php';



Route::get('/link-storage', function() {
    \Artisan::call('storage:link');
    return "Storage Linked";
});


Route::get('/new-migrate', function() {
    \Artisan::call('migrate', ['--force' => true]);
    return "Migration Completed";
});


Route::get('/clear-cache', function() {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    return "Cache Cleared";
});


// City Selection Page - Must be accessible without city requirement
Route::get('/select-city', function () {
    // Load cities from database
    $cities = \App\Models\City::select(['id', 'name', 'slug', 'state', 'country'])
        ->where('is_active', true)
        ->withCount(['shops' => function ($query) {
            $query->where('is_active', true)->where('is_verified', true);
        }])
        ->get()
        ->sortByDesc('shops_count')
        ->sortBy('name');
    
    return view('select-city', [
        'cities' => $cities,
        'seoData' => [
            'title' => 'اختر مدينتك',
            'description' => 'اختر مدينتك للحصول على أفضل تجربة تسوق محلية'
        ]
    ]);
})->name('select.city.page')->middleware(['auto.load.city']);

// Home route - redirect to city selection if no city selected
Route::get('/', function () {
    if (session()->has('selected_city')) {
        $citySlug = session('selected_city');
        return redirect("/city/{$citySlug}");
    }
    return redirect('/select-city');
})->name('home');

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

// Public Service View (no auth required)
Route::get('/user/services/{service}', [App\Http\Controllers\User\UserServiceController::class, 'show'])->name('user.services.show');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
    
    // User Services routes (authenticated)
    Route::prefix('user/services')->name('user.services.')->group(function () {
        Route::get('/', [App\Http\Controllers\User\UserServiceController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\User\UserServiceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\User\UserServiceController::class, 'store'])->name('store');
        Route::get('/{service}/edit', [App\Http\Controllers\User\UserServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [App\Http\Controllers\User\UserServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [App\Http\Controllers\User\UserServiceController::class, 'destroy'])->name('destroy');
        Route::patch('/{service}/toggle-status', [App\Http\Controllers\User\UserServiceController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{service}/analytics', [App\Http\Controllers\User\UserServiceController::class, 'analytics'])->name('analytics');
        Route::post('/{service}/record-contact', [App\Http\Controllers\User\UserServiceController::class, 'recordContact'])->name('record-contact');
    });
    
    // Service Review routes
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/{service}/reviews', [App\Http\Controllers\ServiceReviewController::class, 'index'])->name('reviews.index');
        Route::get('/{service}/reviews/create', [App\Http\Controllers\ServiceReviewController::class, 'create'])->name('reviews.create');
        Route::post('/{service}/reviews', [App\Http\Controllers\ServiceReviewController::class, 'store'])->name('reviews.store');
        Route::get('/reviews/{review}/edit', [App\Http\Controllers\ServiceReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/reviews/{review}', [App\Http\Controllers\ServiceReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [App\Http\Controllers\ServiceReviewController::class, 'destroy'])->name('reviews.destroy');
    });
    
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
    // Routes accessible to all authenticated users (for upgrade flow)
    Route::get('/create-shop', [ShopOwnerDashboardController::class, 'createShop'])->name('create-shop');
    Route::post('/upgrade', [ShopOwnerDashboardController::class, 'upgradeToShopOwner'])->name('upgrade');
    
    // Routes only for shop owners
    Route::middleware(['shop-owner'])->group(function () {
        Route::get('/dashboard', [ShopOwnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/subscriptions', [ShopOwnerDashboardController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/payment', [ShopOwnerDashboardController::class, 'showPayment'])->name('payment');
        Route::post('/process-payment', [ShopOwnerDashboardController::class, 'processPayment'])->name('process-payment');
        Route::post('/shops', [ShopOwnerDashboardController::class, 'storeShop'])->name('shops.store');
        Route::get('/shops/{shop}/edit', [ShopOwnerDashboardController::class, 'editShop'])->name('shops.edit');
        Route::put('/shops/{shop}', [ShopOwnerDashboardController::class, 'updateShop'])->name('shops.update');
    });
});

// Welcome/Landing page - removed as we now use home route above that redirects

// City-specific landing page
Route::get('/city-landing/{city:slug}', [LandingController::class, 'cityLanding'])
    ->name('city.landing')
    ->middleware(['city.context']);

// City services page
Route::get('/city/{city:slug}/services', [LandingController::class, 'cityServices'])
    ->name('city.services')
    ->middleware(['city.context']);

// City selection routes (selectCity removed - now using direct view route above)
Route::post('/set-city', [LandingController::class, 'setCity'])->name('set.city');
Route::post('/skip-city-selection', [LandingController::class, 'skipCitySelection'])->name('skip.city');
Route::get('/change-city', [LandingController::class, 'changeCity'])->name('change.city');

// Test routes (for development)
Route::get('/clear-session', function() {
    session()->flush();
    return redirect('/select-city')->with('success', 'Session cleared');
})->name('clear.session');

Route::get('/test-city-session', function() {
    return view('test-city-session');
})->name('test.city.session');

Route::get('/clear-city-session', function() {
    session()->forget(['selected_city', 'selected_city_name', 'selected_city_id', 'city_slug']);
    return redirect()->route('test.city.session')->with('success', 'City session cleared');
})->name('clear.city.session');

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

// Legacy city route (uses same landing as city-landing)
Route::get('/city/{city:slug}', [LandingController::class, 'cityLanding'])->name('city.shops');

// Shop detail page (legacy route, redirects to city-specific route if possible)
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');

// Shop ratings (public route)
Route::get('/shop/{shop}/ratings', [RatingController::class, 'index'])->name('shop.ratings');

// Enhanced SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');