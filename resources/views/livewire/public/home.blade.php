<div>

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-primary/10 via-base-100 to-secondary/10 py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <span class="badge badge-primary badge-lg mb-4">🇰🇲 Îles Comores</span>
            <h1 class="text-4xl sm:text-5xl font-black leading-tight">
                Le réseau des jeunes<br>
                <span class="text-primary">entrepreneurs comoriens</span>
            </h1>
            <p class="mt-5 text-lg text-base-content/70 max-w-2xl mx-auto">
                Formations, opportunités, mentorat et mise en relation — tout ce qu'il faut pour faire grandir votre activité aux Comores et au-delà.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="#postuler" class="btn btn-primary btn-lg">
                    <x-icon name="o-user-plus" class="w-5 h-5" />
                    Rejoindre le réseau
                </a>
                <a href="#comment" class="btn btn-ghost btn-lg">
                    Comment ça marche
                    <x-icon name="o-arrow-down" class="w-4 h-4" />
                </a>
            </div>
        </div>
    </section>

    {{-- =====================================================
         CHIFFRES CLÉS
    ====================================================== --}}
    <section class="bg-primary text-primary-content py-12 px-4">
        <div class="max-w-4xl mx-auto grid grid-cols-3 gap-6 text-center">
            <div>
                <p class="text-4xl font-black">200+</p>
                <p class="text-sm opacity-80 mt-1">Membres actifs</p>
            </div>
            <div>
                <p class="text-4xl font-black">50+</p>
                <p class="text-sm opacity-80 mt-1">Formations proposées</p>
            </div>
            <div>
                <p class="text-4xl font-black">30+</p>
                <p class="text-sm opacity-80 mt-1">Entreprises partenaires</p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         AVANTAGES
    ====================================================== --}}
    <section class="py-16 px-4 bg-base-100">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-10">Ce que vous obtenez en rejoignant</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['icon'=>'o-book-open',       'title'=>'Formations',       'desc'=>'Accédez à des formations pratiques animées par des experts locaux et internationaux.'],
                    ['icon'=>'o-briefcase',        'title'=>'Opportunités',     'desc'=>'Appels d\'offres, missions freelance, financements et concours en avant-première.'],
                    ['icon'=>'o-academic-cap',     'title'=>'Mentorat',         'desc'=>'Bénéficiez de l\'accompagnement personnalisé d\'un entrepreneur expérimenté.'],
                    ['icon'=>'o-users',            'title'=>'Réseau',           'desc'=>'Connectez-vous avec 200+ entrepreneurs actifs dans tous les secteurs.'],
                    ['icon'=>'o-calendar-days',    'title'=>'Événements',       'desc'=>'Networking, conférences et masterclasses tout au long de l\'année.'],
                    ['icon'=>'o-trophy',           'title'=>'Gamification',     'desc'=>'Progressez, débloquez des niveaux et accédez à des avantages exclusifs.'],
                ] as $item)
                    <div class="card bg-base-200 border border-base-300">
                        <div class="card-body">
                            <x-icon name="{{ $item['icon'] }}" class="w-8 h-8 text-primary mb-2" />
                            <h3 class="font-bold text-lg">{{ $item['title'] }}</h3>
                            <p class="text-sm text-base-content/70">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         COMMENT ÇA MARCHE
    ====================================================== --}}
    <section id="comment" class="py-16 px-4 bg-base-200">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-10">Comment rejoindre</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach([
                    ['step'=>'1', 'icon'=>'o-document-text', 'title'=>'Candidature', 'desc'=>'Remplissez le formulaire ci-dessous avec votre motivation.'],
                    ['step'=>'2', 'icon'=>'o-magnifying-glass','title'=>'Examen',    'desc'=>'Notre comité examine votre candidature sous 7 jours.'],
                    ['step'=>'3', 'icon'=>'o-check-badge',    'title'=>'Activation', 'desc'=>'Une fois accepté, vous recevez vos accès par email.'],
                ] as $step)
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center font-black text-xl mb-3">
                            {{ $step['step'] }}
                        </div>
                        <x-icon name="{{ $step['icon'] }}" class="w-7 h-7 text-primary mb-2" />
                        <h3 class="font-bold">{{ $step['title'] }}</h3>
                        <p class="text-sm text-base-content/70 mt-1">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         PARTENAIRES
    ====================================================== --}}
    @if($partners->isNotEmpty())
        <section class="py-14 px-4 bg-base-100">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl font-bold mb-8">Nos entreprises partenaires</h2>
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach($partners as $partner)
                        <div class="badge badge-outline badge-lg px-4 py-3 text-sm font-medium">
                            {{ $partner->name }}
                            @if($partner->sector)
                                <span class="text-base-content/50 ml-1">· {{ $partner->sector }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- =====================================================
         FORMULAIRE DE CANDIDATURE
    ====================================================== --}}
    <section id="postuler" class="py-16 px-4 bg-base-200">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold">Déposez votre candidature</h2>
                <p class="text-base-content/70 mt-2">Gratuit · Réponse sous 7 jours</p>
            </div>

            @if($submitted)
                <x-card shadow class="border border-success/30 bg-success/5 text-center py-10">
                    <x-icon name="o-check-circle" class="w-16 h-16 text-success mx-auto mb-4" />
                    <h3 class="text-xl font-bold text-success">Candidature envoyée !</h3>
                    <p class="text-base-content/70 mt-2">
                        Merci pour votre intérêt. Notre équipe examinera votre dossier et vous contactera par email dans les 7 jours.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('member.login') }}" class="btn btn-ghost btn-sm">
                            Déjà membre ? Se connecter
                        </a>
                    </div>
                </x-card>
            @else
                <x-card shadow class="border border-base-300">
                    <x-form wire:submit="apply">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-0">
                            <x-input
                                label="Prénom"
                                wire:model="firstName"
                                placeholder="Votre prénom"
                                required
                            />
                            <x-input
                                label="Nom"
                                wire:model="lastName"
                                placeholder="Votre nom"
                                required
                            />
                        </div>
                        <x-input
                            label="Email"
                            wire:model="email"
                            type="email"
                            placeholder="votre@email.com"
                            icon="o-envelope"
                            required
                        />
                        <x-input
                            label="Téléphone"
                            wire:model="phone"
                            placeholder="+269 XX XX XX XX"
                            icon="o-phone"
                            required
                        />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-0">
                            <x-input
                                label="Entreprise / Projet"
                                wire:model="company"
                                placeholder="Nom de votre entreprise"
                            />
                            <x-input
                                label="Secteur d'activité"
                                wire:model="sector"
                                placeholder="Ex: Technologie, Commerce…"
                            />
                        </div>
                        <x-textarea
                            label="Lettre de motivation"
                            wire:model="motivation"
                            placeholder="Présentez-vous, votre activité, et expliquez pourquoi vous souhaitez rejoindre le réseau (min. 50 caractères)…"
                            rows="5"
                            required
                            hint="Minimum 50 caractères"
                        />

                        <x-slot:actions>
                            <x-button
                                label="Envoyer ma candidature"
                                icon="o-paper-airplane"
                                type="submit"
                                class="btn-primary w-full"
                                spinner="apply"
                            />
                        </x-slot:actions>
                    </x-form>
                </x-card>
            @endif
        </div>
    </section>

</div>
