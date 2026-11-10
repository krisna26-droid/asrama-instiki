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
    ->withCommands([
        \App\Console\Commands\CreateStaffAccount::class,
        \App\Console\Commands\CompleteEndedPlacements::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', [\App\Http\Middleware\EnsureActiveAccount::class]);
        // Daftarkan alias middleware role kita
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'password.changed' => \App\Http\Middleware\EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
