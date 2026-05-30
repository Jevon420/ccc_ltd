<?php

namespace App\Livewire\Quotes;

use App\Ai\Agents\QuoteDrafter as QuoteDrafterAgent;
use App\Models\Quote;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class QuoteDrafter extends Component
{
    // Form fields
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

        Quote::create([
            'client_name' => $this->client_name,
            'client_email' => $this->client_email ?: null,
            'client_phone' => $this->client_phone ?: null,
            'service_type' => $this->service_type,
            'location' => $this->location ?: null,
            'job_details' => $this->job_details,
            'ai_draft' => $this->ai_draft ?: null,
            'status' => 'draft',
        ]);

        $this->saved = true;
        $this->reset(['client_name', 'client_email', 'client_phone', 'service_type', 'location', 'job_details', 'ai_draft']);
    }

    public function render()
    {
        return view('livewire.quotes.quote-drafter');
    }
}
