<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'amount'                 => $this->amount,
            'method'                 => $this->method,
            'method_label'           => match ($this->method) {
                'mvola'       => 'M-Vola',
                'holo_money'  => 'Holo Money',
                'cash'        => 'Espèces',
                'cheque'      => 'Chèque',
                default       => $this->method,
            },
            'status'                 => $this->status,
            'status_label'           => match ($this->status) {
                'pending'   => 'En attente',
                'validated' => 'Validé',
                'rejected'  => 'Rejeté',
                default     => $this->status,
            },
            'transaction_reference'  => $this->transaction_reference,
            'period_start'           => $this->period_start?->toDateString(),
            'period_end'             => $this->period_end?->toDateString(),
            'validated_at'           => $this->validated_at?->toISOString(),
            'receipt_url'            => $this->isValidated() ? route('membre.payment.receipt', $this->id) : null,
            'subscription_plan'      => $this->whenLoaded('subscriptionPlan', fn () => [
                'id'     => $this->subscriptionPlan->id,
                'name'   => $this->subscriptionPlan->name,
                'amount' => $this->subscriptionPlan->amount,
                'period' => $this->subscriptionPlan->period,
            ]),
            'created_at'             => $this->created_at?->toISOString(),
        ];
    }
}