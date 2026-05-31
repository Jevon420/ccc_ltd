<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;

class InvoiceShow extends Component
{
    use HasToast;

    public Invoice $invoice;

    public function markSent(): void
    {
        abort_unless(auth()->user()->can('invoices.send'), 403);
        $this->invoice->update(['status' => 'sent']);
        $this->invoice->refresh();
        $this->toastSuccess("Invoice {$this->invoice->reference} marked as sent.");
    }

    public function markPaid(): void
    {
        abort_unless(auth()->user()->can('invoices.mark_paid'), 403);

        $this->invoice->update([
            'status' => 'paid',
            'amount_paid' => $this->invoice->total,
            'paid_date' => now(),
        ]);
        $this->invoice->refresh();
        $this->toastSuccess("Invoice {$this->invoice->reference} marked as paid in full.");
    }

    public function cancel(): void
    {
        abort_unless(auth()->user()->can('invoices.edit'), 403);
        $this->invoice->update(['status' => 'cancelled']);
        $this->invoice->refresh();
        $this->toastWarning("Invoice {$this->invoice->reference} cancelled.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('invoices.view'), 403);

        return view('livewire.invoices.invoice-show', [
            'payments' => $this->invoice->payments()->latest()->get(),
        ]);
    }
}
