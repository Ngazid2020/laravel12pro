<?php

namespace App\Livewire\Member;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Mes Paiements')]
class Payments extends Component
{
    use WithPagination, WithFileUploads, Toast;

    // Modal state
    public bool $showForm = false;

    // Form fields
    public ?int $plan_id = null;
    public string $method = 'mvola';
    public string $transaction_reference = '';
    public ?string $cheque_number = null;
    public ?string $bank_name = null;
    public $screenshot = null;
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'plan_id'               => 'required|exists:subscription_plans,id',
            'method'                => 'required|in:mvola,holo_money,cash,cheque',
            'transaction_reference' => 'required_unless:method,cash|nullable|string|max:100',
            'cheque_number'         => 'required_if:method,cheque|nullable|string|max:50',
            'bank_name'             => 'required_if:method,cheque|nullable|string|max:100',
            'screenshot'            => 'nullable|file|max:4096|mimes:jpg,jpeg,png,pdf',
            'notes'                 => 'nullable|string|max:500',
        ];
    }

    public function declare(): void
    {
        $this->validate();

        $plan = SubscriptionPlan::findOrFail($this->plan_id);

        $screenshotPath = null;
        if ($this->screenshot) {
            $screenshotPath = $this->screenshot->store('payment-proofs', 'public');
        }

        Payment::create([
            'user_id'               => Auth::id(),
            'payable_type'          => SubscriptionPlan::class,
            'payable_id'            => $plan->id,
            'amount'                => $plan->amount,
            'method'                => $this->method,
            'transaction_reference' => $this->transaction_reference ?: null,
            'cheque_number'         => $this->cheque_number ?: null,
            'bank_name'             => $this->bank_name ?: null,
            'screenshot_path'       => $screenshotPath,
            'notes'                 => $this->notes ?: null,
            'status'                => 'pending',
        ]);

        $this->reset(['showForm', 'plan_id', 'transaction_reference', 'cheque_number', 'bank_name', 'screenshot', 'notes']);
        $this->method = 'mvola';
        $this->success('Paiement déclaré. En attente de validation par l\'administrateur.');
    }

    public function render()
    {
        $payments = Payment::where('user_id', Auth::id())
            ->with('payable')
            ->latest()
            ->paginate(10);

        $plans = SubscriptionPlan::orderBy('period')->get();

        $methodLabels = [
            'mvola'      => 'MVola',
            'holo_money' => 'Holo Money',
            'cash'       => 'Espèces',
            'cheque'     => 'Chèque',
        ];

        $statusLabels = [
            'pending'   => 'En attente',
            'validated' => 'Validé',
            'rejected'  => 'Rejeté',
        ];

        $statusColors = [
            'pending'   => 'badge-warning',
            'validated' => 'badge-success',
            'rejected'  => 'badge-error',
        ];

        return view('livewire.member.payments', compact(
            'payments', 'plans', 'methodLabels', 'statusLabels', 'statusColors'
        ));
    }
}
