<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Peuple la base avec des données de démonstration réalistes.
 *
 * Identifiants membres : <prenom>.<nom>@reseau.km / Demo@2026!
 * Admin                 : admin@reseau.km          / Admin@2026!
 * Membre test           : hamid@reseau.km           / Membre@2026!
 *
 * Usage :
 *   php artisan db:seed --class=DemoSeeder
 *   php artisan migrate:fresh --seed   (avec DatabaseSeeder)
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DemoMembersSeeder::class,   // Plans, niveaux, 12 membres + profils + paiements
            DemoContentSeeder::class,   // Niveaux, candidature, partenaires, événements, formations, opportunités, annonces
            DemoActivitySeeder::class,  // Inscriptions, enrollments, mentorat, contacts, points
        ]);
    }
}
