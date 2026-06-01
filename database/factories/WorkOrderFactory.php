<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+14 days'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed', 'completed_at' => now()]);
    }
}
