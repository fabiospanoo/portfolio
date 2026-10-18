<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureSchemaMigrated;
use App\Http\Middleware\ForceCanonicalDomain;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureAdmin::class,
        ]);

        $middleware->web(
            prepend: [
                EnsureSchemaMigrated::class,
                ForceCanonicalDomain::class,
            ],
            append: [
                SecurityHeaders::class,
            ],
        );

        $trustedProxies = env('TRUSTED_PROXIES', '*');
        if ($trustedProxies !== '' && $trustedProxies !== null) {
            $middleware->trustProxies(at: $trustedProxies === '*' ? '*' : explode(',', (string) $trustedProxies));
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
