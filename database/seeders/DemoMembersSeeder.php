<?php

namespace Database\Seeders;

use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoMembersSeeder extends Seeder
{
    public function run(): void
    {
        // Plans de cotisation
        $planMensuel = SubscriptionPlan::firstOrCreate(['name' => 'Adhésion mensuelle'], ['description' => 'Cotisation mensuelle', 'amount' => 5000,  'period' => 'monthly', 'is_active' => true]);
        $planAnnuel  = SubscriptionPlan::firstOrCreate(['name' => 'Adhésion annuelle'],  ['description' => 'Cotisation annuelle',  'amount' => 40000, 'period' => 'annual',  'is_active' => true]);
        $planTrimestriel = $planMensuel; // Alias pour la répartition ci-dessous (period = monthly)

        $members = [
            // Mentors
            [
                'user'    => ['first_name' => 'Fatima',    'last_name' => 'Ahamada',   'email' => 'fatima.ahamada@reseau.km',   'phone' => '+269 321 10 01'],
                'profile' => ['company_name' => 'Comores Commerce International', 'sector' => 'Commerce & Distribution',    'city' => 'Moroni',      'bio' => 'Entrepreneur engagée dans le commerce équitable aux Comores depuis 10 ans. Mentore plusieurs jeunes entrepreneurs.', 'skills_offered' => ['Gestion commerciale', 'Négociation', 'Export'], 'needs_expressed' => ['Digitalisation'], 'status' => 'active', 'months_ago' => 18, 'plan' => $planAnnuel,   'is_mentor' => true],
            ],
            [
                'user'    => ['first_name' => 'Omar',      'last_name' => 'Soilihi',   'email' => 'omar.soilihi@reseau.km',     'phone' => '+269 321 10 02'],
                'profile' => ['company_name' => 'DigitalKom SARL',                    'sector' => 'Technologie',                'city' => 'Moroni',      'bio' => 'Développeur full-stack et fondateur de DigitalKom. Accompagne les startups comoroises dans leur transformation numérique.', 'skills_offered' => ['Développement web', 'UX/UI', 'Startups'], 'needs_expressed' => ['Financement'], 'status' => 'active', 'months_ago' => 24, 'plan' => $planAnnuel,   'is_mentor' => true],
            ],
            // Membres actifs
            [
                'user'    => ['first_name' => 'Mohamed',   'last_name' => 'Said',      'email' => 'mohamed.said@reseau.km',     'phone' => '+269 321 10 03'],
                'profile' => ['company_name' => 'Pêcheries Said & Fils',              'sector' => 'Agriculture & Pêche',        'city' => 'Mutsamudu',   'bio' => 'Entrepreneur dans la pêche artisanale. Cherche à moderniser et exporter ses produits.', 'skills_offered' => ['Pêche artisanale', 'Transformation poisson'], 'needs_expressed' => ['Équipement', 'Export'], 'status' => 'active', 'months_ago' => 10, 'plan' => $planTrimestriel, 'mentor_email' => 'fatima.ahamada@reseau.km'],
            ],
            [
                'user'    => ['first_name' => 'Aisha',     'last_name' => 'Combo',     'email' => 'aisha.combo@reseau.km',      'phone' => '+269 321 10 04'],
                'profile' => ['company_name' => 'Ylang Hôtel & Spa',                  'sector' => 'Tourisme & Hôtellerie',      'city' => 'Moroni',      'bio' => 'Fondatrice d\'un éco-hôtel axé sur le tourisme durable. Passionnée par la valorisation du patrimoine comorien.', 'skills_offered' => ['Accueil touristique', 'Gestion hôtelière'], 'needs_expressed' => ['Marketing digital', 'Partenariats'], 'status' => 'active', 'months_ago' => 8, 'plan' => $planTrimestriel, 'mentor_email' => 'fatima.ahamada@reseau.km'],
            ],
            [
                'user'    => ['first_name' => 'Ibrahim',   'last_name' => 'Bacar',     'email' => 'ibrahim.bacar@reseau.km',    'phone' => '+269 321 10 05'],
                'profile' => ['company_name' => 'Bacar Construction',                 'sector' => 'BTP & Immobilier',           'city' => 'Fomboni',     'bio' => 'Chef d\'entreprise spécialisé dans la construction de logements économiques à Mohéli.', 'skills_offered' => ['Génie civil', 'Gestion de chantier'], 'needs_expressed' => ['Main d\'œuvre qualifiée', 'Matériaux'], 'status' => 'active', 'months_ago' => 6, 'plan' => $planMensuel, 'mentor_email' => 'omar.soilihi@reseau.km'],
            ],
            [
                'user'    => ['first_name' => 'Khadija',   'last_name' => 'Madi',      'email' => 'khadija.madi@reseau.km',     'phone' => '+269 321 10 06'],
                'profile' => ['company_name' => 'Artisanat Ngazidja',                 'sector' => 'Artisanat',                  'city' => 'Moroni',      'bio' => 'Artisane engagée dans la promotion du savoir-faire traditionnel comorien à l\'international.', 'skills_offered' => ['Tissage', 'Broderie', 'Artisanat local'], 'needs_expressed' => ['Boutique en ligne', 'Réseau international'], 'status' => 'active', 'months_ago' => 4, 'plan' => $planMensuel, 'mentor_email' => 'fatima.ahamada@reseau.km'],
            ],
            [
                'user'    => ['first_name' => 'Maryam',    'last_name' => 'Farid',     'email' => 'maryam.farid@reseau.km',     'phone' => '+269 321 10 07'],
                'profile' => ['company_name' => 'Clinique Shifa',                     'sector' => 'Santé',                      'city' => 'Mutsamudu',   'bio' => 'Médecin entrepreneur qui développe une clinique de soins primaires accessibles à Anjouan.', 'skills_offered' => ['Médecine générale', 'Santé publique'], 'needs_expressed' => ['Équipements médicaux', 'Gestion RH'], 'status' => 'active', 'months_ago' => 12, 'plan' => $planAnnuel, 'mentor_email' => 'omar.soilihi@reseau.km'],
            ],
            [
                'user'    => ['first_name' => 'Abdallah',  'last_name' => 'Houmadi',   'email' => 'abdallah.houmadi@reseau.km', 'phone' => '+269 321 10 08'],
                'profile' => ['company_name' => 'École Avenir Comores',               'sector' => 'Éducation & Formation',      'city' => 'Domoni',      'bio' => 'Fondateur d\'un réseau d\'écoles privées à Anjouan. Croit en l\'éducation comme levier de développement.', 'skills_offered' => ['Pédagogie', 'Management éducatif'], 'needs_expressed' => ['Ressources pédagogiques', 'Formation enseignants'], 'status' => 'active', 'months_ago' => 5, 'plan' => $planMensuel, 'mentor_email' => 'fatima.ahamada@reseau.km'],
            ],
            [
                'user'    => ['first_name' => 'Noura',     'last_name' => 'Anliati',   'email' => 'noura.anliati@reseau.km',    'phone' => '+269 321 10 09'],
                'profile' => ['company_name' => 'Comores Microfinance',               'sector' => 'Finance & Assurance',        'city' => 'Moroni',      'bio' => 'Consultante en microfinance spécialisée dans le financement des TPE comoroises.', 'skills_offered' => ['Comptabilité', 'Finance', 'Crédit'], 'needs_expressed' => ['Partenariats bancaires'], 'status' => 'active', 'months_ago' => 9, 'plan' => $planTrimestriel, 'mentor_email' => 'omar.soilihi@reseau.km'],
            ],
            [
                'user'    => ['first_name' => 'Hachim',    'last_name' => 'Hamidou',   'email' => 'hachim.hamidou@reseau.km',   'phone' => '+269 321 10 10'],
                'profile' => ['company_name' => 'Hamidou Import Export',              'sector' => 'Import-Export',              'city' => 'Mutsamudu',   'bio' => 'Importateur de matériaux de construction et équipements électroniques. Active entre Dubaï et les Comores.', 'skills_offered' => ['Commerce international', 'Logistique'], 'needs_expressed' => ['Stockage', 'Transport maritime'], 'status' => 'active', 'months_ago' => 7, 'plan' => $planTrimestriel, 'mentor_email' => 'omar.soilihi@reseau.km'],
            ],
            // Membre suspendu
            [
                'user'    => ['first_name' => 'Zoubeda',   'last_name' => 'Mchangama', 'email' => 'zoubeda.mchangama@reseau.km','phone' => '+269 321 10 11'],
                'profile' => ['company_name' => 'Boutique Mode Zoubeda',              'sector' => 'Commerce & Distribution',    'city' => 'Fomboni',     'bio' => 'Créatrice de mode inspirée des textiles comoriens traditionnels.', 'skills_offered' => ['Couture', 'Mode'], 'needs_expressed' => ['Réseau'], 'status' => 'suspended', 'months_ago' => 14, 'plan' => $planMensuel],
            ],
            // Candidature en attente
            [
                'user'    => ['first_name' => 'Salim',     'last_name' => 'Abdallah',  'email' => 'salim.abdallah@reseau.km',   'phone' => '+269 321 10 12'],
                'profile' => ['company_name' => 'AgriTech Comores',                   'sector' => 'Agriculture & Pêche',        'city' => 'Moroni',      'bio' => 'Jeune entrepreneur qui souhaite moderniser l\'agriculture comorienne grâce aux nouvelles technologies.', 'skills_offered' => ['Agriculture', 'Innovation'], 'needs_expressed' => ['Financement', 'Mentorat'], 'status' => 'candidate', 'months_ago' => 1, 'plan' => $planMensuel],
            ],
        ];

        $createdProfiles = [];

        foreach ($members as $data) {
            $u = $data['user'];
            $p = $data['profile'];

            $user = User::firstOrCreate(['email' => $u['email']], [
                'name'              => $u['first_name'].' '.$u['last_name'],
                'first_name'        => $u['first_name'],
                'last_name'         => $u['last_name'],
                'phone'             => $u['phone'],
                'password'          => Hash::make('Demo@2026!'),
                'email_verified_at' => now(),
            ]);

            $activatedAt = $p['status'] === 'active' ? now()->subMonths($p['months_ago']) : null;
            $expiresAt   = $p['status'] === 'active' ? now()->subMonths($p['months_ago'])->addYear() : null;

            $profile = MemberProfile::firstOrCreate(['user_id' => $user->id], [
                'company_name'         => $p['company_name'],
                'sector'               => $p['sector'],
                'city'                 => $p['city'],
                'bio'                  => $p['bio'],
                'skills_offered'       => $p['skills_offered'],
                'needs_expressed'      => $p['needs_expressed'],
                'social_links'         => [],
                'referral_code'        => strtoupper(substr($u['first_name'], 0, 3).substr($u['last_name'], 0, 3)).rand(100, 999),
                'membership_status'    => $p['status'],
                'membership_expires_at'=> $expiresAt,
                'activated_at'         => $activatedAt,
            ]);

            $createdProfiles[$u['email']] = $profile;

            // Paiement validé pour membres actifs
            if ($p['status'] === 'active') {
                Payment::firstOrCreate(
                    ['user_id' => $user->id, 'subscription_plan_id' => $p['plan']->id, 'status' => 'validated'],
                    [
                        'method'                => collect(['mvola', 'holo_money', 'cash'])->random(),
                        'amount'                => $p['plan']->amount,
                        'transaction_reference' => 'TXN'.strtoupper(substr(md5($u['email']), 0, 8)),
                        'validated_at'          => now()->subMonths($p['months_ago']),
                        'period_start'          => now()->subMonths($p['months_ago']),
                        'period_end'            => now()->subMonths($p['months_ago'])->addYear(),
                    ]
                );
            }
        }

        // Assigner les mentors (après que tous les profils sont créés)
        $mentorAssignments = [
            'mohamed.said@reseau.km'      => 'fatima.ahamada@reseau.km',
            'aisha.combo@reseau.km'       => 'fatima.ahamada@reseau.km',
            'khadija.madi@reseau.km'      => 'fatima.ahamada@reseau.km',
            'abdallah.houmadi@reseau.km'  => 'fatima.ahamada@reseau.km',
            'ibrahim.bacar@reseau.km'     => 'omar.soilihi@reseau.km',
            'maryam.farid@reseau.km'      => 'omar.soilihi@reseau.km',
            'noura.anliati@reseau.km'     => 'omar.soilihi@reseau.km',
            'hachim.hamidou@reseau.km'    => 'omar.soilihi@reseau.km',
        ];

        foreach ($mentorAssignments as $menteeEmail => $mentorEmail) {
            if (isset($createdProfiles[$menteeEmail], $createdProfiles[$mentorEmail])) {
                $createdProfiles[$menteeEmail]->update(['mentor_id' => $createdProfiles[$mentorEmail]->id]);
            }
        }
    }
}
