<?php

namespace App\Services\AI;

use App\Models\Setting;

/**
 * Central service for checking AI feature availability from the DB settings.
 * Use this rather than config() so Developers can toggle features from the UI.
 */
class AiSettingsService
{
    public function isEnabled(): bool
    {
        return (bool) Setting::get('ai_features_enabled', false);
    }

    public function provider(): string
    {
        return Setting::get('ai_provider', 'openai');
    }

    public function model(): string
    {
        return Setting::get('ai_model', 'gpt-4o-mini');
    }

    public function featureEnabled(string $feature): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        return (bool) Setting::get("ai_{$feature}", false);
    }

    public function quoteDraftingEnabled(): bool
    {
        return $this->featureEnabled('quote_drafting');
    }

    public function emailDraftingEnabled(): bool
    {
        return $this->featureEnabled('email_drafting');
    }

    public function reportSummariesEnabled(): bool
    {
        return $this->featureEnabled('report_summaries');
    }

    public function contactCategorisationEnabled(): bool
    {
        return $this->featureEnabled('contact_categorisation');
    }
}
