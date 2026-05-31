<?php

namespace App\Livewire\Quotes;

use App\Models\Quote;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class QuoteList extends Component
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
        abort_unless(auth()->user()->can('quotes.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteQuote(): void
    {
        abort_unless(auth()->user()->can('quotes.delete'), 403);

        $quote = Quote::findOrFail($this->confirmingDeleteId);
        $quote->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess("Quote {$quote->reference} deleted.");
    }

    public function restore(int $id): void
    {
        abort_unless(auth()->user()->can('quotes.delete'), 403);

        $quote = Quote::withTrashed()->findOrFail($id);
        $quote->restore();
        $this->toastSuccess("Quote {$quote->reference} restored.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('quotes.view'), 403);

        $quotes = Quote::withTrashed()
            ->with(['client', 'serviceType'])
            ->when(! $this->showTrashed, fn ($q) => $q->whereNull('deleted_at'))
            ->when($this->showTrashed, fn ($q) => $q->whereNotNull('deleted_at'))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('reference', 'like', "%{$this->search}%")
                    ->orWhere('title', 'like', "%{$this->search}%")
                    ->orWhere('client_name', 'like', "%{$this->search}%")
                    ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.quotes.quote-list', compact('quotes'));
    }
}
