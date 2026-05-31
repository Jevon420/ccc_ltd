<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentList extends Component
{
    use HasToast, WithPagination;

    public string $search = '';

    public string $methodFilter = '';

    public string $statusFilter = '';

    public ?int $confirmingRefundId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'methodFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmRefund(int $id): void
    {
        abort_unless(auth()->user()->can('payments.refund'), 403);
        $this->confirmingRefundId = $id;
    }

    public function refundPayment(): void
    {
        abort_unless(auth()->user()->can('payments.refund'), 403);

        $payment = Payment::findOrFail($this->confirmingRefundId);
        $payment->update(['status' => 'refunded']);
        $this->confirmingRefundId = null;
        $this->toastWarning("Payment {$payment->reference} marked as refunded.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('payments.view'), 403);

        $payments = Payment::with(['invoice', 'client'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('reference', 'like', "%{$this->search}%")
                    ->orWhereHas('invoice', fn ($q) => $q->where('reference', 'like', "%{$this->search}%"))
                    ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->methodFilter, fn ($q) => $q->where('method', $this->methodFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.payments.payment-list', compact('payments'));
    }
}
