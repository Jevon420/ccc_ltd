<?php

namespace App\Livewire\Quotes;

use App\Models\Client;
use App\Models\Quote;
use App\Models\ServiceType;
use App\Traits\Livewire\HasToast;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class QuoteForm extends Component
{
    use HasToast;

    public ?int $quoteId = null;

    public bool $isOpen = false;

    public ?int $clientId = null;

    public string $title = '';

    public string $serviceType = '';

    public string $location = '';

    public string $jobDetails = '';

    public string $status = 'draft';

    public string $amount = '';

    public string $validUntil = '';

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'clientId' => ['required', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'serviceType' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:500'],
            'jobDetails' => ['required', 'string', 'min:10'],
            'status' => ['required', Rule::in(['draft', 'sent', 'accepted', 'declined', 'expired'])],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'validUntil' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    #[On('open-create-quote')]
    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('quotes.create'), 403);
        $this->reset(['quoteId', 'clientId', 'title', 'serviceType', 'location', 'jobDetails', 'amount', 'validUntil', 'notes']);
        $this->status = 'draft';
        $this->isOpen = true;
    }

    #[On('open-edit-quote')]
    public function openEdit(int $quoteId): void
    {
        abort_unless(auth()->user()->can('quotes.edit'), 403);
        $quote = Quote::findOrFail($quoteId);
        $this->quoteId = $quote->id;
        $this->clientId = $quote->client_id;
        $this->title = $quote->title ?? '';
        $this->serviceType = $quote->service_type ?? '';
        $this->location = $quote->location ?? '';
        $this->jobDetails = $quote->job_details ?? '';
        $this->status = $quote->status;
        $this->amount = $quote->amount ?? '';
        $this->validUntil = $quote->valid_until?->format('Y-m-d') ?? '';
        $this->notes = $quote->notes ?? '';
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'client_id' => $this->clientId,
            'title' => $this->title,
            'service_type' => $this->serviceType,
            'location' => $this->location ?: null,
            'job_details' => $this->jobDetails,
            'status' => $this->status,
            'amount' => $this->amount ?: null,
            'valid_until' => $this->validUntil ?: null,
            'notes' => $this->notes ?: null,
        ];

        if ($this->quoteId) {
            abort_unless(auth()->user()->can('quotes.edit'), 403);
            $quote = Quote::findOrFail($this->quoteId);
            $quote->update($data);
            $this->toastSuccess("Quote {$quote->reference} updated.");
        } else {
            abort_unless(auth()->user()->can('quotes.create'), 403);
            $client = Client::findOrFail($this->clientId);
            $data['client_name'] = $client->name;
            $quote = Quote::create($data);
            $this->toastSuccess("Quote {$quote->reference} created.");
        }

        $this->isOpen = false;
        $this->dispatch('quote-saved');
    }

    public function render(): View
    {
        return view('livewire.quotes.quote-form', [
            'clients' => Client::active()->orderBy('name')->get(),
            'serviceTypes' => ServiceType::active()->orderBy('name')->get(),
        ]);
    }
}
