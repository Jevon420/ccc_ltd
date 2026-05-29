<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;

/**
 * Livewire Settings Form
 *
 * Handles real-time saving of company settings without a full page reload.
 * Uses Spatie Activitylog via the Setting model automatically.
 */
class SettingsForm extends Component
{
    // Company Info
    public string $company_name    = '';
    public string $company_email   = '';
    public string $company_phone   = '';
    public string $company_address = '';
    public string $company_slogan  = '';
    public string $company_motto   = '';

    // Feature Flags
    public bool $public_site_enabled      = true;
    public bool $quote_requests_enabled   = true;
    public bool $ai_features_enabled      = false;
    public bool $onboarding_tours_enabled = true;

    public bool $saved = false;

    public function mount(): void
    {
        $this->company_name    = Setting::get('company_name', '');
        $this->company_email   = Setting::get('company_email', '');
        $this->company_phone   = Setting::get('company_phone', '');
        $this->company_address = Setting::get('company_address', '');
        $this->company_slogan  = Setting::get('company_slogan', '');
        $this->company_motto   = Setting::get('company_motto', '');

        $this->public_site_enabled      = (bool) Setting::get('public_site_enabled', true);
        $this->quote_requests_enabled   = (bool) Setting::get('quote_requests_enabled', true);
        $this->ai_features_enabled      = (bool) Setting::get('ai_features_enabled', false);
        $this->onboarding_tours_enabled = (bool) Setting::get('onboarding_tours_enabled', true);
    }

    public function rules(): array
    {
        return [
            'company_name'    => ['required', 'string', 'max:255'],
            'company_email'   => ['required', 'email', 'max:255'],
            'company_phone'   => ['nullable', 'string', 'max:50'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_slogan'  => ['nullable', 'string', 'max:500'],
            'company_motto'   => ['nullable', 'string', 'max:255'],
            'public_site_enabled'      => ['boolean'],
            'quote_requests_enabled'   => ['boolean'],
            'ai_features_enabled'      => ['boolean'],
            'onboarding_tours_enabled' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->authorize('settings.edit');
        $this->validate();

        $fields = [
            'company_name'    => ['value' => $this->company_name,    'type' => 'string'],
            'company_email'   => ['value' => $this->company_email,   'type' => 'string'],
            'company_phone'   => ['value' => $this->company_phone,   'type' => 'string'],
            'company_address' => ['value' => $this->company_address, 'type' => 'string'],
            'company_slogan'  => ['value' => $this->company_slogan,  'type' => 'string'],
            'company_motto'   => ['value' => $this->company_motto,   'type' => 'string'],
            'public_site_enabled'      => ['value' => $this->public_site_enabled,      'type' => 'boolean'],
            'quote_requests_enabled'   => ['value' => $this->quote_requests_enabled,   'type' => 'boolean'],
            'ai_features_enabled'      => ['value' => $this->ai_features_enabled,      'type' => 'boolean'],
            'onboarding_tours_enabled' => ['value' => $this->onboarding_tours_enabled, 'type' => 'boolean'],
        ];

        foreach ($fields as $key => $data) {
            Setting::set($key, $data['value'], $data['type']);
        }

        $this->saved = true;

        $this->dispatch('settings-saved');
    }

    public function render()
    {
        return view('livewire.settings.settings-form');
    }
}
