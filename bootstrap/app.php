<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
         $middleware->alias([
            'admin' =>\App\Http\Middleware\RedirectIfNotAdmin::class,
            'vendor' =>\App\Http\Middleware\RedirectIfNotVendor::class,
        ]);
         
          $middleware->validateCsrfTokens(except: [
          'return_upload','webhook','addUpdateCustomerPayment'
      
         ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
         Integration::handles($exceptions);
    })->create();
