<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_name' => fake()->randomElement([
                'iPhone 14 Pro Max', 
                'iPhone 13', 
                'Macbook Pro 16 inch',
                'iPad Pro 2022', 
                'Dell XPS 2022',
                'Dell Inspiron',
                'LG Gram',
                'Asus Zenbook'
            ]),
            'product_price' => fake()->numberBetween(400, 5000),
            'description' => fake()->paragraph(),
            'is_sale' => round(rand(0, 2)),
        ];
    }
}
