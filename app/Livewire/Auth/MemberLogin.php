<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Connexion')]
class MemberLogin extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:8')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Identifiants incorrects.');
            return;
        }

        session()->regenerate();

        $user = Auth::user();

        if ($user->hasRole(['super_admin', 'admin'])) {
            $this->redirect('/admin', navigate: true);
            return;
        }

        $this->redirect(route('membre.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.member-login');
    }
}
