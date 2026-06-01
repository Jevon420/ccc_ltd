<?php

namespace App\Livewire\Public;

use App\Ai\Agents\PublicChatbot;
use App\Models\Setting;
use App\Traits\Livewire\HasToast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Component;

class ContactAssist extends Component
{
    use HasToast;

    public string $roughDescription = '';

    public string $refinedDescription = '';

    public bool $generating = false;

    public bool $isOpen = false;

    protected function rules(): array
    {
        return [
            'roughDescription' => ['required', 'string', 'min:20', 'max:1000'],
        ];
    }

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function refine(): void
    {
        if (! (bool) Setting::get('ai_public_enabled', false) || ! (bool) Setting::get('ai_contact_assist_enabled', false)) {
            return;
        }

        $this->validate();

        // 1 attempt per IP per 30 minutes
        $ip = request()->ip();
        $key = 'contact_assist:'.$ip;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $retryIn = ceil(RateLimiter::availableIn($key) / 60);
            $this->toastWarning("Please wait {$retryIn} minute(s) before using AI assist again.");

            return;
        }

        RateLimiter::hit($key, 1800); // 30 minutes

        $this->generating = true;
        $this->refinedDescription = '';

        try {
            $agent = new PublicChatbot([]);
            $prompt = <<<PROMPT
            A visitor to the CCC website has described their project in their own words.
            Help them turn this into a clear, professional project description they can submit in a contact form.
            Keep it concise (3–5 sentences), factual, and only based on what they said.
            Do NOT add prices, timelines, or guarantees.

            Their description:
            "{$this->roughDescription}"

            Return only the refined project description — no preamble.
            PROMPT;

            $response = $agent->prompt($prompt);
            $this->refinedDescription = $response->text;
            $this->dispatch('contact-assist-ready', description: $this->refinedDescription);
            $this->toastSuccess('Your project description has been refined. Review it below.');
        } catch (\Throwable $e) {
            Log::warning('ContactAssist error', ['error' => $e->getMessage()]);
            $this->toastError('AI assist is temporarily unavailable. Please describe your project manually.');
        } finally {
            $this->generating = false;
        }
    }

    public function useDescription(): void
    {
        $this->dispatch('contact-assist-ready', description: $this->refinedDescription);
        $this->isOpen = false;
        $this->roughDescription = '';
        $this->refinedDescription = '';
    }

    public function render(): View
    {
        return view('livewire.public.contact-assist');
    }
}
