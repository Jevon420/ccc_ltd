<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 500, 15000);

        return [
            'client_id' => Client::factory(),
            'title' => 'Invoice — '.fake()->bs(),
            'description' => fake()->optional()->sentence(),
            'subtotal' => $subtotal,
            'tax_rate' => 0,
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'overdue']),
            'issue_date' => now()->subDays(fake()->numberBetween(1, 30)),
            'due_date' => now()->addDays(fake()->numberBetween(7, 30)),
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

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'amount_paid' => fn (array $attrs) => $attrs['subtotal'],
            'paid_date' => now(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => 'overdue',
            'due_date' => now()->subDays(5),
        ]);
    }
}
