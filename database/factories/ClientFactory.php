<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['company', 'individual']);

        return [
            'type' => $type,
            'name' => $type === 'company' ? fake()->company() : fake()->name(),
            'contact_person' => $type === 'company' ? fake()->name() : null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['Port of Spain', 'San Fernando', 'Chaguanas', 'Arima', 'Tobago']),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function company(): static
    {
        return $this->state(fn () => [
            'type' => 'company',
            'name' => fake()->company(),
            'contact_person' => fake()->name(),
        ]);
    }

    public function individual(): static
    {
        return $this->state(fn () => [
            'type' => 'individual',
            'name' => fake()->name(),
            'contact_person' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
