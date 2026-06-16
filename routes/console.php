<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rappels de cotisation expirante : tous les lundis à 8h
Schedule::command('membres:expiry-reminders')->weeklyOn(1, '08:00');

// Nettoyage des tokens Sanctum expirés : chaque nuit à 3h
Schedule::command('sanctum:prune-expired --hours=720')->dailyAt('03:00');
