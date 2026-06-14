<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function __invoke(Payment $payment)
    {
        // Seul le propriétaire du paiement ou un admin peut télécharger le reçu
        if (Auth::id() !== $payment->user_id && !Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403);
        }

        // Uniquement les paiements validés
        if ($payment->status !== 'validated') {
            abort(404, 'Reçu disponible uniquement pour les paiements validés.');
        }

        $payment->load(['user.profile', 'payable', 'validator']);

        $pdf = Pdf::loadView('pdf.receipt', compact('payment'))
            ->setPaper('a4', 'portrait');

        $filename = 'recu-'.str_pad($payment->id, 6, '0', STR_PAD_LEFT).'.pdf';

        return $pdf->download($filename);
    }
}
