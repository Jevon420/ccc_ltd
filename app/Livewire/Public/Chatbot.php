<?php

namespace App\Livewire\Public;

use App\Ai\Agents\PublicChatbot as PublicChatbotAgent;
use App\Models\Setting;
use App\Traits\Livewire\HasToast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Chatbot extends Component
{
    use HasToast;

    public bool $isOpen = false;

    public string $input = '';

    public bool $thinking = false;

    public bool $limitReached = false;

    /** @var array<array{role: string, content: string, time: string}> */
    public array $messages = [];

    public function mount(): void
    {
        $greeting = Setting::get('ai_chatbot_greeting', "Hi! I'm the CCC virtual assistant. How can I help you today?");
        $this->messages = [
            ['role' => 'assistant', 'content' => $greeting, 'time' => now()->format('g:i A')],
        ];
    }

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    /**
     * Phase 1: validate, clear input, add user message, show thinking.
     * Renders immediately so the user sees their message + animation BEFORE the AI runs.
     * Then dispatches 'chatbot-process-response' which triggers Phase 2.
     */
    public function send(): void
    {
        $input = trim($this->input);
        $this->input = ''; // clear immediately — visible in this render

        if (empty($input)) {
            return;
        }

        if (! (bool) Setting::get('ai_public_enabled', false) || ! (bool) Setting::get('ai_chatbot_enabled', false)) {
            $this->toastError('Chat is currently unavailable.');

            return;
        }

        $maxMessages = (int) Setting::get('ai_chatbot_max_messages', 8);
        $userMessages = collect($this->messages)->where('role', 'user')->count();

        if ($userMessages >= $maxMessages) {
            $this->limitReached = true;
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "You've reached the maximum of {$maxMessages} messages per session. Please use our contact form or call us directly.",
                'time' => now()->format('g:i A'),
            ];

            return;
        }

        $ip = request()->ip();
        $key = 'chatbot_daily:'.$ip;

        if (RateLimiter::tooManyAttempts($key, 20)) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "You've reached the daily chat limit. Please call us or use our contact form.",
                'time' => now()->format('g:i A'),
            ];

            return;
        }

        RateLimiter::hit($key, 86400);

        $this->messages[] = ['role' => 'user', 'content' => mb_substr($input, 0, 500), 'time' => now()->format('g:i A')];
        $this->thinking = true;

        // Trigger Phase 2 as a separate Livewire request — user sees animation first
        $this->dispatch('chatbot-process-response');
    }

    /**
     * Phase 2: AI call in a separate request so the thinking animation is visible.
     */
    #[On('chatbot-process-response')]
    public function processResponse(): void
    {
        if (! $this->thinking) {
            return;
        }

        $greeting = Setting::get('ai_chatbot_greeting', '');

        $history = collect($this->messages)
            ->filter(fn ($m) => ! ($m['role'] === 'assistant' && $m['content'] === $greeting))
            ->map(fn ($m) => ['role' => $m['role'], 'content' => $m['content']])
            ->values()
            ->toArray();

        // Last entry is the current user prompt
        $current = array_pop($history);
        $conversationHistory = $history;

        try {
            $agent = new PublicChatbotAgent($conversationHistory);
            $response = $agent->prompt($current['content']);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response->text,
                'time' => now()->format('g:i A'),
            ];
        } catch (\Throwable $e) {
            Log::warning('Public chatbot error', ['error' => $e->getMessage(), 'ip' => request()->ip()]);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "Sorry, I'm having trouble responding right now. Please call us or use our contact form.",
                'time' => now()->format('g:i A'),
            ];
        } finally {
            $this->thinking = false;
        }
    }

    public function render(): View
    {
        return view('livewire.public.chatbot');
    }
}
