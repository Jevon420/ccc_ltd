<?php

namespace App\Livewire\Quotes;

use App\Jobs\GenerateQuoteDraft;
use App\Models\Client;
use App\Models\Quote;
use App\Models\ServiceType;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;

class QuoteDrafter extends Component
{
    use HasToast;

    // Form fields
    public ?int $clientId = null;

    public string $client_name = '';

    public string $client_email = '';

    public string $client_phone = '';

    public string $service_type = '';

    public string $location = '';

    public string $job_details = '';

    // State
    public bool $generating = false;

    public bool $saved = false;

    public ?int $draftQuoteId = null;

    public ?string $errorMessage = null;

    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'service_type' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:500'],
            'job_details' => ['required', 'string', 'min:20'],
        ];
    }

    public function updatedClientId(?int $value): void
    {
        if ($value && $client = Client::find($value)) {
            $this->client_name = $client->name;
            $this->client_email = $client->email ?? '';
            $this->client_phone = $client->phone ?? '';
        }
    }

    public function generate(): void
    {
        abort_unless(auth()->user()->can('ai_tools.use'), 403);
        abort_unless(auth()->user()->can('quotes.create'), 403);

        $this->validate();

        $this->errorMessage = null;
        $this->generating = true;

        $quote = Quote::create([
            'client_id' => $this->clientId ?: null,
            'client_name' => $this->client_name,
            'client_email' => $this->client_email ?: null,
            'client_phone' => $this->client_phone ?: null,
            'title' => "Quote — {$this->client_name} ({$this->service_type})",
            'service_type' => $this->service_type,
            'location' => $this->location ?: null,
            'job_details' => $this->job_details,
            'status' => 'draft',
        ]);

        $this->draftQuoteId = $quote->id;

        $prompt = <<<PROMPT
        Client: {$this->client_name}
        Service Type: {$this->service_type}
        Location: {$this->location}
        Job Details: {$this->job_details}
        PROMPT;

        GenerateQuoteDraft::dispatch($quote->id, $prompt);

        $this->toastInfo('Generating AI draft in the background… this takes a few seconds.');
    }

    /**
     * Called by wire:poll in the view every 3 seconds while generating.
     */
    public function checkDraft(): void
    {
        if (! $this->generating || ! $this->draftQuoteId) {
            return;
        }

        $quote = Quote::find($this->draftQuoteId);

        if ($quote && $quote->ai_draft) {
            $this->generating = false;
            $this->saved = true;
            $this->toastSuccess("Quote {$quote->reference} draft ready — view it in Quotes.");
            $this->reset(['clientId', 'client_name', 'client_email', 'client_phone', 'service_type', 'location', 'job_details', 'draftQuoteId']);
        }
    }

    public function render(): View
    {
        return view('livewire.quotes.quote-drafter', [
            'clients' => Client::active()->orderBy('name')->get(),
            'serviceTypes' => ServiceType::active()->orderBy('name')->get(),
        ]);
    }
}
