<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->word(),
            'price' => fake()->randomFloat(2, 5, 200),
            'stock' => fake()->numberBetween(0, 100),
            'description' => fake()->sentence(),
        ];
    }
}
