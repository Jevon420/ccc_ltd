<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Commercial Cleaning',        'description' => 'Professional cleaning services for commercial properties and offices.'],
            ['name' => 'Residential Cleaning',       'description' => 'Cleaning services for homes and residential properties.'],
            ['name' => 'Post-Construction Clean',    'description' => 'Thorough cleaning of properties following construction or renovation work.'],
            ['name' => 'Deep Cleaning',              'description' => 'Intensive cleaning covering all surfaces, fixtures, and hard-to-reach areas.'],
            ['name' => 'Carpet Cleaning',            'description' => 'Professional carpet and upholstery cleaning services.'],
            ['name' => 'Window Cleaning',            'description' => 'Interior and exterior window cleaning for residential and commercial properties.'],
            ['name' => 'Janitorial Services',        'description' => 'Ongoing daily or weekly janitorial and maintenance cleaning.'],
            ['name' => 'Land Maintenance',           'description' => 'Grass cutting, vegetation control, drainage, and site upkeep.'],
            ['name' => 'Debris Removal',             'description' => 'Professional debris clearance for residential, commercial, and disaster-recovery sites.'],
            ['name' => 'Rural Development',          'description' => 'Rural infrastructure, agricultural land preparation, and community development.'],
            ['name' => 'Development Advisory',       'description' => 'Expert guidance on construction, land development, and project feasibility.'],
            ['name' => 'International Metal Trading', 'description' => 'Licensed international trading of ferrous and non-ferrous metals.'],
        ];

        foreach ($types as $type) {
            ServiceType::firstOrCreate(['name' => $type['name']], $type);
        }

        $this->command->info('Service types seeded ('.count($types).' types).');
    }
}
