<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
         'auth.guru' => \App\Http\Middleware\GuruAuth::class,
         'auth.madrasah' => \App\Http\Middleware\MadrasahAuth::class,
         'force.password' => \App\Http\Middleware\ForcePasswordChange::class,
         'super.admin' => \App\Http\Middleware\SuperAdminOnly::class,
     ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
