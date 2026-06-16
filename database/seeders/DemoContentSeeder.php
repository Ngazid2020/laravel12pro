<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\CandidatureApplication;
use App\Models\Event;
use App\Models\Level;
use App\Models\LevelReward;
use App\Models\Opportunity;
use App\Models\PartnerCompany;
use App\Models\Training;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@reseau.km')->first();

        // ─── NIVEAUX ───────────────────────────────────────────────────────────
        $levels = [
            ['name' => 'Débutant',  'slug' => 'debutant',  'min_points' => 0,    'required_trainings' => 0, 'required_months' => 0, 'grants_mentor_status' => false, 'badge_color' => '#94a3b8', 'order' => 1, 'description' => 'Bienvenue dans le réseau !'],
            ['name' => 'Actif',     'slug' => 'actif',     'min_points' => 100,  'required_trainings' => 1, 'required_months' => 1, 'grants_mentor_status' => false, 'badge_color' => '#3b82f6', 'order' => 2, 'description' => 'Vous participez activement au réseau.'],
            ['name' => 'Confirmé',  'slug' => 'confirme',  'min_points' => 300,  'required_trainings' => 2, 'required_months' => 3, 'grants_mentor_status' => false, 'badge_color' => '#22c55e', 'order' => 3, 'description' => 'Entrepreneur confirmé et pilier du réseau.'],
            ['name' => 'Expert',    'slug' => 'expert',    'min_points' => 600,  'required_trainings' => 4, 'required_months' => 6, 'grants_mentor_status' => true,  'badge_color' => '#a855f7', 'order' => 4, 'description' => 'Expert reconnu, éligible au statut de mentor.'],
            ['name' => 'Champion',  'slug' => 'champion',  'min_points' => 1000, 'required_trainings' => 6, 'required_months' => 12,'grants_mentor_status' => true,  'badge_color' => '#f59e0b', 'order' => 5, 'description' => 'Ambassadeur du réseau et modèle pour les membres.'],
        ];

        foreach ($levels as $l) {
            $level = Level::firstOrCreate(['slug' => $l['slug']], $l);
            if ($l['slug'] === 'expert' && $level->rewards()->doesntExist()) {
                LevelReward::create(['level_id' => $level->id, 'type' => 'badge',               'description' => 'Badge Expert affiché sur votre profil', 'value' => 'expert']);
                LevelReward::create(['level_id' => $level->id, 'type' => 'event_invitation',    'description' => 'Invitation prioritaire aux événements partenaires', 'value' => null]);
            }
            if ($l['slug'] === 'champion' && $level->rewards()->doesntExist()) {
                LevelReward::create(['level_id' => $level->id, 'type' => 'badge',               'description' => 'Badge Champion doré', 'value' => 'champion']);
                LevelReward::create(['level_id' => $level->id, 'type' => 'commission_rate',     'description' => '20% de réduction sur les formations premium', 'value' => '20']);
            }
        }

        // ─── CANDIDATURE EN ATTENTE ─────────────────────────────────────────────
        $salim = User::where('email', 'salim.abdallah@reseau.km')->first();
        if ($salim) {
            CandidatureApplication::firstOrCreate(['user_id' => $salim->id], [
                'motivation'   => 'Je souhaite rejoindre le Réseau des Jeunes Entrepreneurs des Comores afin de bénéficier de l\'accompagnement et du réseau nécessaires pour développer mon projet AgriTech. Mon objectif est d\'introduire des techniques d\'agriculture de précision adaptées aux contraintes des Comores.',
                'attachments'  => [],
                'status'       => 'pending',
            ]);
        }

        // ─── ENTREPRISES PARTENAIRES ───────────────────────────────────────────
        $bict = PartnerCompany::firstOrCreate(['name' => 'BICT — Banque pour l\'Industrie et le Commerce'], [
            'description'   => 'Première banque de développement aux Comores, partenaire privilégié du réseau pour le financement des PME.',
            'sector'        => 'Finance & Assurance',
            'website'       => 'https://www.bic-comores.com',
            'contact_name'  => 'Directeur Commercial',
            'contact_email' => 'partenariat@bic-comores.com',
            'contact_phone' => '+269 773 00 00',
            'is_active'     => true,
            'show_publicly' => true,
        ]);

        $comtel = PartnerCompany::firstOrCreate(['name' => 'Comores Telecom'], [
            'description'   => 'Opérateur historique des Comores, soutient la digitalisation des entreprises locales.',
            'sector'        => 'Technologie',
            'website'       => 'https://www.comores-telecom.km',
            'contact_name'  => 'Responsable Partenariats',
            'contact_email' => 'entreprises@comores-telecom.km',
            'contact_phone' => '+269 773 10 00',
            'is_active'     => true,
            'show_publicly' => true,
        ]);

        PartnerCompany::firstOrCreate(['name' => 'SNPSF — Société Nationale des Postes'], [
            'description'   => 'Réseau postal et financier national, présent dans toutes les îles des Comores.',
            'sector'        => 'Finance & Assurance',
            'website'       => null,
            'contact_name'  => 'Direction Générale',
            'contact_email' => 'direction@snpsf.km',
            'contact_phone' => '+269 773 20 00',
            'is_active'     => true,
            'show_publicly' => false,
        ]);

        // ─── ÉVÉNEMENTS ────────────────────────────────────────────────────────
        $fatima = User::where('email', 'fatima.ahamada@reseau.km')->first();
        $omar   = User::where('email', 'omar.soilihi@reseau.km')->first();
        $organizer = $fatima ?? $admin;

        Event::firstOrCreate(['title' => 'Soirée Networking — Rentrée 2025'], [
            'description'  => "Grande soirée de networking pour relancer l'année entrepreneuriale. Rencontrez les nouveaux membres, partagez vos projets et créez des synergies.\n\nAu programme : pitchs éclair de 2 minutes, tables rondes thématiques et buffet comorien.",
            'organizer_id' => $organizer?->id ?? $admin->id,
            'type'         => 'networking',
            'starts_at'    => now()->subMonths(6)->setTime(18, 0),
            'ends_at'      => now()->subMonths(6)->setTime(21, 0),
            'location'     => 'Hôtel Itsandra, Moroni',
            'capacity'     => 80,
            'is_paid'      => false,
            'price'        => 0,
            'is_published' => true,
        ]);

        Event::firstOrCreate(['title' => 'Atelier : Financer son projet aux Comores'], [
            'description'  => "Atelier pratique animé par des experts bancaires et des entrepreneurs ayant réussi à obtenir un financement. Sujets abordés : micro-crédit, crowdfunding, investisseurs locaux, aides de l'État.",
            'organizer_id' => $omar?->id ?? $admin->id,
            'type'         => 'workshop',
            'starts_at'    => now()->subMonths(3)->setTime(9, 0),
            'ends_at'      => now()->subMonths(3)->setTime(13, 0),
            'location'     => 'Salle de conférence BICT, Moroni',
            'capacity'     => 40,
            'is_paid'      => false,
            'price'        => 0,
            'is_published' => true,
        ]);

        Event::firstOrCreate(['title' => 'Forum Entrepreneuriat Comorien 2026'], [
            'description'  => "Le plus grand rassemblement annuel des entrepreneurs comoriens. Conférences inspirantes, stands d'exposition, concours de pitch et remise des prix.\n\nCette année : focus sur l'économie bleue et l'agritech.",
            'organizer_id' => $admin->id,
            'type'         => 'conference',
            'starts_at'    => now()->addMonths(2)->setTime(8, 30),
            'ends_at'      => now()->addMonths(2)->addDay()->setTime(18, 0),
            'location'     => 'Palais du Peuple, Moroni',
            'capacity'     => 200,
            'is_paid'      => true,
            'price'        => 5000,
            'is_published' => true,
        ]);

        Event::firstOrCreate(['title' => 'Webinaire : E-commerce & Vente en ligne depuis les Comores'], [
            'description'  => 'Webinaire interactif sur les stratégies pour vendre en ligne depuis les Comores : plateformes disponibles, logistique internationale, paiement en devises.',
            'organizer_id' => $omar?->id ?? $admin->id,
            'type'         => 'masterclass',
            'starts_at'    => now()->addWeeks(3)->setTime(15, 0),
            'ends_at'      => now()->addWeeks(3)->setTime(17, 0),
            'location'     => 'En ligne (Zoom)',
            'capacity'     => 100,
            'is_paid'      => false,
            'price'        => 0,
            'is_published' => true,
        ]);

        // ─── FORMATIONS ────────────────────────────────────────────────────────
        $formation1 = Training::firstOrCreate(['title' => 'Comptabilité de base pour entrepreneurs'], [
            'description'   => "Formation pratique couvrant les fondamentaux de la comptabilité : tenue de livre de caisse, facturation, TVA, bilan simplifié. Aucun prérequis comptable nécessaire.",
            'trainer_id'    => $fatima?->id ?? $admin->id,
            'prerequisites' => 'Aucun',
            'format'        => 'in_person',
            'capacity'      => 20,
            'price_type'    => 'free',
            'price'         => 0,
            'is_published'  => true,
        ]);

        TrainingSession::firstOrCreate(
            ['training_id' => $formation1->id, 'starts_at' => now()->subMonths(4)->setTime(9, 0)],
            [
                'ends_at'      => now()->subMonths(4)->setTime(17, 0),
                'location'     => 'Siège du Réseau, Moroni',
                'meeting_link' => null,
                'status'       => 'completed',
                'materials'    => ['Support PDF comptabilité', 'Modèle de livre de caisse Excel'],
            ]
        );

        $formation2 = Training::firstOrCreate(['title' => 'Marketing digital pour PME comoroises'], [
            'description'   => "Apprendre à promouvoir son entreprise sur les réseaux sociaux (Facebook, Instagram, TikTok), créer du contenu adapté au marché comorien et mesurer ses résultats.",
            'trainer_id'    => $omar?->id ?? $admin->id,
            'prerequisites' => 'Avoir un smartphone et un compte Facebook ou Instagram',
            'format'        => 'hybrid',
            'capacity'      => 25,
            'price_type'    => 'premium',
            'price'         => 8000,
            'is_published'  => true,
        ]);

        TrainingSession::firstOrCreate(
            ['training_id' => $formation2->id, 'starts_at' => now()->addMonths(1)->setTime(9, 0)],
            [
                'ends_at'      => now()->addMonths(1)->setTime(17, 0),
                'location'     => 'Espace Coworking Moroni',
                'meeting_link' => 'https://zoom.us/j/demo',
                'status'       => 'scheduled',
                'materials'    => [],
            ]
        );

        $formation3 = Training::firstOrCreate(['title' => 'Gestion de trésorerie et prévisions financières'], [
            'description'   => "Construire un tableau de bord financier simple, anticiper les besoins en trésorerie, comprendre les ratios clés et piloter son activité avec des indicateurs concrets.",
            'trainer_id'    => $fatima?->id ?? $admin->id,
            'prerequisites' => 'Avoir suivi la formation Comptabilité de base (ou équivalent)',
            'format'        => 'in_person',
            'capacity'      => 15,
            'price_type'    => 'premium',
            'price'         => 10000,
            'is_published'  => true,
        ]);

        TrainingSession::firstOrCreate(
            ['training_id' => $formation3->id, 'starts_at' => now()->addMonths(2)->addDays(5)->setTime(9, 0)],
            [
                'ends_at'      => now()->addMonths(2)->addDays(5)->setTime(17, 0),
                'location'     => 'Siège du Réseau, Moroni',
                'meeting_link' => null,
                'status'       => 'scheduled',
                'materials'    => [],
            ]
        );

        // ─── OPPORTUNITÉS ──────────────────────────────────────────────────────
        Opportunity::firstOrCreate(['title' => 'Stage développeur web — Comores Telecom'], [
            'description'       => "Comores Telecom recherche un stagiaire développeur web pour rejoindre l'équipe digitale. Mission : développement de l'espace client en ligne.\n\nProfil recherché : HTML/CSS/JS, PHP ou Python, capacité d'apprentissage rapide.",
            'published_by'      => $admin->id,
            'partner_company_id'=> $comtel->id,
            'type'              => 'internship',
            'sector'            => 'Technologie',
            'target_skills'     => ['Développement web', 'PHP', 'JavaScript'],
            'deadline'          => now()->addMonths(1),
            'is_active'         => true,
        ]);

        Opportunity::firstOrCreate(['title' => 'Partenariat export produits halieutiques — BICT'], [
            'description'       => "La BICT accompagne un projet d'exportation de produits halieutiques comoriens vers l'Europe et les pays du Golfe. Recherche d'entrepreneurs du secteur pêche souhaitant s'associer à ce projet.",
            'published_by'      => $admin->id,
            'partner_company_id'=> $bict->id,
            'type'              => 'mission',
            'sector'            => 'Agriculture & Pêche',
            'target_skills'     => ['Pêche artisanale', 'Commerce international', 'Logistique'],
            'deadline'          => now()->addMonths(2),
            'is_active'         => true,
        ]);

        Opportunity::firstOrCreate(['title' => 'Appel à projets : Agriculture durable — Union Européenne'], [
            'description'       => "Programme de subventions de l'UE pour les projets d'agriculture durable aux Comores. Budget disponible : jusqu\'à 50 000 € par projet. Clôture des dossiers dans 6 semaines.",
            'published_by'      => $fatima?->id ?? $admin->id,
            'partner_company_id'=> null,
            'type'              => 'tender',
            'sector'            => 'Agriculture & Pêche',
            'target_skills'     => ['Agriculture', 'Gestion de projet', 'Rédaction de dossiers'],
            'deadline'          => now()->addWeeks(6),
            'is_active'         => true,
        ]);

        Opportunity::firstOrCreate(['title' => 'Recrutement : Responsable commercial — Ylang Hôtel'], [
            'description'       => "Ylang Hôtel & Spa recrute un Responsable Commercial chargé de développer le portefeuille clients et les partenariats avec les agences de voyage.\n\nExpérience en tourisme ou hôtellerie exigée.",
            'published_by'      => $admin->id,
            'partner_company_id'=> null,
            'type'              => 'mission',
            'sector'            => 'Tourisme & Hôtellerie',
            'target_skills'     => ['Commercial', 'Tourisme', 'Négociation'],
            'deadline'          => now()->addMonths(1)->addDays(15),
            'is_active'         => true,
        ]);

        // ─── ANNONCES ──────────────────────────────────────────────────────────
        Announcement::firstOrCreate(['title' => 'Bienvenue sur la plateforme du Réseau !'], [
            'content'        => "Chers membres,\n\nNous sommes ravis de vous accueillir sur la nouvelle plateforme numérique du Réseau des Jeunes Entrepreneurs des Comores.\n\nDécouvrez l'annuaire des membres, inscrivez-vous aux événements et formations, et connectez-vous avec vos pairs.\n\nBonne navigation !",
            'published_by'   => $admin->id,
            'target_audience'=> 'all',
            'published_at'   => now()->subMonths(5),
            'expires_at'     => null,
        ]);

        Announcement::firstOrCreate(['title' => 'Forum Entrepreneuriat 2026 — Inscriptions ouvertes'], [
            'content'        => "Le Forum Entrepreneuriat Comorien 2026 ouvre ses inscriptions ! Cette année, le thème principal est « Économie bleue et innovations pour les Comores ».\n\nPlaces limitées à 200 participants. Tarif membre : 5 000 KMF.\n\nInscrivez-vous dès maintenant dans la section Événements.",
            'published_by'   => $admin->id,
            'target_audience'=> 'active',
            'published_at'   => now()->subWeeks(2),
            'expires_at'     => now()->addMonths(2),
        ]);

        Announcement::firstOrCreate(['title' => 'Nouveaux mentors disponibles — Prenez rendez-vous'], [
            'content'        => "Deux nouveaux mentors experts ont rejoint le programme de mentorat :\n\n• Fatima Ahamada — Commerce & Distribution, Export\n• Omar Soilihi — Technologie, Startups numériques\n\nSi vous souhaitez bénéficier d'un accompagnement personnalisé, rendez-vous dans la section Mentorat de votre espace membre.",
            'published_by'   => $admin->id,
            'target_audience'=> 'active',
            'published_at'   => now()->subMonths(1),
            'expires_at'     => null,
        ]);
    }
}
