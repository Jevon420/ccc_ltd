<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Company Info
            ['key' => 'company_name',    'value' => 'Constructive Cleaning Company LTD', 'type' => 'string',  'group' => 'company',   'is_public' => true,  'description' => 'Official company name'],
            ['key' => 'company_email',   'value' => 'info@constructivecleaningco.com',   'type' => 'string',  'group' => 'company',   'is_public' => true,  'description' => 'Primary contact email'],
            ['key' => 'company_phone',   'value' => '+1 (868) 000-0000',                 'type' => 'string',  'group' => 'company',   'is_public' => true,  'description' => 'Primary contact phone'],
            ['key' => 'company_address', 'value' => 'Trinidad & Tobago',                 'type' => 'string',  'group' => 'company',   'is_public' => true,  'description' => 'Company address'],
            ['key' => 'company_slogan',  'value' => 'Efficiency, Constructiveness & Unity — As Far As The Eyes Can See', 'type' => 'string', 'group' => 'company', 'is_public' => true, 'description' => 'Company slogan'],
            ['key' => 'company_motto',   'value' => 'Sufficient, Effective, Success',    'type' => 'string',  'group' => 'company',   'is_public' => true,  'description' => 'Company motto'],
            ['key' => 'logo_path',       'value' => 'images/ccc_ops_logo.png',           'type' => 'string',  'group' => 'media',     'is_public' => true,  'description' => 'Company logo path (relative to public/)'],

            // Feature Flags
            ['key' => 'public_site_enabled',      'value' => '1', 'type' => 'boolean', 'group' => 'features', 'is_public' => false, 'description' => 'Enable/disable public website'],
            ['key' => 'quote_requests_enabled',   'value' => '1', 'type' => 'boolean', 'group' => 'features', 'is_public' => false, 'description' => 'Allow quote requests from public site'],
            ['key' => 'ai_features_enabled',      'value' => '0', 'type' => 'boolean', 'group' => 'features', 'is_public' => false, 'description' => 'Enable AI-assisted features'],
            ['key' => 'onboarding_tours_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features', 'is_public' => false, 'description' => 'Enable guided onboarding tours'],
        ];

        foreach ($settings as $data) {
            Setting::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }

        $this->command->info('Company settings seeded.');
    }
}
