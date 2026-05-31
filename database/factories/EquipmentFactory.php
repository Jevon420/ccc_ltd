<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Excavator', 'Dump Truck', 'Pressure Washer', 'Generator', 'Chainsaw', 'Safety Harness', 'Scaffolding Kit']),
            'type' => fake()->randomElement(['vehicle', 'machinery', 'tool', 'ppe', 'other']),
            'serial_number' => fake()->optional()->bothify('SN-########'),
            'make_model' => fake()->optional()->bothify('???? Model ##'),
            'purchase_date' => fake()->optional()->dateTimeBetween('-5 years', 'now'),
            'condition' => fake()->randomElement(['excellent', 'good', 'fair', 'poor']),
            'status' => fake()->randomElement(['active', 'active', 'maintenance', 'retired']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function inMaintenance(): static
    {
        return $this->state(fn () => ['status' => 'maintenance']);
    }
}
