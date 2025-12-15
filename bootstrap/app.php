<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
            // Route::middleware('web')
            //     ->group(base_path('routes/shop-owner.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Enable CORS for API
        $middleware->api(append: [
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\TrackApiRequest::class,
        ]);
        
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'permission.guard' => \App\Http\Middleware\SetPermissionGuard::class,
            'city.access' => \App\Http\Middleware\CheckCityAccess::class,
            'city.selection' => \App\Http\Middleware\CitySelection::class,
            'city.context' => \App\Http\Middleware\CityContextMiddleware::class,
            'check.city' => \App\Http\Middleware\CheckCitySelection::class,
            'auto.load.city' => \App\Http\Middleware\AutoLoadCityFromStorage::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'shop_owner' => \App\Http\Middleware\ShopOwnerMiddleware::class,
            'shop-owner' => \App\Http\Middleware\ShopOwnerMiddleware::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'app.status' => \App\Http\Middleware\CheckAppStatus::class,
            'app.version' => \App\Http\Middleware\CheckAppVersion::class,
            'secure.api' => \App\Http\Middleware\SecureApiAccess::class,
            'user.active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
        
        // Apply city selection middleware to web routes
        $middleware->web(append: [
            \App\Http\Middleware\CitySelection::class,
            \App\Http\Middleware\TrackWebsiteVisit::class,
            \App\Http\Middleware\OptimizeResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
