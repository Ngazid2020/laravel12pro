<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Mot de passe oublié')]
class ForgotPassword extends Component
{
    public string $email = '';
    public bool   $sent  = false;

    public function send(): void
    {
        $this->validate(['email' => 'required|email']);

        // On envoie le lien même si l'email n'existe pas (évite l'énumération)
        Password::sendResetLink(['email' => $this->email]);

        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
