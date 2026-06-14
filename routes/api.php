<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DirectoryController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MentoringController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\TrainingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — préfixe automatique /api/v1 (configuré dans bootstrap/app.php)
|--------------------------------------------------------------------------
*/

// ── Authentification (public) ─────────────────────────────────────────────
Route::post('auth/login',  [AuthController::class, 'login'])->name('api.auth.login');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('auth/me',      [AuthController::class, 'me'])->name('api.auth.me');

    // ── Routes membres actifs uniquement ─────────────────────────────────
    Route::middleware('membre.api')->group(function () {

        // Tableau de bord
        Route::get('dashboard', DashboardController::class)->name('api.dashboard');

        // Profil
        Route::get('profile',           [ProfileController::class, 'show'])->name('api.profile.show');
        Route::put('profile',           [ProfileController::class, 'update'])->name('api.profile.update');
        Route::post('profile/avatar',   [ProfileController::class, 'uploadAvatar'])->name('api.profile.avatar');

        // Événements
        Route::get('events',                                    [EventController::class, 'index'])->name('api.events.index');
        Route::get('events/{event}',                            [EventController::class, 'show'])->name('api.events.show');
        Route::post('events/{event}/register',                  [EventController::class, 'register'])->name('api.events.register');
        Route::delete('events/{event}/register',                [EventController::class, 'unregister'])->name('api.events.unregister');
        Route::get('events/registrations/{registration}/qr',    [EventController::class, 'qrCode'])->name('api.events.qr');

        // Formations
        Route::get('trainings',                                         [TrainingController::class, 'index'])->name('api.trainings.index');
        Route::get('trainings/my-enrollments',                          [TrainingController::class, 'myEnrollments'])->name('api.trainings.my-enrollments');
        Route::get('trainings/{training}',                              [TrainingController::class, 'show'])->name('api.trainings.show');
        Route::get('trainings/{training}/sessions',                     [TrainingController::class, 'sessions'])->name('api.trainings.sessions');
        Route::post('trainings/sessions/{session}/enroll',              [TrainingController::class, 'enroll'])->name('api.trainings.enroll');
        Route::delete('trainings/sessions/{session}/enroll',            [TrainingController::class, 'unenroll'])->name('api.trainings.unenroll');
        Route::post('trainings/enrollments/{enrollment}/rate',          [TrainingController::class, 'rate'])->name('api.trainings.rate');

        // Paiements
        Route::get('payments',                                  [PaymentController::class, 'index'])->name('api.payments.index');
        Route::get('payments/plans',                            [PaymentController::class, 'plans'])->name('api.payments.plans');
        Route::post('payments',                                 [PaymentController::class, 'store'])->name('api.payments.store');
        Route::post('payments/{payment}/screenshot',            [PaymentController::class, 'uploadScreenshot'])->name('api.payments.screenshot');

        // Opportunités
        Route::get('opportunities',                             [OpportunityController::class, 'index'])->name('api.opportunities.index');
        Route::get('opportunities/my-applications',             [OpportunityController::class, 'myApplications'])->name('api.opportunities.my-applications');
        Route::get('opportunities/{opportunity}',               [OpportunityController::class, 'show'])->name('api.opportunities.show');
        Route::post('opportunities/{opportunity}/apply',        [OpportunityController::class, 'apply'])->name('api.opportunities.apply');

        // Annuaire
        Route::get('directory',                 [DirectoryController::class, 'index'])->name('api.directory.index');
        Route::get('directory/{user}',          [DirectoryController::class, 'show'])->name('api.directory.show');

        // Contacts
        Route::get('contacts',                                  [ContactController::class, 'index'])->name('api.contacts.index');
        Route::get('contacts/requests',                         [ContactController::class, 'requests'])->name('api.contacts.requests');
        Route::post('contacts/requests/{user}',                 [ContactController::class, 'sendRequest'])->name('api.contacts.send');
        Route::post('contacts/requests/{contactRequest}/accept',[ContactController::class, 'accept'])->name('api.contacts.accept');
        Route::post('contacts/requests/{contactRequest}/decline',[ContactController::class, 'decline'])->name('api.contacts.decline');

        // Mentorat
        Route::get('mentoring/sessions',                        [MentoringController::class, 'index'])->name('api.mentoring.index');
        Route::post('mentoring/sessions',                       [MentoringController::class, 'store'])->name('api.mentoring.store');
        Route::post('mentoring/sessions/{session}/confirm',     [MentoringController::class, 'confirm'])->name('api.mentoring.confirm');
        Route::get('mentoring/mentors',                         [MentoringController::class, 'mentors'])->name('api.mentoring.mentors');

        // Progression
        Route::get('progress', ProgressController::class)->name('api.progress');
    });
});