<?php

namespace App\Livewire\Quotes;

use App\Ai\Agents\QuoteDrafter as QuoteDrafterAgent;
use App\Models\Client;
use App\Models\Quote;
use App\Models\ServiceType;
use App\Traits\Livewire\HasToast;
use Illuminate\Support\Facades\Log;
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

    // AI output
    public string $ai_draft = '';

    public bool $generating = false;

    public bool $saved = false;

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
        $this->validate();
        $this->generateDraft();
    }

    public function generateDraft(): void
    {
        abort_unless(auth()->user()->can('ai_tools.use'), 403);

        $this->generating = true;
        $this->errorMessage = null;
        $this->ai_draft = '';

        try {
            $prompt = <<<PROMPT
            Client: {$this->client_name}
            Service Type: {$this->service_type}
            Location: {$this->location}
            Job Details: {$this->job_details}
            PROMPT;

            $response = (new QuoteDrafterAgent)->prompt($prompt);
            $this->ai_draft = $response->text;
        } catch (\Throwable $e) {
            $this->errorMessage = 'AI draft failed. Please try again or contact support.';
            Log::error('QuoteDrafter AI error', ['error' => $e->getMessage()]);
        } finally {
            $this->generating = false;
        }
    }

    public function saveQuote(): void
    {
        abort_unless(auth()->user()->can('quotes.create'), 403);

        $this->validate();

        $quote = Quote::create([
            'client_id' => $this->clientId ?: null,
            'client_name' => $this->client_name,
            'client_email' => $this->client_email ?: null,
            'client_phone' => $this->client_phone ?: null,
            'title' => "Quote — {$this->client_name} ({$this->service_type})",
            'service_type' => $this->service_type,
            'location' => $this->location ?: null,
            'job_details' => $this->job_details,
            'ai_draft' => $this->ai_draft ?: null,
            'status' => 'draft',
        ]);

        $this->saved = true;
        $this->reset(['clientId', 'client_name', 'client_email', 'client_phone', 'service_type', 'location', 'job_details', 'ai_draft']);
        $this->toastSuccess("Quote {$quote->reference} saved as draft.");
    }

    public function render(): View
    {
        return view('livewire.quotes.quote-drafter', [
            'clients' => Client::active()->orderBy('name')->get(),
            'serviceTypes' => ServiceType::active()->orderBy('name')->get(),
        ]);
    }
}
