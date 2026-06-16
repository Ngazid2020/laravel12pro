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
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'membre'      => \App\Http\Middleware\EnsureActiveMember::class,
            'membre.api'  => \App\Http\Middleware\EnsureActiveMemberApi::class,
        ]);

        // En-têtes de sécurité sur toutes les requêtes
        $middleware->append(\App\Http\Middleware\AddSecurityHeaders::class);
        $middleware->redirectGuestsTo(fn () => route('member.login'));
    })
    ->withExceptions(function (Exceptions $e): void {
        //
    })->create();
