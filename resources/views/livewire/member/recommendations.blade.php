<div class="p-4 lg:p-6 space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-2xl font-bold">Mes Recommandations</h1>
        <x-button
            label="Nouvelle demande"
            icon="o-plus"
            class="btn-primary"
            wire:click="$set('showForm', true)"
        />
    </div>

    @forelse($recommendations as $rec)
        <x-card shadow class="border border-base-200">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="badge {{ $statusColors[$rec->status] ?? 'badge-ghost' }} badge-sm">
                            {{ $statusLabels[$rec->status] ?? $rec->status }}
                        </span>
                        @if($rec->partnerCompany)
                            <span class="badge badge-outline badge-sm">
                                <x-icon name="o-building-office" class="w-3 h-3" />
                                {{ $rec->partnerCompany->name }}
                            </span>
                        @elseif($rec->targetUser)
                            <span class="badge badge-outline badge-sm">
                                <x-icon name="o-user" class="w-3 h-3" />
                                {{ $rec->targetUser->name }}
                            </span>
                        @endif
                    </div>

                    <p class="mt-2 text-sm text-base-content/80">{{ $rec->need_description }}</p>

                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-base-content/50">
                        <span>Soumise le {{ $rec->created_at->format('d/m/Y') }}</span>
                        @if($rec->examiner)
                            <span>Examinée par {{ $rec->examiner->name }}</span>
                        @endif
                        @if($rec->transmitted_at)
                            <span>Transmise le {{ $rec->transmitted_at->format('d/m/Y') }}</span>
                        @endif
                    </div>

                    @if($rec->outcome_notes)
                        <div class="mt-2 p-2 bg-base-200 rounded text-sm">
                            <p class="text-xs font-semibold text-base-content/50 mb-1">Résultat</p>
                            {{ $rec->outcome_notes }}
                            @if($rec->estimated_value)
                                <span class="badge badge-success badge-sm ml-2">
                                    {{ number_format($rec->estimated_value) }} KMF
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </x-card>
    @empty
        <div class="text-center py-16">
            <x-icon name="o-arrow-path-rounded-square" class="w-14 h-14 text-base-content/20 mx-auto mb-3" />
            <p class="text-base-content/50">Aucune demande de recommandation pour le moment.</p>
        </div>
    @endforelse

    {{-- Modal nouvelle demande --}}
    <x-modal wire:model="showForm" title="Nouvelle demande de recommandation" class="backdrop-blur">
        <x-form wire:submit="submit">

            <div>
                <p class="label-text mb-2">Vers qui ?</p>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="targetType" value="company" class="radio radio-primary radio-sm" />
                        <span class="text-sm">Entreprise partenaire</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="targetType" value="member" class="radio radio-primary radio-sm" />
                        <span class="text-sm">Membre du réseau</span>
                    </label>
                </div>
            </div>

            @if($targetType === 'company')
                <x-select
                    label="Entreprise partenaire"
                    wire:model="partnerCompanyId"
                    :options="$companies->map(fn($c) => ['id'=>$c->id,'name'=>$c->name])->toArray()"
                    option-value="id"
                    option-label="name"
                    placeholder="Sélectionner une entreprise"
                    required
                />
            @else
                <x-select
                    label="Membre"
                    wire:model="targetUserId"
                    :options="$members->map(fn($m) => ['id'=>$m->id,'name'=>$m->name])->toArray()"
                    option-value="id"
                    option-label="name"
                    placeholder="Sélectionner un membre"
                    required
                />
            @endif

            <x-textarea
                label="Description du besoin"
                wire:model="needDescription"
                placeholder="Décrivez précisément votre besoin ou ce que vous recherchez (min. 20 caractères)…"
                rows="4"
                required
            />

            <x-slot:actions>
                <x-button label="Annuler" wire:click="$set('showForm', false)" class="btn-ghost" />
                <x-button label="Envoyer" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-modal>

</div>
