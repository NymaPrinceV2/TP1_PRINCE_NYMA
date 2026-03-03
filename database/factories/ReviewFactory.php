<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {

        return [
            'rating' => fake()->numberBetween($min = -128, $max = 127) ,
            'comment' => fake()->text(),
            'user_id' => fake()->numberBetween($min = 1, $max = 10) ,
            'rental_id' => fake()->numberBetween($min = 1, $max = 10) ,
        ];
    }
}
