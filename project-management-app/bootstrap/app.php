<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        // Register custom security middleware aliases
        $middleware->alias([
            'rate.limit.auth' => \App\Http\Middleware\RateLimitAuth::class,
            'rate.limit.api' => \App\Http\Middleware\RateLimitApi::class,
            'throttle.failed.logins' => \App\Http\Middleware\ThrottleFailedLogins::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'validate.input' => \App\Http\Middleware\ValidateInput::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Apply global middleware to all routes
        $middleware->append([
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
