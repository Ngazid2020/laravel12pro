<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; background: #fff; }
    .page { padding: 40px; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #4f46e5; padding-bottom: 20px; margin-bottom: 28px; }
    .brand { font-size: 22px; font-weight: 700; color: #4f46e5; }
    .brand span { font-weight: 300; color: #6b7280; }
    .receipt-meta { text-align: right; color: #6b7280; font-size: 11px; }
    .receipt-meta strong { display: block; font-size: 16px; color: #1f2937; font-weight: 700; }

    /* Status badge */
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .badge-success { background: #d1fae5; color: #065f46; }

    /* Section */
    .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; margin-bottom: 8px; }
    .card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 16px; }

    /* Table */
    table { width: 100%; border-collapse: collapse; }
    table td { padding: 8px 4px; vertical-align: top; }
    table td:first-child { color: #6b7280; width: 40%; }
    table td:last-child { font-weight: 600; }
    .divider { border: none; border-top: 1px solid #e5e7eb; margin: 16px 0; }

    /* Amount */
    .amount-box { background: #4f46e5; color: #fff; border-radius: 8px; padding: 16px 20px; text-align: center; }
    .amount-box .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.8; }
    .amount-box .value { font-size: 28px; font-weight: 700; margin-top: 2px; }

    /* Footer */
    .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; text-align: center; }
    .footer a { color: #4f46e5; }
</style>
</head>
<body>
<div class="page">

    {{-- ===== HEADER ===== --}}
    <div class="header">
        <div>
            <div class="brand">Réseau <span>Entrepreneurs</span></div>
            <div style="color:#6b7280;font-size:11px;margin-top:4px;">Îles Comores · Réseau des Jeunes Entrepreneurs</div>
        </div>
        <div class="receipt-meta">
            <strong>REÇU DE PAIEMENT</strong>
            N° {{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}<br>
            Émis le {{ now()->format('d/m/Y') }}<br>
            <span class="badge badge-success" style="margin-top:6px;">Validé</span>
        </div>
    </div>

    {{-- ===== MEMBRE ===== --}}
    <div class="section-title">Informations du membre</div>
    <div class="card">
        <table>
            <tr>
                <td>Nom complet</td>
                <td>{{ $payment->user->full_name }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $payment->user->email }}</td>
            </tr>
            @if($payment->user->phone)
            <tr>
                <td>Téléphone</td>
                <td>{{ $payment->user->phone }}</td>
            </tr>
            @endif
            @if($payment->user->profile?->company_name)
            <tr>
                <td>Entreprise</td>
                <td>{{ $payment->user->profile->company_name }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ===== PAIEMENT ===== --}}
    <div style="display:flex;gap:16px;">
        <div style="flex:1;">
            <div class="section-title">Détails du paiement</div>
            <div class="card">
                <table>
                    <tr>
                        <td>Date de validation</td>
                        <td>{{ $payment->validated_at?->format('d/m/Y à H\hi') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Moyen de paiement</td>
                        <td>{{ match($payment->method) {
                            'mvola' => 'MVola',
                            'holo_money' => 'Holo Money',
                            'cash' => 'Espèces',
                            'cheque' => 'Chèque',
                            default => $payment->method
                        } }}</td>
                    </tr>
                    @if($payment->transaction_reference)
                    <tr>
                        <td>Référence</td>
                        <td>{{ $payment->transaction_reference }}</td>
                    </tr>
                    @endif
                    @if($payment->cheque_number)
                    <tr>
                        <td>N° chèque</td>
                        <td>{{ $payment->cheque_number }} — {{ $payment->bank_name }}</td>
                    </tr>
                    @endif
                    @if($payment->period_start && $payment->period_end)
                    <tr>
                        <td>Période couverte</td>
                        <td>{{ $payment->period_start->format('d/m/Y') }} → {{ $payment->period_end->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @if($payment->validator)
                    <tr>
                        <td>Validé par</td>
                        <td>{{ $payment->validator->name }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        <div style="width:160px;">
            <div class="section-title">Montant</div>
            <div class="amount-box">
                <div class="label">Total payé</div>
                <div class="value">{{ number_format($payment->amount) }}</div>
                <div style="font-size:11px;margin-top:2px;opacity:0.8;">KMF</div>
            </div>
            @if($payment->payable instanceof \App\Models\SubscriptionPlan)
            <div style="text-align:center;margin-top:8px;font-size:10px;color:#6b7280;">
                Cotisation {{ $payment->payable->period === 'annual' ? 'annuelle' : 'mensuelle' }}
            </div>
            @endif
        </div>
    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        Ce reçu est un document officiel du Réseau des Entrepreneurs des Comores.<br>
        Pour toute question : contact@reseau-entrepreneurs.km · {{ config('app.url') }}
    </div>

</div>
</body>
</html>
