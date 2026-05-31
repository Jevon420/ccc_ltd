<?php

namespace Database\Factories;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceType>
 */
class ServiceTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Commercial Cleaning', 'Residential Cleaning', 'Post-Construction Clean',
                'Deep Cleaning', 'Carpet Cleaning', 'Window Cleaning',
                'Janitorial Services', 'Land Maintenance', 'Debris Removal',
                'Rural Development', 'Development Advisory', 'International Metal Trading',
            ]),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
