<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->userName(),
            'display_name' => fake()->unique()->company() . ' ' . fake()->companySuffix(),
            'description' => fake()->text(rand(300,500)),
            'created_at' => fake()->dateTimeBetween('-10 years', '-1 years'),
            'updated_at' => fake()->dateTimeBetween('-6 months')
        ];
    }
}
