<div class="p-4 lg:p-6 space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-2xl font-bold">Mes Paiements</h1>
        <x-button
            label="Déclarer un paiement"
            icon="o-plus"
            class="btn-primary"
            wire:click="$set('showForm', true)"
        />
    </div>

    {{-- Info cotisation --}}
    @if(auth()->user()->profile?->membership_expires_at)
        @php $exp = auth()->user()->profile->membership_expires_at; @endphp
        <x-alert
            :icon="$exp->isPast() ? 'o-exclamation-triangle' : 'o-information-circle'"
            :class="$exp->isPast() ? 'alert-error' : ($exp->diffInDays() < 30 ? 'alert-warning' : 'alert-info')"
        >
            @if($exp->isPast())
                Votre cotisation a expiré le {{ $exp->format('d/m/Y') }}. Veuillez renouveler votre adhésion.
            @elseif($exp->diffInDays() < 30)
                Votre cotisation expire le {{ $exp->format('d/m/Y') }} (dans {{ $exp->diffInDays() }} jours).
            @else
                Cotisation valide jusqu'au {{ $exp->format('d/m/Y') }}.
            @endif
        </x-alert>
    @endif

    {{-- Historique --}}
    @forelse($payments as $payment)
        <x-card shadow class="border border-base-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="badge {{ $statusColors[$payment->status] ?? 'badge-ghost' }} badge-sm">
                            {{ $statusLabels[$payment->status] ?? $payment->status }}
                        </span>
                        <span class="badge badge-outline badge-sm">
                            {{ $methodLabels[$payment->method] ?? $payment->method }}
                        </span>
                    </div>
                    <p class="font-semibold mt-1">
                        {{ number_format($payment->amount) }} KMF
                        @if($payment->payable instanceof \App\Models\SubscriptionPlan)
                            <span class="text-sm font-normal text-base-content/60">
                                — Cotisation {{ $payment->payable->period === 'annual' ? 'annuelle' : 'mensuelle' }}
                            </span>
                        @endif
                    </p>
                    <p class="text-xs text-base-content/50 mt-0.5">
                        Déclaré le {{ $payment->created_at->format('d/m/Y à H\hi') }}
                        @if($payment->transaction_reference)
                            · Réf. {{ $payment->transaction_reference }}
                        @endif
                    </p>
                    @if($payment->notes && $payment->status === 'rejected')
                        <p class="text-xs text-error mt-1">
                            <x-icon name="o-x-circle" class="w-3 h-3 inline" />
                            Motif : {{ $payment->notes }}
                        </p>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if($payment->screenshot_path)
                        <a
                            href="{{ \Illuminate\Support\Facades\Storage::url($payment->screenshot_path) }}"
                            target="_blank"
                            class="btn btn-ghost btn-xs"
                        >
                            <x-icon name="o-paper-clip" class="w-4 h-4" /> Justificatif
                        </a>
                    @endif
                </div>
            </div>
        </x-card>
    @empty
        <div class="text-center py-16">
            <x-icon name="o-banknotes" class="w-14 h-14 text-base-content/20 mx-auto mb-3" />
            <p class="text-base-content/50">Aucun paiement enregistré.</p>
        </div>
    @endforelse

    {{ $payments->links() }}

    {{-- Modal déclaration --}}
    <x-modal wire:model="showForm" title="Déclarer un paiement" class="backdrop-blur">
        <x-form wire:submit="declare">

            <x-select
                label="Plan de cotisation"
                wire:model.live="plan_id"
                :options="$plans->map(fn($p) => [
                    'id'   => $p->id,
                    'name' => ($p->period === 'annual' ? 'Annuelle' : 'Mensuelle').' — '.number_format($p->amount).' KMF',
                ])->toArray()"
                option-value="id"
                option-label="name"
                placeholder="Sélectionner un plan"
                required
            />

            <x-select
                label="Mode de paiement"
                wire:model.live="method"
                :options="[
                    ['id'=>'mvola',      'name'=>'MVola'],
                    ['id'=>'holo_money', 'name'=>'Holo Money'],
                    ['id'=>'cash',       'name'=>'Espèces'],
                    ['id'=>'cheque',     'name'=>'Chèque'],
                ]"
                option-value="id"
                option-label="name"
                required
            />

            @if(in_array($method, ['mvola', 'holo_money']))
                <x-input
                    label="Référence de transaction"
                    wire:model="transaction_reference"
                    placeholder="Ex: TXN-20260613-XXXX"
                    required
                />
            @endif

            @if($method === 'cheque')
                <x-input
                    label="Numéro de chèque"
                    wire:model="cheque_number"
                    required
                />
                <x-input
                    label="Banque"
                    wire:model="bank_name"
                    required
                />
            @endif

            <x-file
                label="Justificatif (capture d'écran, photo chèque…)"
                wire:model="screenshot"
                hint="JPG, PNG ou PDF — max 4 Mo"
                accept="image/jpeg,image/png,application/pdf"
            />

            <x-textarea
                label="Notes (optionnel)"
                wire:model="notes"
                rows="2"
            />

            <x-slot:actions>
                <x-button label="Annuler" wire:click="$set('showForm', false)" class="btn-ghost" />
                <x-button label="Envoyer la déclaration" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-modal>

</div>
