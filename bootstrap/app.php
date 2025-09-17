<?php

use App\Http\Middleware\AuthSatum;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->alias([
        'auth.satum' => AuthSatum::class,
    ]);
        $middleware->api(prepend: [  // Thêm vào api group để tự động auth token
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,  // Nếu dùng SPA, bỏ nếu pure API
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'auth:sanctum',  // Thêm dòng này để enable auth cho tất cả api routes
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();