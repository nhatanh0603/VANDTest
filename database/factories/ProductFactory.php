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
    public function definition(): array
    {
        $temp_name = fake()->unique()->sentence(rand(3,6));
        $temp_name = substr($temp_name, 0, strlen($temp_name) - 1);
        $product_name = $temp_name;
        $product_slug = substr($temp_name, 0, 50);

        return [
            'slug' => strtolower(str_replace(' ', '_', $product_slug)),
            'display_name' => ucwords($product_name),
            'description' => fake()->text(rand(400,600)),
            'price' => fake()->randomFloat(2, 10, 5000),
            'stock' => fake()->numberBetween(1000, 20000),
            'created_at' => fake()->dateTimeBetween('-9 years', '-1 years'),
            'updated_at' => fake()->dateTimeBetween('-6 months')
        ];
    }
}
