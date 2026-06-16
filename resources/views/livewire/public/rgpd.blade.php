<div class="max-w-3xl mx-auto px-4 py-12">

    {{-- En-tête --}}
    <div class="mb-10">
        <div class="flex items-center gap-2 text-sm text-base-content/50 mb-4">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Accueil</a>
            <span>/</span>
            <span>Politique de confidentialité</span>
        </div>
        <h1 class="text-3xl font-extrabold text-base-content mb-2">Politique de confidentialité</h1>
        <p class="text-base-content/60 text-sm">Dernière mise à jour : {{ date('d/m/Y') }}</p>
        <div class="divider my-4"></div>
        <p class="text-base-content/70 leading-relaxed">
            Le Réseau des Jeunes Entrepreneurs des Comores (<strong>ci-après « le Réseau »</strong>) s'engage à
            protéger la vie privée de ses membres et des personnes qui interagissent avec sa plateforme.
            La présente politique vous informe sur la manière dont vos données personnelles sont collectées,
            utilisées et protégées, conformément aux principes internationaux de protection des données personnelles.
        </p>
    </div>

    {{-- Sections --}}
    <div class="space-y-8 prose prose-sm max-w-none">

        {{-- 1. Responsable du traitement --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">1</span>
                Responsable du traitement
            </h2>
            <div class="card bg-base-200 mt-3">
                <div class="card-body py-4">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th class="text-base-content/60 w-40">Organisation</th>
                                <td>Réseau des Jeunes Entrepreneurs des Comores</td>
                            </tr>
                            <tr>
                                <th class="text-base-content/60">Adresse</th>
                                <td>Moroni, Union des Comores</td>
                            </tr>
                            <tr>
                                <th class="text-base-content/60">Email</th>
                                <td><a href="mailto:contact@reseau-entrepreneurs.km" class="link link-primary">contact@reseau-entrepreneurs.km</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- 2. Données collectées --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">2</span>
                Données personnelles collectées
            </h2>
            <p class="text-base-content/70 mt-3">Nous collectons les catégories de données suivantes :</p>

            <div class="overflow-x-auto mt-3">
                <table class="table table-sm border border-base-300 rounded-lg">
                    <thead class="bg-base-200">
                        <tr>
                            <th>Catégorie</th>
                            <th>Données</th>
                            <th>Obligatoire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-medium">Identité</td>
                            <td>Nom, prénom, photo de profil</td>
                            <td><span class="badge badge-error badge-xs">Oui</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">Contact</td>
                            <td>Adresse e-mail, numéro de téléphone</td>
                            <td><span class="badge badge-error badge-xs">Oui</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">Profil professionnel</td>
                            <td>Nom d'entreprise, secteur d'activité, ville, bio, compétences, besoins</td>
                            <td><span class="badge badge-ghost badge-xs">Partiel</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">Réseaux sociaux</td>
                            <td>Liens optionnels (LinkedIn, etc.)</td>
                            <td><span class="badge badge-success badge-xs">Non</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">Paiements</td>
                            <td>Moyen de paiement, référence de transaction, capture d'écran justificative</td>
                            <td><span class="badge badge-error badge-xs">Oui</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">Activité</td>
                            <td>Inscriptions aux événements et formations, évaluations, candidatures aux opportunités</td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td class="font-medium">Mentorat</td>
                            <td>Notes de sessions, demandes de mise en relation</td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td class="font-medium">Connexion</td>
                            <td>Adresse IP (logs de sécurité), tokens d'accès API</td>
                            <td>—</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 3. Finalités --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">3</span>
                Finalités du traitement
            </h2>
            <p class="text-base-content/70 mt-3">Vos données sont traitées pour les finalités suivantes :</p>
            <ul class="mt-3 space-y-2">
                @foreach([
                    ['Gestion des adhésions', 'Traitement de votre candidature, activation et suivi de votre compte membre.'],
                    ['Facturation et paiements', 'Enregistrement de vos cotisations, émission de reçus, validation par l\'administration.'],
                    ['Organisation d\'événements', 'Gestion des inscriptions, contrôle des présences via QR code.'],
                    ['Formations', 'Suivi de vos inscriptions, attestations de participation, système d\'évaluation.'],
                    ['Mise en réseau', 'Annuaire des membres actifs, demandes de contact, annuaire des opportunités.'],
                    ['Mentorat', 'Mise en relation avec les mentors, planification et suivi des sessions.'],
                    ['Gamification', 'Attribution de points et niveaux en fonction de votre engagement.'],
                    ['Communication', 'Envoi d\'e-mails transactionnels (validation de compte, paiement confirmé, rappels).'],
                    ['Sécurité', 'Protection contre les accès non autorisés, journaux d\'audit.'],
                ] as [$title, $desc])
                <li class="flex gap-3 items-start">
                    <x-icon name="o-check-circle" class="w-5 h-5 text-primary shrink-0 mt-0.5" />
                    <span><strong>{{ $title }} :</strong> {{ $desc }}</span>
                </li>
                @endforeach
            </ul>
        </section>

        {{-- 4. Base légale --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">4</span>
                Base légale du traitement
            </h2>
            <div class="grid sm:grid-cols-2 gap-3 mt-3">
                @foreach([
                    ['o-document-text', 'Exécution du contrat', 'Données nécessaires à la gestion de votre adhésion et à l\'accès aux services du Réseau.'],
                    ['o-hand-raised', 'Consentement', 'Photo de profil, liens sociaux, bio, compétences — vous pouvez les retirer à tout moment.'],
                    ['o-shield-check', 'Intérêt légitime', 'Sécurité de la plateforme, journaux d\'accès, prévention de la fraude.'],
                    ['o-scale', 'Obligation légale', 'Conservation des données de facturation conformément aux règles comptables en vigueur.'],
                ] as [$icon, $title, $desc])
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body py-4 px-5">
                        <div class="flex items-center gap-2 font-semibold mb-1">
                            <x-icon name="{{ $icon }}" class="w-5 h-5 text-primary" />
                            {{ $title }}
                        </div>
                        <p class="text-sm text-base-content/65">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- 5. Durée de conservation --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">5</span>
                Durée de conservation
            </h2>
            <div class="overflow-x-auto mt-3">
                <table class="table table-sm border border-base-300 rounded-lg">
                    <thead class="bg-base-200">
                        <tr>
                            <th>Type de données</th>
                            <th>Durée de conservation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Données de compte actif</td>
                            <td>Durée de l'adhésion + 3 ans après sa clôture</td>
                        </tr>
                        <tr>
                            <td>Données de facturation</td>
                            <td>10 ans (obligations comptables)</td>
                        </tr>
                        <tr>
                            <td>Candidature refusée</td>
                            <td>1 an à compter du refus</td>
                        </tr>
                        <tr>
                            <td>Logs de connexion</td>
                            <td>12 mois glissants</td>
                        </tr>
                        <tr>
                            <td>Tokens API</td>
                            <td>30 jours (révocables à tout moment)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 6. Partage des données --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">6</span>
                Partage et destinataires des données
            </h2>
            <p class="text-base-content/70 mt-3">
                Vos données ne sont <strong>jamais vendues</strong> à des tiers.
                Elles peuvent être partagées dans les cas suivants :
            </p>
            <ul class="mt-3 space-y-2">
                <li class="flex gap-3 items-start">
                    <x-icon name="o-users" class="w-5 h-5 text-primary shrink-0 mt-0.5" />
                    <span><strong>Annuaire membres :</strong> votre nom, entreprise, secteur et ville sont visibles par les autres membres actifs. Vous pouvez masquer ces informations depuis votre profil.</span>
                </li>
                <li class="flex gap-3 items-start">
                    <x-icon name="o-building-office" class="w-5 h-5 text-primary shrink-0 mt-0.5" />
                    <span><strong>Entreprises partenaires :</strong> en cas de candidature à une opportunité, votre nom et votre message de candidature sont transmis à l'entreprise concernée.</span>
                </li>
                <li class="flex gap-3 items-start">
                    <x-icon name="o-server" class="w-5 h-5 text-primary shrink-0 mt-0.5" />
                    <span><strong>Hébergeur :</strong> vos données sont stockées sur des serveurs sécurisés. Aucun transfert hors des Comores sans garanties adéquates.</span>
                </li>
            </ul>
        </section>

        {{-- 7. Sécurité --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">7</span>
                Sécurité des données
            </h2>
            <p class="text-base-content/70 mt-3">
                Nous mettons en œuvre des mesures techniques et organisationnelles adaptées :
            </p>
            <ul class="mt-3 space-y-1.5 text-base-content/70">
                <li class="flex gap-2 items-center"><x-icon name="o-lock-closed" class="w-4 h-4 text-success" /> Mots de passe chiffrés par hachage bcrypt</li>
                <li class="flex gap-2 items-center"><x-icon name="o-lock-closed" class="w-4 h-4 text-success" /> Communications chiffrées HTTPS (TLS)</li>
                <li class="flex gap-2 items-center"><x-icon name="o-lock-closed" class="w-4 h-4 text-success" /> Justificatifs de paiement stockés dans un espace privé non public</li>
                <li class="flex gap-2 items-center"><x-icon name="o-lock-closed" class="w-4 h-4 text-success" /> Accès à l'administration restreint aux comptes autorisés</li>
                <li class="flex gap-2 items-center"><x-icon name="o-lock-closed" class="w-4 h-4 text-success" /> Limitation du nombre de tentatives de connexion (anti-brute force)</li>
                <li class="flex gap-2 items-center"><x-icon name="o-lock-closed" class="w-4 h-4 text-success" /> En-têtes de sécurité HTTP (X-Frame-Options, X-Content-Type-Options…)</li>
            </ul>
        </section>

        {{-- 8. Cookies --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">8</span>
                Cookies et traceurs
            </h2>
            <p class="text-base-content/70 mt-3">
                La plateforme utilise <strong>uniquement des cookies de session</strong> nécessaires au fonctionnement
                (authentification, protection CSRF). Aucun cookie publicitaire, aucun traceur tiers
                (Google Analytics, Meta Pixel, etc.) n'est utilisé.
            </p>
        </section>

        {{-- 9. Droits --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">9</span>
                Vos droits
            </h2>
            <p class="text-base-content/70 mt-3">Vous disposez des droits suivants sur vos données personnelles :</p>
            <div class="grid sm:grid-cols-2 gap-3 mt-3">
                @foreach([
                    ['Droit d\'accès', 'Obtenir une copie de toutes les données que nous détenons sur vous.'],
                    ['Droit de rectification', 'Corriger des informations inexactes ou incomplètes directement depuis votre profil.'],
                    ['Droit à l\'effacement', 'Demander la suppression de votre compte et de vos données (sous réserve des obligations légales de conservation).'],
                    ['Droit à la portabilité', 'Recevoir vos données dans un format structuré et lisible par machine.'],
                    ['Droit d\'opposition', 'Vous opposer à certains traitements, notamment à l\'affichage de votre profil dans l\'annuaire.'],
                    ['Droit de retrait du consentement', 'Retirer à tout moment votre consentement pour les traitements basés sur celui-ci.'],
                ] as [$title, $desc])
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body py-3 px-4">
                        <div class="font-semibold text-sm mb-1">{{ $title }}</div>
                        <p class="text-xs text-base-content/60">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="alert alert-info mt-4 text-sm">
                <x-icon name="o-information-circle" class="w-5 h-5 shrink-0" />
                <span>
                    Pour exercer vos droits, contactez-nous à
                    <a href="mailto:contact@reseau-entrepreneurs.km" class="link font-medium">contact@reseau-entrepreneurs.km</a>.
                    Nous répondons dans un délai maximum de <strong>30 jours</strong>.
                </span>
            </div>
        </section>

        {{-- 10. Modifications --}}
        <section>
            <h2 class="text-xl font-bold text-base-content flex items-center gap-2">
                <span class="badge badge-primary badge-sm">10</span>
                Modifications de la présente politique
            </h2>
            <p class="text-base-content/70 mt-3">
                Nous nous réservons le droit de mettre à jour cette politique. En cas de modification
                substantielle, les membres actifs seront informés par e-mail au moins 15 jours avant
                l'entrée en vigueur des changements. La date de la dernière mise à jour figure en haut
                de cette page.
            </p>
        </section>

    </div>

    {{-- Retour accueil --}}
    <div class="mt-12 pt-6 border-t border-base-300 flex justify-between items-center">
        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm gap-2">
            <x-icon name="o-arrow-left" class="w-4 h-4" />
            Retour à l'accueil
        </a>
        <a href="mailto:contact@reseau-entrepreneurs.km" class="btn btn-outline btn-sm">
            Nous contacter
        </a>
    </div>

</div>
