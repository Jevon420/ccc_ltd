<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('settings.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'settings'                    => ['required', 'array'],
            'settings.company_name'       => ['sometimes', 'string', 'max:255'],
            'settings.company_email'      => ['sometimes', 'email', 'max:255'],
            'settings.company_phone'      => ['sometimes', 'string', 'max:50'],
            'settings.company_address'    => ['sometimes', 'string', 'max:500'],
            'settings.company_slogan'     => ['sometimes', 'string', 'max:500'],
            'settings.company_motto'      => ['sometimes', 'string', 'max:255'],
            'settings.public_site_enabled'      => ['sometimes', 'boolean'],
            'settings.quote_requests_enabled'   => ['sometimes', 'boolean'],
            'settings.ai_features_enabled'      => ['sometimes', 'boolean'],
            'settings.onboarding_tours_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
