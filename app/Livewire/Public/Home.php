<?php

namespace App\Livewire\Public;

use App\Models\CandidatureApplication;
use App\Models\PartnerCompany;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.public')]
#[Title('Réseau des Jeunes Entrepreneurs des Comores')]
class Home extends Component
{
    use Toast;

    // Formulaire de candidature
    public string $firstName   = '';
    public string $lastName    = '';
    public string $email       = '';
    public string $phone       = '';
    public string $company     = '';
    public string $sector      = '';
    public string $motivation  = '';
    public bool   $submitted   = false;

    protected function rules(): array
    {
        return [
            'firstName'  => 'required|string|max:80',
            'lastName'   => 'required|string|max:80',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string|max:30',
            'company'    => 'nullable|string|max:120',
            'sector'     => 'nullable|string|max:80',
            'motivation' => 'required|string|min:50|max:2000',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.unique'        => 'Un compte existe déjà avec cet email.',
            'motivation.min'      => 'Votre motivation doit contenir au moins 50 caractères.',
        ];
    }

    public function apply(): void
    {
        $this->validate();

        $user = User::create([
            'name'       => trim("{$this->firstName} {$this->lastName}"),
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'password'   => Hash::make(Str::random(16)),
        ]);

        CandidatureApplication::create([
            'user_id'    => $user->id,
            'motivation' => $this->motivation,
            'status'     => 'pending',
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        $partners = PartnerCompany::public()->orderBy('name')->take(6)->get();

        return view('livewire.public.home', compact('partners'));
    }
}
