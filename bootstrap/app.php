<?php

use App\Http\Middleware\AllowRegistration;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\CheckStatusApi;
use App\Http\Middleware\Demo;
use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\ProAjaxMiddleware;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function (): void {
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/api.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append([
            ProAjaxMiddleware::class,
        ]);

        $middleware->web(append: [
            LanguageMiddleware::class,
        ]);

        $middleware->statefulApi();

        $middleware->alias([
            'admin' => RedirectIfNotAdmin::class,
            'admin.guest' => RedirectIfAdmin::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
            'checkStatus' => CheckStatus::class,
            'checkStatusApi' => CheckStatusApi::class,
            'demo' => Demo::class,
            'guest' => RedirectIfAuthenticated::class,
            'regStatus' => AllowRegistration::class,
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
