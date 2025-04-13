<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'price' => $this->faker->randomFloat(2, 10, 500),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'images' => [],
        ];
    }
}
