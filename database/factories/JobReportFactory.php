<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\JobReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobReport>
 */
class JobReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'work_performed' => fake()->optional()->paragraph(),
            'issues_encountered' => fake()->optional()->sentence(),
            'recommendations' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['draft', 'submitted', 'approved']),
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => ['status' => 'submitted']);
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }
}
