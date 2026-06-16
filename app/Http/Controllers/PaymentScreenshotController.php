<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentScreenshotController extends Controller
{
    public function __invoke(Payment $payment)
    {
        // Seul le propriétaire ou un admin peut voir le justificatif
        if (Auth::id() !== $payment->user_id && ! Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403);
        }

        if (! $payment->screenshot_path) {
            abort(404);
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($payment->screenshot_path)) {
            abort(404);
        }

        $mimeType = $disk->mimeType($payment->screenshot_path);

        return response()->stream(
            fn () => fpassthru($disk->readStream($payment->screenshot_path)),
            200,
            [
                'Content-Type'        => $mimeType,
                'Content-Disposition' => 'inline',
                'Cache-Control'       => 'private, no-store',
            ]
        );
    }
}