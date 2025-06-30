<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function ($router) {
            $rate_limit = config('env.APP_ENV') === 'local' ? 20000 : 60;
            RateLimiter::for('api-user', function (Request $request) use ($rate_limit) {
                return Limit::perMinute($rate_limit)->by($request->user()?->id ?: $request->ip());
            });
            RateLimiter::for('api-admin', function (Request $request) use ($rate_limit) {
                return Limit::perMinute($rate_limit)->by($request->user()?->id ?: $request->ip());
            });

            Route::prefix('api/user')
                ->middleware('api-user')
                ->group(base_path('routes/api/user.php'));

            Route::prefix('api/admin')
                ->middleware('api-admin')
                ->group(base_path('routes/api/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'language' => \App\Http\Middleware\Language::class,
            'adminHasPermission' => \App\Http\Middleware\AdminHasPermission::class,
            'setSanctumGuard' => \App\Http\Middleware\SetSanctumGuard::class,
            'optionalAuth' => \App\Http\Middleware\OptionalAuthenticate::class,
            'checkAssetRevision' => \App\Http\Middleware\CheckAssetRevision::class,
        ]);

        $middleware->group('api-user', [
            'setSanctumGuard:user',
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api-user',
            'language:user',
            'checkAssetRevision:user',
        ]);

        $middleware->group('api-admin', [
            'setSanctumGuard:admin',
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api-admin',
            'language:admin',
            'checkAssetRevision:admin',
        ]);

        $middleware->redirectGuestsTo('/login');
        $middleware->validateCsrfTokens(except: [
            '/api/*',
            '/cb/*',
        ]);

        $middleware->web(append: [
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
