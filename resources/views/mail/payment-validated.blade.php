<x-mail::message>
# Paiement confirmé ✅

Cher(e) **{{ $payment->user->full_name }}**,

Votre paiement a été **validé** par notre équipe. Vous trouverez votre reçu officiel en pièce jointe.

<x-mail::panel>
**Récapitulatif du paiement**

- Montant : **{{ number_format($payment->amount) }} KMF**
- Moyen de paiement : {{ match($payment->method) { 'mvola' => 'MVola', 'holo_money' => 'Holo Money', 'cash' => 'Espèces', 'cheque' => 'Chèque', default => $payment->method } }}
@if($payment->transaction_reference)
- Référence : {{ $payment->transaction_reference }}
@endif
@if($payment->period_start && $payment->period_end)
- Période couverte : {{ $payment->period_start->format('d/m/Y') }} → {{ $payment->period_end->format('d/m/Y') }}
@endif
- Validé le : {{ $payment->validated_at?->format('d/m/Y à H\hi') }}
</x-mail::panel>

Votre adhésion est maintenant active. Vous pouvez accéder à l'ensemble des services du réseau.

<x-mail::button :url="route('membre.payments')" color="success">
Voir mes paiements
</x-mail::button>

Cordialement,
**L'équipe du Réseau Entrepreneurs Comores**
</x-mail::message>
