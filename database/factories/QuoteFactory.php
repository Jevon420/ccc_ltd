<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'client_name' => fake()->company(),
            'client_email' => fake()->safeEmail(),
            'service_type' => fake()->randomElement(['Commercial Cleaning', 'Deep Cleaning', 'Land Maintenance', 'Debris Removal']),
            'job_details' => fake()->paragraph(),
            'status' => fake()->randomElement(['draft', 'sent', 'accepted', 'declined']),
            'amount' => fake()->optional()->randomFloat(2, 500, 20000),
            'valid_until' => fake()->optional()->dateTimeBetween('now', '+60 days'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function sent(): static
    {
        return $this->state(fn () => ['status' => 'sent']);
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => 'accepted']);
    }
}
