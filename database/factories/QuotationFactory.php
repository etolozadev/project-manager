<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'title' => 'Proyecto ' . fake()->word(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['draft', 'sent', 'accepted']),
            'total' => fake()->randomFloat(2, 500000, 5000000),
            'delivery_date' => fake()->date(),
        ];
    }
}
