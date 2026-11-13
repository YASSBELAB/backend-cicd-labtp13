<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'total_amount' => fake()->randomFloat(2, 10, 2000),
            'status' => fake()->randomElement([
                'pending',
                'paid',
                'shipped',
                'cancelled',
            ]),
        ];
    }
}
