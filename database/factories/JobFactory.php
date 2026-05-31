<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'service_type_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'location' => fake()->streetAddress().', '.fake()->randomElement(['Port of Spain', 'San Fernando', 'Chaguanas']),
            'status' => fake()->randomElement(['pending', 'scheduled', 'in_progress', 'completed', 'cancelled']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'scheduled_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => 'in_progress']);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'completed_date' => now(),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn () => ['priority' => 'urgent']);
    }
}
