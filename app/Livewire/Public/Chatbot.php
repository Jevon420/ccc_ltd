<?php

namespace App\Livewire\Public;

use App\Ai\Agents\PublicChatbot as PublicChatbotAgent;
use App\Models\Setting;
use App\Traits\Livewire\HasToast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Component;

class Chatbot extends Component
{
    use HasToast;

    public bool $isOpen = false;

    public string $input = '';

    public bool $thinking = false;

    /** @var array<array{role: string, content: string, time: string}> */
    public array $messages = [];

    public bool $limitReached = false;

    public function mount(): void
    {
        // Show greeting on first open
        $greeting = Setting::get('ai_chatbot_greeting', 'Hi! I\'m the CCC virtual assistant. How can I help you today?');
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

    public function send(): void
    {
        $input = trim($this->input);
        if (empty($input)) {
            return;
        }

        // Check feature enabled
        if (! (bool) Setting::get('ai_public_enabled', false) || ! (bool) Setting::get('ai_chatbot_enabled', false)) {
            $this->toastError('Chat is currently unavailable.');

            return;
        }

        // Enforce session message limit
        $maxMessages = (int) Setting::get('ai_chatbot_max_messages', 8);
        $userMessages = collect($this->messages)->where('role', 'user')->count();

        if ($userMessages >= $maxMessages) {
            $this->limitReached = true;
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "You've reached the maximum of {$maxMessages} messages per session. Please use our contact form or call us directly — we'd love to help!",
                'time' => now()->format('g:i A'),
            ];
            $this->input = '';

            return;
        }

        // IP-based daily rate limit (20 messages per IP per day)
        $ip = request()->ip();
        $key = 'chatbot_daily:'.$ip;

        if (RateLimiter::tooManyAttempts($key, 20)) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'You\'ve reached the daily chat limit. Please call us or use our contact form — our team is ready to help!',
                'time' => now()->format('g:i A'),
            ];
            $this->input = '';

            return;
        }

        RateLimiter::hit($key, 86400); // 24 hours

        // Enforce message length
        $input = mb_substr($input, 0, 500);

        // Add user message
        $this->messages[] = ['role' => 'user', 'content' => $input, 'time' => now()->format('g:i A')];
        $this->input = '';
        $this->thinking = true;

        // Build conversation history (exclude greeting, exclude time metadata)
        $history = collect($this->messages)
            ->filter(fn ($m) => $m['role'] !== 'assistant' || $m['content'] !== Setting::get('ai_chatbot_greeting', ''))
            ->map(fn ($m) => ['role' => $m['role'], 'content' => $m['content']])
            ->values()
            ->toArray();

        try {
            // Remove the last user message from history — it's the current prompt
            $conversationHistory = array_slice($history, 0, -1);
            $agent = new PublicChatbotAgent($conversationHistory);
            $response = $agent->prompt($input);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response->text,
                'time' => now()->format('g:i A'),
            ];
        } catch (\Throwable $e) {
            Log::warning('Public chatbot error', ['error' => $e->getMessage(), 'ip' => $ip]);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Sorry, I\'m having trouble responding right now. Please call us or use our contact form.',
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
