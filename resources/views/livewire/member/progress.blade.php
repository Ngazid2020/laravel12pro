<div class="p-4 lg:p-6 space-y-6">

    <h1 class="text-2xl font-bold">Ma Progression</h1>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <x-stat
            title="Points totaux"
            :value="number_format($totalPoints)"
            icon="o-star"
            color="text-warning"
        />
        <x-stat
            title="Formations suivies"
            :value="$trainingsAttended"
            icon="o-book-open"
            color="text-primary"
        />
        <x-stat
            title="Mois de membre"
            :value="$monthsActive"
            icon="o-calendar"
            color="text-secondary"
            class="col-span-2 sm:col-span-1"
        />
    </div>

    {{-- Niveau actuel + progression --}}
    <x-card shadow class="border border-base-200">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0">
                @if($currentLevel)
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-black"
                         style="background-color: {{ $currentLevel->badge_color ?? '#6366f1' }}20; color: {{ $currentLevel->badge_color ?? '#6366f1' }}">
                        {{ substr($currentLevel->name, 0, 1) }}
                    </div>
                @else
                    <div class="w-16 h-16 rounded-full bg-base-200 flex items-center justify-center">
                        <x-icon name="o-trophy" class="w-8 h-8 text-base-content/30" />
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <p class="font-semibold text-lg">
                    {{ $currentLevel?->name ?? 'Aucun niveau' }}
                </p>
                @if($currentLevel?->description)
                    <p class="text-sm text-base-content/60">{{ $currentLevel->description }}</p>
                @endif
                @if($nextLevel)
                    <div class="mt-2">
                        <div class="flex justify-between text-xs text-base-content/50 mb-1">
                            <span>Vers : <strong>{{ $nextLevel->name }}</strong></span>
                            <span>{{ $progressPercent }}%</span>
                        </div>
                        <progress class="progress progress-primary w-full" value="{{ $progressPercent }}" max="100"></progress>
                        <p class="text-xs text-base-content/40 mt-1">
                            {{ number_format($totalPoints) }} / {{ number_format($nextLevel->min_points) }} pts
                            @if($nextLevel->required_trainings > $trainingsAttended)
                                · {{ $trainingsAttended }}/{{ $nextLevel->required_trainings }} formations
                            @endif
                            @if($nextLevel->required_months > $monthsActive)
                                · {{ $monthsActive }}/{{ $nextLevel->required_months }} mois
                            @endif
                        </p>
                    </div>
                @else
                    <p class="text-sm text-success mt-1 font-medium">Niveau maximum atteint !</p>
                @endif
            </div>
        </div>
    </x-card>

    {{-- Tous les niveaux --}}
    <x-card shadow class="border border-base-200">
        <x-slot:title>
            <x-icon name="o-trophy" class="w-5 h-5" /> Parcours de niveaux
        </x-slot:title>

        <div class="space-y-3">
            @forelse($levels as $level)
                @php $unlocked = $level->isUnlockedBy(auth()->user()); @endphp
                <div class="flex items-start gap-3 p-3 rounded-lg {{ $unlocked ? 'bg-success/10 border border-success/20' : 'bg-base-200' }}">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm"
                         style="{{ $unlocked ? 'background-color:'.($level->badge_color ?? '#6366f1').'20;color:'.($level->badge_color ?? '#6366f1') : '' }}">
                        @if($unlocked)
                            <x-icon name="o-check-badge" class="w-6 h-6" style="color:{{ $level->badge_color ?? '#6366f1' }}" />
                        @else
                            <x-icon name="o-lock-closed" class="w-5 h-5 text-base-content/30" />
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold">{{ $level->name }}</p>
                            @if($unlocked)
                                <span class="badge badge-success badge-xs">Débloqué</span>
                            @endif
                            @if($level->grants_mentor_status)
                                <span class="badge badge-secondary badge-xs">Statut mentor</span>
                            @endif
                        </div>
                        <p class="text-xs text-base-content/50 mt-0.5">
                            {{ number_format($level->min_points) }} pts
                            · {{ $level->required_trainings }} formations
                            · {{ $level->required_months }} mois
                        </p>
                        @if($level->rewards->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($level->rewards as $reward)
                                    <span class="badge badge-outline badge-xs">{{ $reward->description }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-base-content/50 italic">Aucun niveau configuré pour le moment.</p>
            @endforelse
        </div>
    </x-card>

    {{-- Historique des points --}}
    <x-card shadow class="border border-base-200">
        <x-slot:title>
            <x-icon name="o-queue-list" class="w-5 h-5" /> Historique des points
        </x-slot:title>

        @forelse($pointHistory as $entry)
            <div class="flex items-center justify-between py-2 border-b border-base-200 last:border-0">
                <div>
                    <p class="text-sm font-medium">{{ $sourceLabels[$entry->source] ?? $entry->source }}</p>
                    @if($entry->description)
                        <p class="text-xs text-base-content/50">{{ $entry->description }}</p>
                    @endif
                    <p class="text-xs text-base-content/40">{{ $entry->created_at->format('d/m/Y') }}</p>
                </div>
                <span class="font-bold {{ $entry->points >= 0 ? 'text-success' : 'text-error' }}">
                    {{ $entry->points >= 0 ? '+' : '' }}{{ $entry->points }} pts
                </span>
            </div>
        @empty
            <p class="text-sm text-base-content/50 italic">Aucun point enregistré.</p>
        @endforelse

        {{ $pointHistory->links() }}
    </x-card>

</div>
