<?php

namespace App\Livewire\Member;

use App\Models\MemberProfile;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Mon profil')]
class Profile extends Component
{
    use Toast, WithFileUploads;

    // Infos utilisateur
    public string $name        = '';
    public string $first_name  = '';
    public string $last_name   = '';
    public string $phone       = '';

    // Profil membre
    public string $company_name  = '';
    public string $project_name  = '';
    public string $sector        = '';
    public string $city          = '';
    public string $bio           = '';
    public array  $skills_offered   = [];
    public array  $needs_expressed  = [];
    public array  $social_links     = [];

    public $avatar;

    public function mount(): void
    {
        $user    = auth()->user();
        $profile = $user->profile;

        $this->name       = $user->name;
        $this->first_name = $user->first_name ?? '';
        $this->last_name  = $user->last_name ?? '';
        $this->phone      = $user->phone ?? '';

        if ($profile) {
            $this->company_name   = $profile->company_name ?? '';
            $this->project_name   = $profile->project_name ?? '';
            $this->sector         = $profile->sector ?? '';
            $this->city           = $profile->city ?? '';
            $this->bio            = $profile->bio ?? '';
            $this->skills_offered = $profile->skills_offered ?? [];
            $this->needs_expressed= $profile->needs_expressed ?? [];
            $this->social_links   = $profile->social_links ?? [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'name'       => 'required|string|max:255',
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'phone'      => 'nullable|string|max:30',
            'bio'        => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        $userData = [
            'name'       => $this->name,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'phone'      => $this->phone,
        ];

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $userData['avatar'] = $path;
        }

        $user->update($userData);

        MemberProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name'   => $this->company_name,
                'project_name'   => $this->project_name,
                'sector'         => $this->sector,
                'city'           => $this->city,
                'bio'            => $this->bio,
                'skills_offered' => $this->skills_offered,
                'needs_expressed'=> $this->needs_expressed,
                'social_links'   => $this->social_links,
            ]
        );

        $this->success('Profil mis à jour avec succès.');
    }

    public function render()
    {
        $user    = auth()->user();
        $profile = $user->profile;

        return view('livewire.member.profile', compact('profile'));
    }
}
