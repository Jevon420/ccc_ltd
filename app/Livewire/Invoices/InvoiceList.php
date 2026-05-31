<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use HasToast, WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public bool $showTrashed = false;

    public ?int $confirmingDeleteId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('invoices.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteInvoice(): void
    {
        abort_unless(auth()->user()->can('invoices.delete'), 403);

        $invoice = Invoice::findOrFail($this->confirmingDeleteId);
        $invoice->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess("Invoice {$invoice->reference} deleted.");
    }

    public function restore(int $id): void
    {
        abort_unless(auth()->user()->can('invoices.delete'), 403);

        $invoice = Invoice::withTrashed()->findOrFail($id);
        $invoice->restore();
        $this->toastSuccess("Invoice {$invoice->reference} restored.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('invoices.view'), 403);

        $invoices = Invoice::withTrashed()
            ->with('client')
            ->when(! $this->showTrashed, fn ($q) => $q->whereNull('deleted_at'))
            ->when($this->showTrashed, fn ($q) => $q->whereNotNull('deleted_at'))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('reference', 'like', "%{$this->search}%")
                    ->orWhere('title', 'like', "%{$this->search}%")
                    ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.invoices.invoice-list', compact('invoices'));
    }
}
