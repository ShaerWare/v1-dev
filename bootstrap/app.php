<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        // Добавляем стандартные middleware
        /* $middleware->api([
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\AuthenticateApi::class, // Ваш новый middleware
        ]);
*/
        // Если нужно добавить другие middleware, делайте это здесь
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //artisan cache:clear

    })->create();
