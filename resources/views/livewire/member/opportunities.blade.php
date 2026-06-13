<div class="p-4 lg:p-6 space-y-5">

    <h1 class="text-2xl font-bold">Opportunités</h1>

    {{-- Filtres --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <x-input
            wire:model.live.debounce.400ms="search"
            placeholder="Rechercher…"
            icon="o-magnifying-glass"
            class="flex-1"
            clearable
        />
        <x-select
            wire:model.live="type"
            placeholder="Tous les types"
            :options="collect($typeLabels)->map(fn($l,$k) => ['id'=>$k,'name'=>$l])->values()->toArray()"
            option-value="id"
            option-label="name"
            class="sm:w-48"
        />
        <x-input
            wire:model.live.debounce.400ms="sector"
            placeholder="Secteur…"
            icon="o-tag"
            class="sm:w-40"
            clearable
        />
    </div>

    {{-- Liste --}}
    @forelse($opportunities as $opp)
        <x-card shadow class="border border-base-200">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="badge badge-primary badge-sm">{{ $typeLabels[$opp->type] ?? $opp->type }}</span>
                        @if($opp->sector)
                            <span class="badge badge-ghost badge-sm">{{ $opp->sector }}</span>
                        @endif
                        @if($opp->deadline)
                            <span class="badge badge-outline badge-sm {{ $opp->deadline->isPast() ? 'badge-error' : '' }}">
                                Limite : {{ $opp->deadline->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-lg mt-2">{{ $opp->title }}</h3>
                    @if($opp->partnerCompany)
                        <p class="text-sm text-base-content/60">
                            <x-icon name="o-building-office" class="w-4 h-4 inline" />
                            {{ $opp->partnerCompany->name }}
                        </p>
                    @endif
                    <div class="text-sm text-base-content/70 mt-2 line-clamp-3">
                        {!! strip_tags($opp->description) !!}
                    </div>
                    @if(!empty($opp->target_skills))
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($opp->target_skills as $skill)
                                <span class="badge badge-xs badge-outline">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="sm:text-right flex-shrink-0">
                    @if(in_array($opp->id, $myApplications))
                        <span class="badge badge-success gap-1">
                            <x-icon name="o-check" class="w-3 h-3" /> Candidature envoyée
                        </span>
                    @else
                        <x-button
                            label="Postuler"
                            icon="o-paper-airplane"
                            class="btn-primary btn-sm"
                            wire:click="apply({{ $opp->id }})"
                            wire:loading.attr="disabled"
                        />
                    @endif
                </div>
            </div>
        </x-card>
    @empty
        <div class="text-center py-16">
            <x-icon name="o-briefcase" class="w-14 h-14 text-base-content/20 mx-auto mb-3" />
            <p class="text-base-content/50">Aucune opportunité disponible pour le moment.</p>
        </div>
    @endforelse

    {{ $opportunities->links() }}

</div>
