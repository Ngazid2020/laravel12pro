<div class="p-4 lg:p-6 space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-2xl font-bold">Annuaire des membres</h1>
        <span class="badge badge-neutral">{{ $members->total() }} membre(s)</span>
    </div>

    {{-- Filtres --}}
    <x-card shadow class="border border-base-200">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <x-input
                wire:model.live.debounce.400ms="search"
                placeholder="Rechercher un nom…"
                icon="o-magnifying-glass"
                clearable
            />
            <x-select
                wire:model.live="sector"
                placeholder="Tous les secteurs"
                :options="$sectors->map(fn($s) => ['id' => $s, 'name' => $s])->toArray()"
                option-value="id"
                option-label="name"
            />
            <x-input
                wire:model.live.debounce.400ms="city"
                placeholder="Ville…"
                icon="o-map-pin"
                clearable
            />
            <x-input
                wire:model.live.debounce.400ms="skill"
                placeholder="Compétence…"
                icon="o-wrench-screwdriver"
                clearable
            />
        </div>
    </x-card>

    {{-- Grille membres --}}
    @if($members->isEmpty())
        <div class="text-center py-16">
            <x-icon name="o-user-group" class="w-14 h-14 text-base-content/20 mx-auto mb-3" />
            <p class="text-base-content/50">Aucun membre trouvé.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($members as $profile)
                <x-card shadow class="border border-base-200 hover:border-primary/40 transition-colors">
                    <div class="flex items-start gap-3">
                        <x-avatar
                            :placeholder="substr($profile->user->name, 0, 1)"
                            class="!w-12 !h-12 bg-primary text-primary-content flex-shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate">{{ $profile->user->name }}</p>
                            @if($profile->company_name)
                                <p class="text-xs text-base-content/60 truncate">
                                    <x-icon name="o-building-office" class="w-3 h-3 inline" />
                                    {{ $profile->company_name }}
                                </p>
                            @endif
                            @if($profile->sector)
                                <span class="badge badge-xs badge-ghost mt-1">{{ $profile->sector }}</span>
                            @endif
                            @if($profile->city)
                                <p class="text-xs text-base-content/50 mt-0.5">
                                    <x-icon name="o-map-pin" class="w-3 h-3 inline" />
                                    {{ $profile->city }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($profile->bio)
                        <p class="text-xs text-base-content/60 mt-3 line-clamp-2">{{ $profile->bio }}</p>
                    @endif

                    @if(!empty($profile->skills_offered))
                        <div class="flex flex-wrap gap-1 mt-3">
                            @foreach(array_slice($profile->skills_offered, 0, 3) as $skill)
                                <span class="badge badge-xs badge-primary badge-outline">{{ $skill }}</span>
                            @endforeach
                            @if(count($profile->skills_offered) > 3)
                                <span class="badge badge-xs badge-ghost">+{{ count($profile->skills_offered) - 3 }}</span>
                            @endif
                        </div>
                    @endif
                </x-card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $members->links() }}
        </div>
    @endif

</div>
