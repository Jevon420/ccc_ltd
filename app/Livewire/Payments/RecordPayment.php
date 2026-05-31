<?php

namespace App\Livewire\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Traits\Livewire\HasToast;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class RecordPayment extends Component
{
    use HasToast;

    protected $listeners = ['open-record-payment' => 'open'];

    public bool $isOpen = false;

    public ?int $invoiceId = null;

    public string $amount = '';

    public string $method = 'bank_transfer';

    public string $status = 'confirmed';

    public string $paidAt = '';

    public string $transactionReference = '';

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'invoiceId' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::in(['cash', 'bank_transfer', 'cheque', 'card', 'wipay', 'other'])],
            'status' => ['required', Rule::in(['pending', 'confirmed', 'failed', 'refunded'])],
            'paidAt' => ['required', 'date'],
            'transactionReference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function open(?int $invoiceId = null): void
    {
        abort_unless(auth()->user()->can('payments.create'), 403);

        $this->reset(['amount', 'transactionReference', 'notes']);
        $this->invoiceId = $invoiceId;
        $this->method = 'bank_transfer';
        $this->status = 'confirmed';
        $this->paidAt = now()->format('Y-m-d');

        // Pre-fill amount with balance due
        if ($invoiceId && $invoice = Invoice::find($invoiceId)) {
            $this->amount = $invoice->balanceDue > 0 ? $invoice->balanceDue : '';
            $this->invoiceId = $invoice->id;
        }

        $this->isOpen = true;
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can('payments.create'), 403);

        $this->validate();

        $invoice = Invoice::findOrFail($this->invoiceId);
        $payment = Payment::create([
            'invoice_id' => $this->invoiceId,
            'client_id' => $invoice->client_id,
            'amount' => $this->amount,
            'method' => $this->method,
            'status' => $this->status,
            'paid_at' => $this->paidAt,
            'transaction_reference' => $this->transactionReference ?: null,
            'notes' => $this->notes ?: null,
        ]);

        $this->isOpen = false;
        $this->reset(['invoiceId', 'amount', 'transactionReference', 'notes']);
        $this->toastSuccess("Payment {$payment->reference} of TTD ".number_format($payment->amount, 2).' recorded.');
        $this->dispatch('paymentSaved');
    }

    public function render(): View
    {
        $invoices = Invoice::whereNull('deleted_at')
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->with('client')
            ->latest()
            ->get();

        return view('livewire.payments.record-payment', compact('invoices'));
    }
}
