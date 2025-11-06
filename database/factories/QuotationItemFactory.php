<?php

namespace Database\Factories;

use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unit = fake()->randomFloat(2, 50000, 200000);
        return [
            'quotation_id' => Quotation::factory(),
            'concept' => fake()->sentence(3),
            'quantity' => $quantity,
            'unit_price' => $unit,
            'total' => $quantity * $unit,
            'type' => fake()->randomElement(['service', 'resource', 'other']),
        ];
    }
}
