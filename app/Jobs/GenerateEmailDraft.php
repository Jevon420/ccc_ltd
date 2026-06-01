<?php

namespace App\Jobs;

use App\Ai\Agents\EmailDrafter as EmailDrafterAgent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateEmailDraft implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 90;

    public function __construct(
        public readonly string $cacheKey,
        public readonly string $prompt,
    ) {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        try {
            $response = (new EmailDrafterAgent)->prompt($this->prompt);
            Cache::put($this->cacheKey, ['draft' => $response->text, 'done' => true], now()->addMinutes(30));
        } catch (\Throwable $e) {
            Cache::put($this->cacheKey, ['error' => $e->getMessage(), 'done' => true], now()->addMinutes(5));
            Log::error('GenerateEmailDraft failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
