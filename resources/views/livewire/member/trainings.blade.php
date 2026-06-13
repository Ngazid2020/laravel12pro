<div class="p-4 lg:p-6 space-y-5">

    <h1 class="text-2xl font-bold">Formations</h1>

    {{-- Onglets --}}
    <div class="tabs tabs-boxed w-fit">
        <button
            class="tab {{ $tab === 'catalog' ? 'tab-active' : '' }}"
            wire:click="$set('tab', 'catalog')"
        >Catalogue</button>
        <button
            class="tab {{ $tab === 'my_trainings' ? 'tab-active' : '' }}"
            wire:click="$set('tab', 'my_trainings')"
        >
            Mes formations
            @if($upcoming->count())
                <span class="badge badge-primary badge-sm ml-1">{{ $upcoming->count() }}</span>
            @endif
        </button>
    </div>

    {{-- ============================================================
         ONGLET CATALOGUE
    ============================================================= --}}
    @if($tab === 'catalog')

        {{-- Filtres --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <x-input
                wire:model.live.debounce.400ms="search"
                placeholder="Rechercher une formation…"
                icon="o-magnifying-glass"
                class="flex-1"
                clearable
            />
            <x-select
                wire:model.live="format"
                placeholder="Tous les formats"
                :options="[
                    ['id'=>'in_person','name'=>'Présentiel'],
                    ['id'=>'online',   'name'=>'En ligne'],
                    ['id'=>'hybrid',   'name'=>'Hybride'],
                ]"
                option-value="id"
                option-label="name"
                class="sm:w-44"
            />
            <x-select
                wire:model.live="priceType"
                placeholder="Tous les tarifs"
                :options="[
                    ['id'=>'free',    'name'=>'Gratuite'],
                    ['id'=>'included','name'=>'Incluse'],
                    ['id'=>'premium', 'name'=>'Premium'],
                ]"
                option-value="id"
                option-label="name"
                class="sm:w-44"
            />
        </div>

        @forelse($trainings as $training)
            <x-card shadow class="border border-base-200">
                {{-- En-tête formation --}}
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="badge badge-primary badge-sm">
                                {{ $formatLabels[$training->format] ?? $training->format }}
                            </span>
                            @if($training->price_type === 'premium')
                                <span class="badge badge-warning badge-sm">
                                    Premium · {{ number_format($training->price) }} KMF
                                </span>
                            @elseif($training->price_type === 'free')
                                <span class="badge badge-success badge-sm">Gratuite</span>
                            @else
                                <span class="badge badge-info badge-sm">Incluse dans l'adhésion</span>
                            @endif
                        </div>
                        <h3 class="font-semibold text-lg mt-1">{{ $training->title }}</h3>
                        <p class="text-sm text-base-content/60">
                            <x-icon name="o-academic-cap" class="w-4 h-4 inline" />
                            {{ $training->trainer->name ?? '—' }}
                        </p>
                        @if($training->description)
                            <p class="text-sm text-base-content/70 mt-2 line-clamp-2">
                                {{ strip_tags($training->description) }}
                            </p>
                        @endif
                        @if($training->prerequisites)
                            <p class="text-xs text-base-content/50 mt-1">
                                <x-icon name="o-information-circle" class="w-3 h-3 inline" />
                                Prérequis : {{ $training->prerequisites }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Sessions à venir --}}
                @if($training->sessions->isNotEmpty())
                    <div class="mt-4 space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-base-content/50">
                            Sessions à venir
                        </p>
                        @foreach($training->sessions as $session)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 bg-base-200 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-3 text-sm">
                                        <span>
                                            <x-icon name="o-calendar" class="w-4 h-4 inline text-primary" />
                                            {{ $session->starts_at->isoFormat('D MMM YYYY [à] HH[h]mm') }}
                                        </span>
                                        @if($session->location)
                                            <span class="text-base-content/60">
                                                <x-icon name="o-map-pin" class="w-4 h-4 inline" />
                                                {{ $session->location }}
                                            </span>
                                        @endif
                                        @if($session->meeting_link)
                                            <a href="{{ $session->meeting_link }}" target="_blank" class="link link-primary text-xs">
                                                <x-icon name="o-video-camera" class="w-4 h-4 inline" /> Lien visio
                                            </a>
                                        @endif
                                    </div>
                                    @if($training->capacity)
                                        @php $spots = max(0, $training->capacity - $session->enrollments_count); @endphp
                                        <p class="text-xs text-base-content/50 mt-0.5">
                                            {{ $spots > 0 ? $spots.' place(s) restante(s)' : 'Complet' }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex-shrink-0">
                                    @if(in_array($session->id, $mySessionIds))
                                        <span class="badge badge-success gap-1">
                                            <x-icon name="o-check" class="w-3 h-3" /> Inscrit
                                        </span>
                                    @elseif($training->price_type === 'premium')
                                        <span class="badge badge-warning badge-sm" title="Contactez l'administration">
                                            <x-icon name="o-lock-closed" class="w-3 h-3" /> Premium
                                        </span>
                                    @elseif($session->enrollments_count >= ($training->capacity ?? PHP_INT_MAX))
                                        <span class="badge badge-error badge-sm">Complet</span>
                                    @else
                                        <x-button
                                            label="S'inscrire"
                                            icon="o-plus"
                                            class="btn-primary btn-sm"
                                            wire:click="enroll({{ $session->id }})"
                                        />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-base-content/40 mt-3 italic">
                        Aucune session programmée pour le moment.
                    </p>
                @endif
            </x-card>
        @empty
            <div class="text-center py-16">
                <x-icon name="o-book-open" class="w-14 h-14 text-base-content/20 mx-auto mb-3" />
                <p class="text-base-content/50">Aucune formation disponible pour le moment.</p>
            </div>
        @endforelse

        {{ $trainings->links() }}

    @endif

    {{-- ============================================================
         ONGLET MES FORMATIONS
    ============================================================= --}}
    @if($tab === 'my_trainings')

        {{-- À venir --}}
        <div class="space-y-3">
            <h2 class="font-semibold text-base-content/70">À venir ({{ $upcoming->count() }})</h2>

            @forelse($upcoming as $enrollment)
                @php $session = $enrollment->trainingSession; $training = $session->training; @endphp
                <x-card shadow class="border border-base-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-semibold">{{ $training->title }}</h3>
                            <div class="flex flex-wrap gap-3 text-sm text-base-content/60 mt-1">
                                <span>
                                    <x-icon name="o-calendar" class="w-4 h-4 inline" />
                                    {{ $session->starts_at->isoFormat('D MMM YYYY [à] HH[h]mm') }}
                                </span>
                                @if($session->location)
                                    <span>
                                        <x-icon name="o-map-pin" class="w-4 h-4 inline" />
                                        {{ $session->location }}
                                    </span>
                                @endif
                                @if($session->meeting_link)
                                    <a href="{{ $session->meeting_link }}" target="_blank" class="link link-primary">
                                        <x-icon name="o-video-camera" class="w-4 h-4 inline" /> Rejoindre
                                    </a>
                                @endif
                            </div>
                            {{-- Matériaux --}}
                            @if(!empty($session->materials))
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($session->materials as $mat)
                                        <a href="{{ $mat['url'] }}" target="_blank"
                                           class="badge badge-outline badge-xs gap-1 hover:badge-primary">
                                            <x-icon name="{{ $mat['type'] === 'video' ? 'o-play-circle' : 'o-document-text' }}" class="w-3 h-3" />
                                            {{ $mat['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <x-button
                            label="Annuler"
                            icon="o-x-mark"
                            class="btn-ghost btn-sm text-error"
                            wire:click="unenroll({{ $session->id }})"
                            wire:confirm="Annuler votre inscription à cette session ?"
                        />
                    </div>
                </x-card>
            @empty
                <p class="text-base-content/50 italic text-sm">Aucune session à venir.</p>
            @endforelse
        </div>

        {{-- Passées --}}
        <div class="space-y-3 mt-6">
            <h2 class="font-semibold text-base-content/70">Passées ({{ $past->count() }})</h2>

            @forelse($past as $enrollment)
                @php $session = $enrollment->trainingSession; $training = $session->training; @endphp
                <x-card shadow class="border border-base-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold">{{ $training->title }}</h3>
                                <span class="badge badge-xs {{ $enrollment->status === 'attended' ? 'badge-success' : 'badge-ghost' }}">
                                    {{ $statusLabels[$enrollment->status] ?? $enrollment->status }}
                                </span>
                            </div>
                            <p class="text-sm text-base-content/60 mt-0.5">
                                <x-icon name="o-calendar" class="w-4 h-4 inline" />
                                {{ $session->starts_at->isoFormat('D MMM YYYY') }}
                            </p>
                            @if($enrollment->rating)
                                <div class="flex items-center gap-1 mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-icon
                                            name="{{ $i <= $enrollment->rating ? 's-star' : 'o-star' }}"
                                            class="w-4 h-4 {{ $i <= $enrollment->rating ? 'text-warning' : 'text-base-content/20' }}"
                                        />
                                    @endfor
                                    @if($enrollment->comment)
                                        <span class="text-xs text-base-content/50 ml-1 italic">« {{ Str::limit($enrollment->comment, 60) }} »</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($enrollment->status === 'attended' && !$enrollment->rating)
                            <x-button
                                label="Évaluer"
                                icon="o-star"
                                class="btn-outline btn-sm btn-warning"
                                wire:click="openRating({{ $enrollment->id }})"
                            />
                        @endif
                    </div>
                </x-card>
            @empty
                <p class="text-base-content/50 italic text-sm">Aucune formation passée.</p>
            @endforelse
        </div>

    @endif

    {{-- Modal notation --}}
    <x-modal wire:model="showRating" title="Évaluer la formation" class="backdrop-blur">
        <x-form wire:submit="submitRating">
            <div>
                <p class="label-text mb-2">Note</p>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button
                            type="button"
                            wire:click="$set('rating', {{ $i }})"
                            class="btn btn-ghost btn-sm p-1"
                        >
                            <x-icon
                                name="{{ $i <= $rating ? 's-star' : 'o-star' }}"
                                class="w-7 h-7 {{ $i <= $rating ? 'text-warning' : 'text-base-content/30' }}"
                            />
                        </button>
                    @endfor
                </div>
            </div>

            <x-textarea
                label="Commentaire (optionnel)"
                wire:model="ratingComment"
                placeholder="Votre retour sur cette formation…"
                rows="3"
            />

            <x-slot:actions>
                <x-button label="Annuler" wire:click="$set('showRating', false)" class="btn-ghost" />
                <x-button label="Envoyer" type="submit" class="btn-warning" />
            </x-slot:actions>
        </x-form>
    </x-modal>

</div>
