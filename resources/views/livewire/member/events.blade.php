<div class="p-4 lg:p-6 space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-2xl font-bold">Événements</h1>
        <x-radio
            wire:model.live="filter"
            :options="[['id'=>'upcoming','name'=>'À venir'],['id'=>'past','name'=>'Passés']]"
            option-value="id"
            option-label="name"
            inline
        />
    </div>

    @forelse($events as $event)
        @php $registrationId = $myRegistrations[$event->id] ?? null; @endphp
        <x-card shadow class="border border-base-200">
            <div class="flex flex-col sm:flex-row gap-4">
                {{-- Date bloc --}}
                <div class="shrink-0 bg-primary/10 rounded-xl text-center p-3 w-16 h-16 sm:w-20 sm:h-20 flex flex-col items-center justify-center">
                    <p class="text-2xl font-bold text-primary leading-none">{{ $event->starts_at->format('d') }}</p>
                    <p class="text-xs text-primary uppercase">{{ $event->starts_at->locale('fr')->isoFormat('MMM') }}</p>
                </div>

                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-primary badge-sm">{{ $typeLabels[$event->type] ?? $event->type }}</span>
                        @if($event->is_paid)
                            <span class="badge badge-warning badge-sm">{{ number_format($event->price) }} KMF</span>
                        @else
                            <span class="badge badge-success badge-sm">Gratuit</span>
                        @endif
                        @if($event->capacity)
                            @php $remaining = $event->capacity - $event->registrations()->count(); @endphp
                            <span class="badge badge-outline badge-sm {{ $remaining <= 0 ? 'badge-error' : '' }}">
                                {{ $remaining <= 0 ? 'Complet' : $remaining.' place(s)' }}
                            </span>
                        @endif
                    </div>

                    <h3 class="font-semibold text-lg mt-1">{{ $event->title }}</h3>

                    <div class="flex flex-wrap gap-3 text-sm text-base-content/60 mt-1">
                        <span>
                            <x-icon name="o-clock" class="w-4 h-4 inline" />
                            {{ $event->starts_at->isoFormat('D MMM YYYY [à] HH[h]mm') }}
                        </span>
                        @if($event->location)
                            <span>
                                <x-icon name="o-map-pin" class="w-4 h-4 inline" />
                                {{ $event->location }}
                            </span>
                        @endif
                    </div>

                    @if($event->description)
                        <p class="text-sm text-base-content/70 mt-2 line-clamp-2">
                            {{ strip_tags($event->description) }}
                        </p>
                    @endif
                </div>

                {{-- Action --}}
                <div class="sm:text-right shrink-0 flex sm:flex-col gap-2 items-start sm:items-end">
                    @if($filter === 'upcoming')
                        @if($registrationId)
                            <span class="badge badge-success gap-1">
                                <x-icon name="o-check" class="w-3 h-3" /> Inscrit
                            </span>
                            {{-- Bouton QR code --}}
                            <x-button
                                label="Mon QR"
                                icon="o-qr-code"
                                class="btn-outline btn-sm btn-primary"
                                wire:click="showQrCode({{ $registrationId }})"
                            />
                            <x-button
                                label="Annuler"
                                class="btn-ghost btn-xs text-error"
                                wire:click="unregister({{ $event->id }})"
                                wire:confirm="Annuler votre inscription ?"
                            />
                        @elseif(!$event->isFull())
                            <x-button
                                label="S'inscrire"
                                icon="o-calendar"
                                class="btn-primary btn-sm"
                                wire:click="register({{ $event->id }})"
                            />
                        @else
                            <span class="badge badge-error">Complet</span>
                        @endif
                    @endif
                </div>
            </div>
        </x-card>
    @empty
        <div class="text-center py-16">
            <x-icon name="o-calendar-days" class="w-14 h-14 text-base-content/20 mx-auto mb-3" />
            <p class="text-base-content/50">
                {{ $filter === 'upcoming' ? 'Aucun événement à venir.' : 'Aucun événement passé.' }}
            </p>
        </div>
    @endforelse

    {{ $events->links() }}

    {{-- Modal QR Code --}}
    <x-modal wire:model="showQr" title="QR Code d'entrée" class="backdrop-blur">
        <div class="flex flex-col items-center gap-4 py-2">
            @if($qrEventTitle)
                <p class="font-semibold text-center">{{ $qrEventTitle }}</p>
            @endif

            @if($qrUrl)
                <div class="p-4 bg-white rounded-xl border border-base-200 shadow-inner">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->margin(1)->generate($qrUrl) !!}
                </div>
                <p class="text-xs text-base-content/50 text-center max-w-xs">
                    Présentez ce QR code à l'entrée. Le staff le scannera pour valider votre présence.
                </p>
            @endif
        </div>
        <x-slot:actions>
            <x-button label="Fermer" wire:click="$set('showQr', false)" class="btn-primary w-full" />
        </x-slot:actions>
    </x-modal>

</div>
