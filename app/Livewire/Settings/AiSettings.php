<?php

namespace App\Livewire\Settings;

use App\Ai\Agents\QuoteDrafter;
use App\Models\Setting;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;

class AiSettings extends Component
{
    use HasToast;

    // Master toggle
    public bool $ai_features_enabled = false;

    // Provider & model
    public string $ai_provider = 'openai';

    public string $ai_model = 'gpt-4o-mini';

    // Admin feature toggles
    public bool $ai_quote_drafting = true;

    public bool $ai_email_drafting = true;

    public bool $ai_report_summaries = true;

    public bool $ai_contact_categorisation = false;

    // Public UI toggles
    public bool $ai_public_enabled = false;

    public bool $ai_chatbot_enabled = false;

    public string $ai_chatbot_greeting = '';

    public int $ai_chatbot_max_messages = 8;

    public bool $ai_contact_assist_enabled = false;

    // Connection test
    public ?string $testResult = null;

    public bool $testPassed = false;

    public bool $testingConnection = false;

    public static array $providers = [
        'openai' => ['name' => 'OpenAI', 'models' => ['gpt-4o-mini', 'gpt-4o', 'gpt-4-turbo']],
        'anthropic' => ['name' => 'Anthropic', 'models' => ['claude-haiku-4-5-20251001', 'claude-sonnet-4-5-20251022', 'claude-opus-4-5-20251101']],
    ];

    protected function rules(): array
    {
        return [
            'ai_features_enabled' => ['boolean'],
            'ai_provider' => ['required', 'string', 'in:openai,anthropic'],
            'ai_model' => ['required', 'string', 'max:100'],
            'ai_quote_drafting' => ['boolean'],
            'ai_email_drafting' => ['boolean'],
            'ai_report_summaries' => ['boolean'],
            'ai_contact_categorisation' => ['boolean'],
            'ai_public_enabled' => ['boolean'],
            'ai_chatbot_enabled' => ['boolean'],
            'ai_chatbot_greeting' => ['nullable', 'string', 'max:300'],
            'ai_chatbot_max_messages' => ['required', 'integer', 'min:1', 'max:20'],
            'ai_contact_assist_enabled' => ['boolean'],
        ];
    }

    public function mount(): void
    {
        $this->ai_features_enabled = (bool) Setting::get('ai_features_enabled', false);
        $this->ai_provider = Setting::get('ai_provider', 'openai');
        $this->ai_model = Setting::get('ai_model', 'gpt-4o-mini');
        $this->ai_quote_drafting = (bool) Setting::get('ai_quote_drafting', true);
        $this->ai_email_drafting = (bool) Setting::get('ai_email_drafting', true);
        $this->ai_report_summaries = (bool) Setting::get('ai_report_summaries', true);
        $this->ai_contact_categorisation = (bool) Setting::get('ai_contact_categorisation', false);
        $this->ai_public_enabled = (bool) Setting::get('ai_public_enabled', false);
        $this->ai_chatbot_enabled = (bool) Setting::get('ai_chatbot_enabled', false);
        $this->ai_chatbot_greeting = Setting::get('ai_chatbot_greeting', 'Hi! I\'m the CCC virtual assistant. How can I help you today?');
        $this->ai_chatbot_max_messages = (int) Setting::get('ai_chatbot_max_messages', 8);
        $this->ai_contact_assist_enabled = (bool) Setting::get('ai_contact_assist_enabled', false);
    }

    public function updatedAiProvider(string $value): void
    {
        // Auto-select first model for provider
        $this->ai_model = static::$providers[$value]['models'][0] ?? 'gpt-4o-mini';
    }

    public function testConnection(): void
    {
        abort_unless(auth()->user()->can('settings.edit'), 403);

        $this->testingConnection = true;
        $this->testResult = null;

        try {
            // Quick prompt to verify the API key and model work
            $agent = new QuoteDrafter;
            $response = $agent->prompt('Say "OK" — this is a connection test.');
            $this->testPassed = str_contains(strtolower($response->text ?? ''), 'ok') || strlen($response->text ?? '') > 0;
            $this->testResult = $this->testPassed
                ? 'Connection successful. Model responded: "'.substr($response->text, 0, 80).'"'
                : 'Connection failed — empty response from model.';
        } catch (\Throwable $e) {
            $this->testPassed = false;
            $this->testResult = 'Connection failed: '.$e->getMessage();
        } finally {
            $this->testingConnection = false;
        }
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can('settings.edit'), 403);

        $this->validate();

        $fields = [
            'ai_features_enabled' => ['value' => $this->ai_features_enabled,        'type' => 'boolean'],
            'ai_provider' => ['value' => $this->ai_provider,                 'type' => 'string'],
            'ai_model' => ['value' => $this->ai_model,                    'type' => 'string'],
            'ai_quote_drafting' => ['value' => $this->ai_quote_drafting,           'type' => 'boolean'],
            'ai_email_drafting' => ['value' => $this->ai_email_drafting,           'type' => 'boolean'],
            'ai_report_summaries' => ['value' => $this->ai_report_summaries,         'type' => 'boolean'],
            'ai_contact_categorisation' => ['value' => $this->ai_contact_categorisation,   'type' => 'boolean'],
            'ai_public_enabled' => ['value' => $this->ai_public_enabled,           'type' => 'boolean'],
            'ai_chatbot_enabled' => ['value' => $this->ai_chatbot_enabled,          'type' => 'boolean'],
            'ai_chatbot_greeting' => ['value' => $this->ai_chatbot_greeting,         'type' => 'string'],
            'ai_chatbot_max_messages' => ['value' => $this->ai_chatbot_max_messages,     'type' => 'integer'],
            'ai_contact_assist_enabled' => ['value' => $this->ai_contact_assist_enabled,   'type' => 'boolean'],
        ];

        foreach ($fields as $key => $data) {
            Setting::set($key, $data['value'], $data['type']);
        }

        $this->toastSuccess('AI settings saved successfully.');
    }

    public function render(): View
    {
        return view('livewire.settings.ai-settings');
    }
}
