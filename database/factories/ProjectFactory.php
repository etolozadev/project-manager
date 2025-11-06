<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => 'Proyecto ' . fake()->word(),
            'start_date' => now(),
            'end_date' => now()->addWeeks(fake()->numberBetween(2, 8)),
            'status' => fake()->randomElement(['planning', 'in_progress', 'completed']),
            'notes' => fake()->sentence(),
        ];
    }
}
