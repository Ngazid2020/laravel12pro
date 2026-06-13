<?php

namespace Database\Seeders;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestMemberSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'hamid@reseau.km'],
            [
                'name'     => 'Hamid Mchangama',
                'password' => bcrypt('Membre@2026!'),
            ]
        );

        MemberProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'company_name'          => 'Comores Digital',
                'sector'                => 'Technologie',
                'city'                  => 'Moroni',
                'bio'                   => 'Entrepreneur passionné par le numérique et l\'innovation aux Comores.',
                'skills_offered'        => ['Développement web', 'Marketing digital'],
                'needs_expressed'       => ['Financement', 'Mentoring'],
                'referral_code'         => 'HAMID2026',
                'membership_status'     => 'active',
                'membership_expires_at' => now()->addYear(),
                'activated_at'          => now(),
            ]
        );
    }
}
