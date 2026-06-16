<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * GET /api/v1/payments
     */
    public function index(Request $request): JsonResponse
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->with('subscriptionPlan')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json(PaymentResource::collection($payments)->response()->getData(true));
    }

    /**
     * GET /api/v1/payments/plans
     * Liste les plans de cotisation actifs.
     */
    public function plans(): JsonResponse
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return response()->json($plans->map(fn ($p) => [
            'id'          => $p->id,
            'name'        => $p->name,
            'description' => $p->description,
            'amount'      => $p->amount,
            'period'      => $p->period,
        ]));
    }

    /**
     * POST /api/v1/payments
     * Déclarer un paiement.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id'               => ['required', 'exists:subscription_plans,id'],
            'method'                => ['required', 'in:mvola,holo_money,cash,cheque'],
            'transaction_reference' => ['required_if:method,mvola', 'required_if:method,holo_money', 'nullable', 'string'],
            'cheque_number'         => ['required_if:method,cheque', 'nullable', 'string'],
            'bank_name'             => ['required_if:method,cheque', 'nullable', 'string'],
            'cheque_date'           => ['required_if:method,cheque', 'nullable', 'date'],
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $payment = Payment::create([
            'user_id'               => $request->user()->id,
            'subscription_plan_id'  => $plan->id,
            'amount'                => $plan->amount,
            'method'                => $validated['method'],
            'status'                => 'pending',
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'cheque_number'         => $validated['cheque_number'] ?? null,
            'bank_name'             => $validated['bank_name'] ?? null,
            'cheque_date'           => $validated['cheque_date'] ?? null,
        ]);

        return response()->json([
            'message' => 'Paiement déclaré. En attente de validation par l\'administration.',
            'payment' => new PaymentResource($payment->load('subscriptionPlan')),
        ], 201);
    }

    /**
     * POST /api/v1/payments/{payment}/screenshot
     * Uploader une preuve de paiement (capture d'écran).
     */
    public function uploadScreenshot(Request $request, Payment $payment): JsonResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 403);
        abort_unless($payment->status === 'pending', 422);

        $request->validate([
            'screenshot' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($payment->screenshot_path) {
            Storage::disk('local')->delete($payment->screenshot_path);
        }

        $path = $request->file('screenshot')->store('payment-screenshots', 'local');
        $payment->update(['screenshot_path' => $path]);

        return response()->json(['message' => 'Justificatif uploadé.']);
    }
}