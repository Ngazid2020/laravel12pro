<div class="p-4 lg:p-6 space-y-6">

    <h1 class="text-2xl font-bold">Mentorat</h1>

    {{-- ======================================================
         VUE MENTORÉ
    ====================================================== --}}
    <x-card shadow class="border border-base-200">
        <x-slot:title>
            <x-icon name="o-user-circle" class="w-5 h-5" /> Mes sessions (en tant que mentoré)
        </x-slot:title>

        @if(!$hasMentor)
            <x-alert icon="o-information-circle" class="alert-info">
                Vous n'avez pas encore de mentor assigné. Contactez l'administration pour être mis en relation.
            </x-alert>
        @else
            <div class="mb-3">
                <x-button
                    label="Demander une session"
                    icon="o-plus"
                    class="btn-primary btn-sm"
                    wire:click="$set('showRequest', true)"
                />
            </div>

            @forelse($menteeSessions as $session)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 rounded-lg bg-base-200 mb-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $statusColors[$session->status] ?? 'badge-ghost' }} badge-xs">
                                {{ $statusLabels[$session->status] ?? $session->status }}
                            </span>
                            <span class="text-sm font-medium">
                                {{ $session->scheduled_at->isoFormat('D MMM YYYY [à] HH[h]mm') }}
                            </span>
                        </div>
                        <p class="text-xs text-base-content/50 mt-0.5">
                            Mentor : {{ $session->mentor->name ?? '—' }}
                        </p>
                        @if($session->notes)
                            <p class="text-xs text-base-content/60 mt-1 italic">{{ $session->notes }}</p>
                        @endif
                    </div>

                    @if($session->status === 'confirmed' && !$session->confirmed_by_mentee)
                        <x-button
                            label="Confirmer la tenue"
                            icon="o-check"
                            class="btn-success btn-sm"
                            wire:click="confirmSession({{ $session->id }})"
                        />
                    @elseif($session->confirmed_by_mentee)
                        <span class="badge badge-success badge-xs gap-1">
                            <x-icon name="o-check" class="w-3 h-3" /> Confirmée
                        </span>
                    @endif
                </div>
            @empty
                <p class="text-sm text-base-content/50 italic">Aucune session pour le moment.</p>
            @endforelse
        @endif
    </x-card>

    {{-- ======================================================
         VUE MENTOR (visible uniquement si on a des mentorés)
    ====================================================== --}}
    @if($mentorSessions->isNotEmpty())
        <x-card shadow class="border border-base-200">
            <x-slot:title>
                <x-icon name="o-academic-cap" class="w-5 h-5" /> Sessions avec mes affiliés (en tant que mentor)
            </x-slot:title>

            @foreach($mentorSessions as $session)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 rounded-lg bg-base-200 mb-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $statusColors[$session->status] ?? 'badge-ghost' }} badge-xs">
                                {{ $statusLabels[$session->status] ?? $session->status }}
                            </span>
                            <span class="text-sm font-medium">
                                {{ $session->scheduled_at->isoFormat('D MMM YYYY [à] HH[h]mm') }}
                            </span>
                        </div>
                        <p class="text-xs text-base-content/50 mt-0.5">
                            Mentoré : {{ $session->mentee->name ?? '—' }}
                        </p>
                        @if($session->notes)
                            <p class="text-xs text-base-content/60 mt-1 italic">{{ $session->notes }}</p>
                        @endif
                    </div>

                    <x-button
                        :label="$session->status === 'confirmed' ? 'Modifier notes' : 'Valider & noter'"
                        icon="o-pencil-square"
                        class="btn-outline btn-sm"
                        wire:click="openNotes({{ $session->id }})"
                    />
                </div>
            @endforeach
        </x-card>
    @endif

    {{-- Modal demande de session --}}
    <x-modal wire:model="showRequest" title="Demander une session de mentorat" class="backdrop-blur">
        <x-form wire:submit="requestSession">
            <x-input
                label="Date et heure souhaitées"
                wire:model="scheduledAt"
                type="datetime-local"
                required
            />
            <x-textarea
                label="Notes / sujet de la session (optionnel)"
                wire:model="requestNotes"
                rows="3"
                placeholder="Points que vous souhaitez aborder…"
            />
            <x-slot:actions>
                <x-button label="Annuler" wire:click="$set('showRequest', false)" class="btn-ghost" />
                <x-button label="Envoyer la demande" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Modal notes mentor --}}
    <x-modal wire:model="showNotes" title="Notes de session" class="backdrop-blur">
        <x-form wire:submit="saveNotes">
            <x-textarea
                label="Notes de la session"
                wire:model="sessionNotes"
                rows="4"
                placeholder="Résumé de la session, actions de suivi…"
            />
            <x-slot:actions>
                <x-button label="Annuler" wire:click="$set('showNotes', false)" class="btn-ghost" />
                <x-button label="Enregistrer et confirmer" type="submit" class="btn-success" />
            </x-slot:actions>
        </x-form>
    </x-modal>

</div>
