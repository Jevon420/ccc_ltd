<?php

namespace App\Jobs;

use App\Ai\Agents\QuoteDrafter as QuoteDrafterAgent;
use App\Models\Quote;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateQuoteDraft implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(
        public readonly int $quoteId,
        public readonly string $prompt,
    ) {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        $quote = Quote::find($this->quoteId);

        if (! $quote) {
            return;
        }

        try {
            $response = (new QuoteDrafterAgent)->prompt($this->prompt);

            $quote->update(['ai_draft' => $response->text]);
        } catch (\Throwable $e) {
            Log::error('GenerateQuoteDraft job failed', [
                'quote_id' => $this->quoteId,
                'error' => $e->getMessage(),
            ]);

            // Re-throw so the job fails and can be retried
            throw $e;
        }
    }
}
