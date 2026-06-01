<?php

namespace App\Jobs;

use App\Ai\Agents\ReportSummariser as ReportSummariserAgent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateReportSummary implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 90;

    public function __construct(
        public readonly string $cacheKey,
        public readonly string $reportText,
    ) {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        try {
            $response = (new ReportSummariserAgent)->prompt($this->reportText);
            Cache::put($this->cacheKey, ['summary' => $response->text, 'done' => true], now()->addMinutes(30));
        } catch (\Throwable $e) {
            Cache::put($this->cacheKey, ['error' => $e->getMessage(), 'done' => true], now()->addMinutes(5));
            Log::error('GenerateReportSummary failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
