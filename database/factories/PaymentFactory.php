<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'method' => fake()->randomElement(['cash', 'bank_transfer', 'cheque', 'card', 'wipay']),
            'status' => 'confirmed',
            'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'transaction_reference' => fake()->optional()->bothify('TXN-########'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
