<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Définir mon mot de passe')]
class ResetPassword extends Component
{
    public string $token    = '';
    #[Url]
    public string $email    = '';
    public string $password = '';
    public string $passwordConfirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
    }

    public function save(): void
    {
        $this->validate([
            'token'               => 'required',
            'email'               => 'required|email',
            'password'            => 'required|min:8|same:passwordConfirmation',
            'passwordConfirmation'=> 'required',
        ], [
            'password.same'   => 'Les mots de passe ne correspondent pas.',
            'password.min'    => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $status = Password::reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->passwordConfirmation,
                'token'                 => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Mot de passe défini avec succès. Vous pouvez maintenant vous connecter.');
            $this->redirect(route('member.login'));
            return;
        }

        $this->addError('email', __($status));
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
