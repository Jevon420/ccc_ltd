<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

/**
 * CCC Ops AI Service
 *
 * AI assistant service for CCC Ops — powered by laravel/ai (official Laravel AI SDK).
 * Docs: https://laravel.com/docs/ai
 *
 * Phase 2: implement each method using Laravel\Ai\Facades\Ai
 *
 * ⚠️  SAFETY RULES (enforced here):
 * - AI may ONLY assist with drafting, summarising, and suggestions.
 * - AI must NEVER autonomously approve, delete, invoice, mark paid,
 *   assign roles, or change critical records without human confirmation.
 * - All restricted actions are blocked via config('ai.restricted_actions').
 *
 * Phase 1: Foundation only. Add workflows in Phase 2+.
 */
class AiService
{
    public function isEnabled(): bool
    {
        return (bool) config('ai.enabled', false);
    }

    /**
     * Draft a quote description using AI.
     * Human must review and approve before saving.
     *
     * @return array{draft: string, tokens: int}
     */
    public function draftQuoteDescription(array $context): array
    {
        $this->guardEnabled();
        $this->guardFeature('quote_drafting');

        // TODO Phase 2: Implement with Prism PHP
        // Example:
        // $result = Prism::text()
        //     ->using('anthropic', config('ai.providers.anthropic.default_model'))
        //     ->withPrompt("Draft a professional quote description for: " . json_encode($context))
        //     ->generate();
        // return ['draft' => $result->text, 'tokens' => $result->usage->totalTokens];

        return ['draft' => 'AI quote drafting will be available in Phase 2.', 'tokens' => 0];
    }

    /**
     * Summarise a job report.
     */
    public function summariseReport(string $reportText): array
    {
        $this->guardEnabled();
        $this->guardFeature('report_summaries');

        // TODO Phase 2: Implement with Prism PHP
        return ['summary' => 'AI report summaries will be available in Phase 2.', 'tokens' => 0];
    }

    /**
     * Draft a professional email.
     */
    public function draftEmail(string $context, string $tone = 'professional'): array
    {
        $this->guardEnabled();
        $this->guardFeature('email_drafting');

        // TODO Phase 2: Implement with Prism PHP
        return ['draft' => 'AI email drafting will be available in Phase 2.', 'tokens' => 0];
    }

    // -------------------------------------------------------------------------
    // Guards
    // -------------------------------------------------------------------------

    private function guardEnabled(): void
    {
        if (! $this->isEnabled()) {
            throw new \RuntimeException('AI features are currently disabled. Enable them in Settings → Features.');
        }
    }

    private function guardFeature(string $feature): void
    {
        if (! config("ai.features.{$feature}", false)) {
            throw new \RuntimeException("AI feature [{$feature}] is not enabled.");
        }
    }

    /**
     * Block restricted AI actions — AI must never perform these without human confirmation.
     */
    public function guardNotRestricted(string $action): void
    {
        $restricted = config('ai.restricted_actions', []);
        if (in_array($action, $restricted)) {
            Log::warning("AI attempted restricted action: {$action}");
            throw new \RuntimeException("AI is not permitted to perform action [{$action}] without human confirmation.");
        }
    }
}
