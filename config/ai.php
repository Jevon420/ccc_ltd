<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CCC Ops AI Configuration
    | Phase 2: install an AI SDK compatible with Laravel 13
    | e.g. openai-php/laravel, anthropic-sdk-php, or prism-php/prism (when L13 support lands)
    |
    | IMPORTANT: AI features must ONLY assist users — suggestions, drafts, summaries.
    | AI must NEVER autonomously approve, delete, invoice, mark paid,
    | assign roles, or modify critical records without explicit human confirmation.
    |--------------------------------------------------------------------------
    */

    'enabled' => env('AI_FEATURES_ENABLED', false),

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'anthropic'),

    'providers' => [
        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY', ''),
            'default_model' => env('ANTHROPIC_DEFAULT_MODEL', 'claude-3-5-haiku-20241022'),
        ],
        'openai' => [
            'api_key' => env('OPENAI_API_KEY', ''),
            'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4o-mini'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'quote_drafting'    => env('AI_QUOTE_DRAFTING', false),
        'report_summaries'  => env('AI_REPORT_SUMMARIES', false),
        'email_drafting'    => env('AI_EMAIL_DRAFTING', false),
        'job_notes'         => env('AI_JOB_NOTES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Safety Rules (enforced in all AI services)
    |--------------------------------------------------------------------------
    | These are checked before any AI action is performed.
    */
    'restricted_actions' => [
        'approve_invoice',
        'mark_paid',
        'delete_record',
        'assign_role',
        'change_permissions',
        'approve_quote',
    ],
];
