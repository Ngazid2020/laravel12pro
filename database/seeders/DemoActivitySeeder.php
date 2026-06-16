<?php

namespace Database\Seeders;

use App\Models\ContactRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\MentoringSession;
use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use App\Models\PointEntry;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoActivitySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@reseau.km')->first();

        $members = [
            'fatima'   => User::where('email', 'fatima.ahamada@reseau.km')->first(),
            'omar'     => User::where('email', 'omar.soilihi@reseau.km')->first(),
            'mohamed'  => User::where('email', 'mohamed.said@reseau.km')->first(),
            'aisha'    => User::where('email', 'aisha.combo@reseau.km')->first(),
            'ibrahim'  => User::where('email', 'ibrahim.bacar@reseau.km')->first(),
            'khadija'  => User::where('email', 'khadija.madi@reseau.km')->first(),
            'maryam'   => User::where('email', 'maryam.farid@reseau.km')->first(),
            'abdallah' => User::where('email', 'abdallah.houmadi@reseau.km')->first(),
            'noura'    => User::where('email', 'noura.anliati@reseau.km')->first(),
            'hachim'   => User::where('email', 'hachim.hamidou@reseau.km')->first(),
            'hamid'    => User::where('email', 'hamid@reseau.km')->first(),
        ];
        $members = array_filter($members);

        // ─── INSCRIPTIONS AUX ÉVÉNEMENTS ───────────────────────────────────────
        $eventNetworking  = Event::where('title', 'like', '%Soirée Networking%')->first();
        $eventAtelier     = Event::where('title', 'like', '%Financer son projet%')->first();
        $eventForum       = Event::where('title', 'like', '%Forum Entrepreneuriat%')->first();
        $eventWebinaire   = Event::where('title', 'like', '%E-commerce%')->first();

        // Événement passé : Soirée Networking — tous ont participé
        if ($eventNetworking) {
            foreach ($members as $key => $user) {
                EventRegistration::firstOrCreate(
                    ['event_id' => $eventNetworking->id, 'user_id' => $user->id],
                    ['checked_in_at' => $eventNetworking->starts_at->addMinutes(rand(5, 45))]
                );
            }
        }

        // Événement passé : Atelier financement — la moitié des membres
        if ($eventAtelier) {
            $atelier_attendees = ['fatima', 'omar', 'noura', 'hachim', 'aisha', 'hamid'];
            foreach ($atelier_attendees as $key) {
                if (isset($members[$key])) {
                    EventRegistration::firstOrCreate(
                        ['event_id' => $eventAtelier->id, 'user_id' => $members[$key]->id],
                        ['checked_in_at' => $eventAtelier->starts_at->addMinutes(rand(5, 30))]
                    );
                }
            }
        }

        // Événement à venir : Forum — quelques inscrits
        if ($eventForum) {
            $forum_registered = ['fatima', 'omar', 'aisha', 'maryam', 'hamid'];
            foreach ($forum_registered as $key) {
                if (isset($members[$key])) {
                    EventRegistration::firstOrCreate(
                        ['event_id' => $eventForum->id, 'user_id' => $members[$key]->id],
                        ['checked_in_at' => null]
                    );
                }
            }
        }

        // Webinaire à venir — quelques inscrits
        if ($eventWebinaire) {
            $webinaire_registered = ['omar', 'hachim', 'khadija', 'ibrahim'];
            foreach ($webinaire_registered as $key) {
                if (isset($members[$key])) {
                    EventRegistration::firstOrCreate(
                        ['event_id' => $eventWebinaire->id, 'user_id' => $members[$key]->id],
                        ['checked_in_at' => null]
                    );
                }
            }
        }

        // ─── INSCRIPTIONS AUX FORMATIONS ──────────────────────────────────────
        $formation1 = Training::where('title', 'like', '%Comptabilité de base%')->first();
        $formation2 = Training::where('title', 'like', '%Marketing digital%')->first();

        $session1 = $formation1?->sessions()->first();
        $session2 = $formation2?->sessions()->first();

        // Formation passée : Comptabilité — avec notes et évaluations
        if ($session1) {
            $comptabilite_attendees = [
                'mohamed'  => ['rating' => 5, 'comment' => 'Formation très pratique, j\'ai pu tenir mes comptes dès le lendemain.'],
                'khadija'  => ['rating' => 4, 'comment' => 'Très bien expliqué. J\'aurais aimé plus d\'exemples du secteur artisanat.'],
                'ibrahim'  => ['rating' => 5, 'comment' => 'Excellent ! Les modèles Excel fournis sont directement utilisables.'],
                'abdallah' => ['rating' => 4, 'comment' => 'Contenu riche. Formatrice très pédagogue.'],
                'aisha'    => ['rating' => 5, 'comment' => 'Parfait pour les débutants. J\'ai tout compris.'],
                'hamid'    => ['rating' => 4, 'comment' => 'Bonne formation, contenu solide.'],
            ];

            foreach ($comptabilite_attendees as $key => $eval) {
                if (isset($members[$key])) {
                    TrainingEnrollment::firstOrCreate(
                        ['training_session_id' => $session1->id, 'user_id' => $members[$key]->id],
                        [
                            'status'      => 'attended',
                            'attended_at' => $session1->starts_at->addHours(1),
                            'rating'      => $eval['rating'],
                            'comment'     => $eval['comment'],
                        ]
                    );
                }
            }
        }

        // Formation à venir : Marketing digital — quelques pré-inscrits
        if ($session2) {
            $marketing_enrolled = ['hachim', 'maryam', 'noura', 'aisha'];
            foreach ($marketing_enrolled as $key) {
                if (isset($members[$key])) {
                    TrainingEnrollment::firstOrCreate(
                        ['training_session_id' => $session2->id, 'user_id' => $members[$key]->id],
                        ['status' => 'enrolled', 'attended_at' => null, 'rating' => null, 'comment' => null]
                    );
                }
            }
        }

        // ─── CANDIDATURES AUX OPPORTUNITÉS ────────────────────────────────────
        $oppStage      = Opportunity::where('title', 'like', '%Stage développeur%')->first();
        $oppPartenariat= Opportunity::where('title', 'like', '%export produits halieutiques%')->first();
        $oppEmploi     = Opportunity::where('title', 'like', '%Responsable commercial%')->first();

        if ($oppStage && isset($members['hamid'])) {
            OpportunityApplication::firstOrCreate(
                ['opportunity_id' => $oppStage->id, 'user_id' => $members['hamid']->id],
                ['message' => 'Je suis développeur web avec 2 ans d\'expérience en PHP/Laravel. Je souhaite apporter mes compétences à Comores Telecom pour développer des solutions adaptées au marché local.', 'status' => 'pending']
            );
        }

        if ($oppStage && isset($members['ibrahim'])) {
            OpportunityApplication::firstOrCreate(
                ['opportunity_id' => $oppStage->id, 'user_id' => $members['ibrahim']->id],
                ['message' => 'Bien que principalement dans le BTP, je développe des outils de gestion numérique pour mon entreprise. Je souhaite renforcer mes compétences techniques.', 'status' => 'closed']
            );
        }

        if ($oppPartenariat && isset($members['mohamed'])) {
            OpportunityApplication::firstOrCreate(
                ['opportunity_id' => $oppPartenariat->id, 'user_id' => $members['mohamed']->id],
                ['message' => 'Ma pêcherie traite en moyenne 500 kg de poisson par semaine. Je suis très intéressé par ce projet d\'exportation et dispose déjà de contacts à La Réunion.', 'status' => 'contacted']
            );
        }

        if ($oppEmploi && isset($members['aisha'])) {
            OpportunityApplication::firstOrCreate(
                ['opportunity_id' => $oppEmploi->id, 'user_id' => $members['aisha']->id],
                ['message' => 'Fondatrice d\'un hôtel, je connais bien le secteur et les agences de voyage partenaires. Je serais heureuse de contribuer au développement commercial de Ylang Hôtel.', 'status' => 'pending']
            );
        }

        // ─── SESSIONS DE MENTORAT ──────────────────────────────────────────────
        $fatima = $members['fatima'] ?? null;
        $omar   = $members['omar']   ?? null;

        $mentoratData = [
            // Sessions passées de Fatima
            ['mentor' => $fatima, 'mentee' => $members['mohamed']  ?? null, 'months_ago' => 3, 'status' => 'confirmed', 'notes' => 'Discussion sur la stratégie d\'exportation des produits halieutiques. Mohamed a identifié 3 contacts potentiels à La Réunion.'],
            ['mentor' => $fatima, 'mentee' => $members['khadija']  ?? null, 'months_ago' => 2, 'status' => 'confirmed', 'notes' => 'Accompagnement pour la création d\'une boutique en ligne. Plan d\'action établi sur 3 mois.'],
            ['mentor' => $fatima, 'mentee' => $members['abdallah'] ?? null, 'months_ago' => 1, 'status' => 'confirmed', 'notes' => 'Revue du business plan de l\'École Avenir. Recommandations sur la structure tarifaire.'],
            // Sessions à venir
            ['mentor' => $fatima, 'mentee' => $members['aisha']    ?? null, 'weeks_ahead' => 2, 'status' => 'scheduled', 'notes' => ''],
            ['mentor' => $omar,   'mentee' => $members['hamid']    ?? null, 'months_ago' => 2, 'status' => 'confirmed', 'notes' => 'Session de revue du projet Tech Comores. Conseils sur l\'approche marché B2B.'],
            ['mentor' => $omar,   'mentee' => $members['noura']    ?? null, 'months_ago' => 1, 'status' => 'confirmed', 'notes' => 'Analyse de la viabilité du modèle microfinance. Introduction aux outils de scoring crédit.'],
            ['mentor' => $omar,   'mentee' => $members['hachim']   ?? null, 'weeks_ahead' => 1, 'status' => 'scheduled', 'notes' => ''],
        ];

        foreach ($mentoratData as $s) {
            if (! $s['mentor'] || ! $s['mentee']) {
                continue;
            }

            $scheduledAt = isset($s['months_ago'])
                ? now()->subMonths($s['months_ago'])->setTime(10, 0)
                : now()->addWeeks($s['weeks_ahead'])->setTime(10, 0);

            $heldAt = $s['status'] === 'completed' ? $scheduledAt->copy()->addMinutes(5) : null;

            MentoringSession::firstOrCreate(
                ['mentor_id' => $s['mentor']->id, 'mentee_id' => $s['mentee']->id, 'scheduled_at' => $scheduledAt],
                [
                    'held_at'             => $heldAt,
                    'status'              => $s['status'],
                    'notes'               => $s['notes'],
                    'confirmed_by_mentee' => $s['status'] === 'confirmed',
                ]
            );
        }

        // ─── DEMANDES DE CONTACT ──────────────────────────────────────────────
        $contacts = [
            ['sender' => 'hachim',   'receiver' => 'fatima',   'status' => 'accepted', 'message' => 'Bonjour Fatima, je souhaite discuter d\'un partenariat pour l\'import de matériaux de construction. Votre expertise en commerce international serait précieuse.'],
            ['sender' => 'maryam',   'receiver' => 'noura',    'status' => 'accepted', 'message' => 'Bonjour Noura, je cherche à comprendre les options de financement pour l\'équipement médical. Votre profil en microfinance m\'intéresse beaucoup.'],
            ['sender' => 'ibrahim',  'receiver' => 'hachim',   'status' => 'pending',  'message' => 'Salut Hachim, tu importe des matériaux de construction ? J\'aurais besoin de ciment et de ferraille pour un chantier à Fomboni.'],
            ['sender' => 'abdallah', 'receiver' => 'omar',     'status' => 'accepted', 'message' => 'Bonjour Omar, je souhaite digitaliser le système de gestion de mes écoles. Pourriez-vous m\'orienter ?'],
            ['sender' => 'khadija',  'receiver' => 'aisha',    'status' => 'pending',  'message' => 'Bonjour Aisha, j\'aimerais proposer mes produits artisanaux dans ton hôtel comme souvenirs pour les touristes.'],
        ];

        foreach ($contacts as $c) {
            if (! isset($members[$c['sender']], $members[$c['receiver']])) {
                continue;
            }
            ContactRequest::firstOrCreate(
                ['sender_id' => $members[$c['sender']]->id, 'receiver_id' => $members[$c['receiver']]->id],
                ['message' => $c['message'], 'status' => $c['status']]
            );
        }

        // ─── POINTS ───────────────────────────────────────────────────────────
        $pointsData = [
            'fatima'   => [['source' => 'training_attended', 'pts' => 50, 'desc' => 'Formation Comptabilité de base'], ['source' => 'contribution', 'pts' => 20, 'desc' => 'Soirée Networking 2025'], ['source' => 'mentoring_session', 'pts' => 30, 'desc' => 'Session de mentorat (x3)'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'omar'     => [['source' => 'training_attended', 'pts' => 50, 'desc' => 'Formation Comptabilité de base'], ['source' => 'contribution', 'pts' => 20, 'desc' => 'Soirée Networking 2025'], ['source' => 'mentoring_session', 'pts' => 20, 'desc' => 'Session de mentorat (x2)'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'mohamed'  => [['source' => 'training_attended', 'pts' => 50, 'desc' => 'Formation Comptabilité de base'], ['source' => 'contribution', 'pts' => 20, 'desc' => 'Soirée Networking 2025'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'aisha'    => [['source' => 'training_attended', 'pts' => 50, 'desc' => 'Formation Comptabilité de base'], ['source' => 'contribution', 'pts' => 40, 'desc' => 'Événements (x2)'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'ibrahim'  => [['source' => 'contribution', 'pts' => 20, 'desc' => 'Soirée Networking 2025'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'khadija'  => [['source' => 'training_attended', 'pts' => 50, 'desc' => 'Formation Comptabilité de base'], ['source' => 'contribution', 'pts' => 20, 'desc' => 'Soirée Networking 2025'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'maryam'   => [['source' => 'training_attended', 'pts' => 50, 'desc' => 'Formation Comptabilité de base'], ['source' => 'contribution', 'pts' => 20, 'desc' => 'Soirée Networking 2025'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'abdallah' => [['source' => 'training_attended', 'pts' => 50, 'desc' => 'Formation Comptabilité de base'], ['source' => 'contribution', 'pts' => 20, 'desc' => 'Soirée Networking 2025'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'noura'    => [['source' => 'contribution', 'pts' => 40, 'desc' => 'Événements (x2)'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'hachim'   => [['source' => 'contribution', 'pts' => 20, 'desc' => 'Soirée Networking 2025'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
            'hamid'    => [['source' => 'training_attended', 'pts' => 50, 'desc' => 'Formation Comptabilité de base'], ['source' => 'contribution', 'pts' => 40, 'desc' => 'Événements (x2)'], ['source' => 'manual', 'pts' => 50, 'desc' => 'Profil complété']],
        ];

        foreach ($pointsData as $key => $entries) {
            if (! isset($members[$key])) {
                continue;
            }
            $user = $members[$key];
            foreach ($entries as $entry) {
                // On évite les doublons par source+user
                if (! PointEntry::where('user_id', $user->id)->where('description', $entry['desc'])->exists()) {
                    PointEntry::create([
                        'user_id'    => $user->id,
                        'source'     => $entry['source'],
                        'points'     => $entry['pts'],
                        'description'=> $entry['desc'],
                        'created_by' => $admin->id,
                        'created_at' => now()->subDays(rand(1, 60)),
                    ]);
                }
            }
        }
    }
}
