<?php

namespace App\Livewire\AiTools;

use App\Jobs\GenerateEmailDraft as GenerateEmailDraftJob;
use App\Models\Setting;
use App\Traits\Livewire\HasToast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class EmailDrafter extends Component
{
    use HasToast;

    public string $recipient = '';

    public string $subject = '';

    public string $tone = 'professional';

    public string $context = '';

    public string $draft = '';

    public bool $generating = false;

    public ?string $cacheKey = null;

    public ?string $errorMessage = null;

    protected function rules(): array
    {
        return [
            'recipient' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'tone' => ['required', 'in:professional,friendly,firm,apologetic'],
            'context' => ['required', 'string', 'min:20'],
        ];
    }

    public function generate(): void
    {
        abort_unless(auth()->user()->can('ai_tools.use'), 403);
        abort_unless((bool) Setting::get('ai_features_enabled', false), 403);
        abort_unless((bool) Setting::get('ai_email_drafting', true), 403);

        $this->validate();

        $this->generating = true;
        $this->draft = '';
        $this->errorMessage = null;
        $this->cacheKey = 'email_draft_'.Str::uuid();

        $recipient = $this->recipient ?: 'Client';
        $prompt = <<<PROMPT
        Draft a {$this->tone} email for:
        Recipient: {$recipient}
        Subject: {$this->subject}
        Context / Points to cover:
        {$this->context}
        PROMPT;

        GenerateEmailDraftJob::dispatch($this->cacheKey, $prompt);
        $this->toastInfo('Drafting email in background…');
    }

    public function checkDraft(): void
    {
        if (! $this->generating || ! $this->cacheKey) {
            return;
        }

        $result = Cache::get($this->cacheKey);

        if ($result && ($result['done'] ?? false)) {
            $this->generating = false;

            if (isset($result['error'])) {
                $this->errorMessage = 'Draft failed: '.$result['error'];
                $this->toastError('AI email draft failed. Please try again.');
            } else {
                $this->draft = $result['draft'] ?? '';
                $this->toastSuccess('Email draft ready!');
            }

            Cache::forget($this->cacheKey);
            $this->cacheKey = null;
        }
    }

    public function copyDraft(): void
    {
        // Client-side copy handled by Alpine/JS — this just fires a toast
        $this->toastSuccess('Draft copied to clipboard.');
    }

    public function reset(): void
    {
        $this->reset(['recipient', 'subject', 'context', 'draft', 'generating', 'cacheKey', 'errorMessage']);
        $this->tone = 'professional';
    }

    public function render(): View
    {
        return view('livewire.ai-tools.email-drafter');
    }
}
