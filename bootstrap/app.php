<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\roleMiddleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
            $middleware->alias([    
            'role'=>roleMiddleware::class
        ]);
        //! Trust the proxies that are set in the environment variable  (deployment)
        $middleware->trustProxies(
        at: '*',
        headers: Request::HEADER_X_FORWARDED_FOR |
                 Request::HEADER_X_FORWARDED_HOST |
                 Request::HEADER_X_FORWARDED_PORT |
                 Request::HEADER_X_FORWARDED_PROTO |
                 Request::HEADER_X_FORWARDED_AWS_ELB
    );


    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
