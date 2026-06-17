<div class="p-4 lg:p-6 space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-2xl font-bold">Mise en relation</h1>
        <x-button
            label="Nouvelle demande"
            icon="o-user-plus"
            class="btn-primary"
            wire:click="$set('showForm', true)"
        />
    </div>

    {{-- Demandes reçues --}}
    <div class="space-y-3">
        <h2 class="font-semibold text-base-content/70 flex items-center gap-2">
            <x-icon name="o-inbox" class="w-5 h-5" />
            Reçues
            @php $pending = $received->where('status', 'pending')->count(); @endphp
            @if($pending)
                <span class="badge badge-error badge-sm">{{ $pending }}</span>
            @endif
        </h2>

        @forelse($received as $req)
            <x-card shadow class="border border-base-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <x-avatar
                            :placeholder="substr($req->sender->name, 0, 1)"
                            class="!w-10 !h-10 bg-primary text-primary-content"
                        />
                        <div>
                            <p class="font-medium">{{ $req->sender->name }}</p>
                            @if($req->sender->profile?->company_name)
                                <p class="text-xs text-base-content/50">{{ $req->sender->profile->company_name }}</p>
                            @endif
                            @if($req->message)
                                <p class="text-sm text-base-content/70 mt-1 italic">« {{ $req->message }} »</p>
                            @endif
                            <p class="text-xs text-base-content/40 mt-0.5">
                                {{ $req->created_at->diffForHumans() }}
                                <span class="badge {{ $statusColors[$req->status] ?? 'badge-ghost' }} badge-xs ml-1">
                                    {{ $statusLabels[$req->status] ?? $req->status }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($req->status === 'pending')
                        <div class="flex gap-2">
                            <x-button
                                label="Accepter"
                                icon="o-check"
                                class="btn-success btn-sm"
                                wire:click="accept({{ $req->id }})"
                            />
                            <x-button
                                label="Refuser"
                                icon="o-x-mark"
                                class="btn-ghost btn-sm text-error"
                                wire:click="decline({{ $req->id }})"
                            />
                        </div>
                    @endif
                </div>
            </x-card>
        @empty
            <p class="text-sm text-base-content/50 italic">Aucune demande reçue.</p>
        @endforelse
    </div>

    {{-- Demandes envoyées --}}
    <div class="space-y-3">
        <h2 class="font-semibold text-base-content/70 flex items-center gap-2">
            <x-icon name="o-paper-airplane" class="w-5 h-5" />
            Envoyées
        </h2>

        @forelse($sent as $req)
            <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <x-avatar
                        :placeholder="substr($req->receiver->name, 0, 1)"
                        class="!w-9 !h-9 bg-secondary text-secondary-content"
                    />
                    <div>
                        <p class="text-sm font-medium">{{ $req->receiver->name }}</p>
                        @if($req->receiver->profile?->company_name)
                            <p class="text-xs text-base-content/50">{{ $req->receiver->profile->company_name }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge {{ $statusColors[$req->status] ?? 'badge-ghost' }} badge-sm">
                        {{ $statusLabels[$req->status] ?? $req->status }}
                    </span>
                    @if($req->status === 'pending')
                        <x-button
                            icon="o-x-mark"
                            class="btn-ghost btn-xs text-error"
                            title="Annuler la demande"
                            wire:click="cancel({{ $req->id }})"
                            wire:confirm="Annuler cette demande de mise en relation ?"
                        />
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-base-content/50 italic">Aucune demande envoyée.</p>
        @endforelse
    </div>

    {{-- Modal nouvelle demande --}}
    <x-modal wire:model="showForm" title="Demande de mise en relation" class="backdrop-blur">
        <x-form wire:submit="send">
            <x-select
                label="Membre"
                wire:model="receiverId"
                :options="$members->map(fn($m) => ['id'=>$m->id,'name'=>$m->name])->toArray()"
                option-value="id"
                option-label="name"
                placeholder="Choisir un membre…"
                required
            />
            <x-textarea
                label="Message (optionnel)"
                wire:model="message"
                rows="3"
                placeholder="Présentez-vous ou expliquez l'objet de votre mise en relation…"
            />
            <x-slot:actions>
                <x-button label="Annuler" wire:click="$set('showForm', false)" class="btn-ghost" />
                <x-button label="Envoyer" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-modal>

</div>
