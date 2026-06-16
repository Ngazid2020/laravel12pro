<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // Login API : 5 tentatives par minute par IP, 3 par email
        // Protège contre les attaques par force brute sur les mots de passe
        RateLimiter::for('api-login', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip())->response(fn () => response()->json([
                    'message' => 'Trop de tentatives. Réessayez dans une minute.',
                ], 429)),
                Limit::perMinute(3)->by('email:'.$request->input('email', ''))->response(fn () => response()->json([
                    'message' => 'Trop de tentatives sur ce compte. Réessayez dans une minute.',
                ], 429)),
            ];
        });
    }
}
