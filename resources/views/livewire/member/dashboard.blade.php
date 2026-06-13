<div class="p-4 lg:p-6 space-y-6">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Bonjour, {{ auth()->user()->first_name ?: auth()->user()->name }} 👋</h1>
            <p class="text-base-content/60 text-sm mt-0.5">
                {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </p>
        </div>
        @if($profile)
            <span class="badge badge-lg {{ $profile->statusColor() }} self-start sm:self-center">
                {{ $profile->statusLabel() }}
                @if($profile->membership_expires_at)
                    · expire le {{ $profile->membership_expires_at->format('d/m/Y') }}
                @endif
            </span>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat
            title="Mes points"
            value="{{ number_format($totalPoints) }}"
            icon="o-trophy"
            color="text-warning"
            description="Score d'implication"
        />
        <x-stat
            title="Formations"
            value="{{ $trainingsCount }}"
            icon="o-academic-cap"
            color="text-info"
            description="Sessions suivies"
        />
        <x-stat
            title="Affiliés"
            value="{{ $menteesCount }}"
            icon="o-users"
            color="text-success"
            description="Membres parrainés"
        />
        <x-stat
            title="Cotisation"
            value="{{ $profile?->isActive() ? 'À jour' : 'Attention' }}"
            icon="o-check-badge"
            color="{{ $profile?->isActive() ? 'text-success' : 'text-warning' }}"
            description="{{ $profile?->membership_expires_at ? 'Expire le '.$profile->membership_expires_at->format('d/m/Y') : '—' }}"
        />
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Annonces --}}
        <div class="lg:col-span-2 space-y-3">
            <h2 class="font-semibold text-lg flex items-center gap-2">
                <x-icon name="o-megaphone" class="w-5 h-5 text-primary" />
                Annonces du réseau
            </h2>
            @forelse($announcements as $a)
                <x-card class="border border-base-200 shadow-none">
                    <div class="flex items-start gap-3">
                        <x-icon name="o-bell" class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" />
                        <div>
                            <p class="font-semibold text-sm">{{ $a->title }}</p>
                            <div class="text-xs text-base-content/60 mt-0.5 line-clamp-2">
                                {!! strip_tags($a->content) !!}
                            </div>
                            <p class="text-xs text-base-content/40 mt-1">
                                {{ $a->published_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </x-card>
            @empty
                <x-card class="border border-base-200 shadow-none text-center py-8">
                    <x-icon name="o-inbox" class="w-10 h-10 text-base-content/30 mx-auto mb-2" />
                    <p class="text-base-content/50 text-sm">Aucune annonce pour le moment.</p>
                </x-card>
            @endforelse
        </div>

        {{-- Prochains événements --}}
        <div class="space-y-3">
            <h2 class="font-semibold text-lg flex items-center gap-2">
                <x-icon name="o-calendar-days" class="w-5 h-5 text-primary" />
                Prochains événements
            </h2>
            @forelse($upcomingEvents as $event)
                <x-card class="border border-base-200 shadow-none">
                    <p class="font-semibold text-sm">{{ $event->title }}</p>
                    <p class="text-xs text-base-content/60 mt-1">
                        <x-icon name="o-clock" class="w-3 h-3 inline" />
                        {{ $event->starts_at->isoFormat('D MMM à HH[h]mm') }}
                    </p>
                    @if($event->location)
                        <p class="text-xs text-base-content/60">
                            <x-icon name="o-map-pin" class="w-3 h-3 inline" />
                            {{ $event->location }}
                        </p>
                    @endif
                    <x-button
                        label="Voir"
                        :link="route('membre.events')"
                        class="btn-xs btn-ghost mt-2"
                        icon="o-arrow-right"
                        wire:navigate
                    />
                </x-card>
            @empty
                <p class="text-base-content/50 text-sm text-center py-4">Aucun événement à venir.</p>
            @endforelse

            <x-button
                label="Toutes les opportunités"
                :link="route('membre.opportunities')"
                class="btn-outline btn-primary btn-sm w-full"
                icon="o-briefcase"
                wire:navigate
            />
        </div>
    </div>

</div>
