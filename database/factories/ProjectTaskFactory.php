<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectTaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'estimated_hours' => fake()->randomFloat(1, 2, 40),
            'spent_hours' => fake()->randomFloat(1, 0, 40),
            'status' => fake()->randomElement(['pending', 'in_progress', 'done']),
        ];
    }
}
