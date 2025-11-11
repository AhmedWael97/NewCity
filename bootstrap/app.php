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
        
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
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
        ]);
        
        // Apply city selection middleware to web routes
        $middleware->web(append: [
            \App\Http\Middleware\CitySelection::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
