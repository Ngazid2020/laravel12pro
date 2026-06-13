<?php

use App\Livewire\Auth\MemberLogin;
use App\Livewire\Member\Dashboard;
use App\Livewire\Member\Directory;
use App\Livewire\Member\Contacts;
use App\Livewire\Member\Events;
use App\Livewire\Member\Mentoring;
use App\Livewire\Member\MyNetwork;
use App\Livewire\Member\Opportunities;
use App\Livewire\Member\Payments;
use App\Livewire\Member\Profile;
use App\Livewire\Member\Progress;
use App\Livewire\Member\Recommendations;
use App\Livewire\Member\Trainings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirection racine
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole(['super_admin', 'admin'])) {
            return redirect('/admin');
        }
        return redirect()->route('membre.dashboard');
    }
    return redirect()->route('member.login');
});

// Auth membre
Route::get('/login', MemberLogin::class)->name('member.login')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('member.login');
})->name('logout');

// Espace membre
Route::middleware(['auth', 'membre'])
    ->prefix('membre')
    ->name('membre.')
    ->group(function () {
        Route::get('/dashboard',    Dashboard::class)->name('dashboard');
        Route::get('/profil',       Profile::class)->name('profile');
        Route::get('/annuaire',     Directory::class)->name('directory');
        Route::get('/opportunites', Opportunities::class)->name('opportunities');
        Route::get('/evenements',   Events::class)->name('events');
        Route::get('/formations',       Trainings::class)->name('trainings');
        Route::get('/paiements',        Payments::class)->name('payments');
        Route::get('/recommandations',  Recommendations::class)->name('recommendations');
        Route::get('/mentorat',         Mentoring::class)->name('mentoring');
        Route::get('/progression',      Progress::class)->name('progress');
        Route::get('/contacts',         Contacts::class)->name('contacts');
        Route::get('/mon-reseau',       MyNetwork::class)->name('network');
    });
