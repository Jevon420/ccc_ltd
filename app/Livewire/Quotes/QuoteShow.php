<?php

namespace App\Livewire\Quotes;

use App\Models\Quote;
use App\Traits\Livewire\HasToast;
use App\Traits\Notifies;
use Illuminate\View\View;
use Livewire\Component;

class QuoteShow extends Component
{
    use HasToast, Notifies;

    public Quote $quote;

    // Edit amount & valid_until inline
    public bool $editingAmount = false;

    public string $amount = '';

    public string $validUntil = '';

    public function mount(): void
    {
        $this->amount = $this->quote->amount ?? '';
        $this->validUntil = $this->quote->valid_until?->format('Y-m-d') ?? '';
    }

    public function saveAmount(): void
    {
        abort_unless(auth()->user()->can('quotes.edit'), 403);

        $this->validate([
            'amount' => ['nullable', 'numeric', 'min:0'],
            'validUntil' => ['nullable', 'date'],
        ]);

        $this->quote->update([
            'amount' => $this->amount ?: null,
            'valid_until' => $this->validUntil ?: null,
        ]);

        $this->quote->refresh();
        $this->editingAmount = false;
        $this->toastSuccess('Quote pricing updated.');
    }

    public function markSent(): void
    {
        abort_unless(auth()->user()->can('quotes.send'), 403);

        $this->quote->update(['status' => 'sent']);
        $this->quote->refresh();
        $this->toastSuccess("Quote {$this->quote->reference} marked as sent.");
    }

    public function approve(): void
    {
        abort_unless(auth()->user()->can('quotes.approve'), 403);

        $this->quote->update(['status' => 'accepted']);
        $this->quote->refresh();
        $this->toastSuccess("Quote {$this->quote->reference} accepted.");

        $this->notifyUsersWithPermission(
            'quotes.view',
            "Quote {$this->quote->reference} has been accepted by ".auth()->user()->name.'.',
            'success',
            'View Quote',
            route('dashboard.quotes.show', $this->quote)
        );
    }

    public function decline(): void
    {
        abort_unless(auth()->user()->can('quotes.reject'), 403);

        $this->quote->update(['status' => 'declined']);
        $this->quote->refresh();
        $this->toastWarning("Quote {$this->quote->reference} declined.");

        $this->notifyUsersWithPermission(
            'quotes.view',
            "Quote {$this->quote->reference} was declined.",
            'warning',
            'View Quote',
            route('dashboard.quotes.show', $this->quote)
        );
    }

    public function revertToDraft(): void
    {
        abort_unless(auth()->user()->can('quotes.edit'), 403);

        $this->quote->update(['status' => 'draft']);
        $this->quote->refresh();
        $this->toastInfo("Quote {$this->quote->reference} reverted to draft.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('quotes.view'), 403);

        return view('livewire.quotes.quote-show');
    }
}
