<?php

use App\Http\Controllers\EventCheckInController;
use App\Http\Controllers\PaymentScreenshotController;
use App\Http\Controllers\ReceiptController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\MemberLogin;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Member\Contacts;
use App\Livewire\Member\Dashboard;
use App\Livewire\Member\Directory;
use App\Livewire\Member\Events;
use App\Livewire\Member\Mentoring;
use App\Livewire\Member\MyNetwork;
use App\Livewire\Member\Opportunities;
use App\Livewire\Member\Payments;
use App\Livewire\Member\Profile;
use App\Livewire\Member\Progress;
use App\Livewire\Member\Recommendations;
use App\Livewire\Member\Trainings;
use App\Livewire\Public\Home;
use App\Livewire\Public\Rgpd;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------
// VITRINE PUBLIQUE
// -----------------------------------------------
Route::get('/', Home::class)->name('home');
Route::get('/politique-de-confidentialite', Rgpd::class)->name('rgpd');

// Redirige les utilisateurs connectés depuis la racine
Route::get('/portail', function () {
    if (Auth::user()->hasRole(['super_admin', 'admin'])) {
        return redirect('/admin');
    }
    return redirect()->route('membre.dashboard');
})->middleware('auth')->name('portail');

// Check-in événement : URL signée, aucune auth requise (staff scanne avec téléphone)
Route::get('/evenements/check-in/{registration}', EventCheckInController::class)
    ->name('event.checkin')
    ->middleware('signed');

// -----------------------------------------------
// AUTHENTIFICATION
// -----------------------------------------------
Route::get('/login', MemberLogin::class)->name('member.login')->middleware('guest');
Route::get('/mot-de-passe-oublie', ForgotPassword::class)->name('password.request')->middleware('guest');
Route::get('/reinitialiser-mot-de-passe/{token}', ResetPassword::class)->name('password.reset')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// -----------------------------------------------
// ESPACE MEMBRE
// -----------------------------------------------
Route::middleware(['auth', 'membre'])
    ->prefix('membre')
    ->name('membre.')
    ->group(function () {
        Route::get('/dashboard',        Dashboard::class)->name('dashboard');
        Route::get('/profil',           Profile::class)->name('profile');
        Route::get('/annuaire',         Directory::class)->name('directory');
        Route::get('/opportunites',     Opportunities::class)->name('opportunities');
        Route::get('/evenements',       Events::class)->name('events');
        Route::get('/formations',       Trainings::class)->name('trainings');
        Route::get('/paiements',        Payments::class)->name('payments');
        Route::get('/recommandations',  Recommendations::class)->name('recommendations');
        Route::get('/mentorat',         Mentoring::class)->name('mentoring');
        Route::get('/progression',      Progress::class)->name('progress');
        Route::get('/contacts',         Contacts::class)->name('contacts');
        Route::get('/mon-reseau',       MyNetwork::class)->name('network');

        // Téléchargement du reçu PDF
        Route::get('/paiements/{payment}/recu', ReceiptController::class)->name('payment.receipt');

        // Justificatif de paiement (stockage privé — auth requise)
        Route::get('/paiements/{payment}/justificatif', PaymentScreenshotController::class)->name('payment.screenshot');
    });
