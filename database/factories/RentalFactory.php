<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {

        return [
            'start_date' => fake()->dateTime($max = 'now', $timezone = null) ,
            'end_date' => fake()->dateTime($max = 'now', $timezone = null) ,
            'total_price' => fake()->numberBetween($min = 0, $max = 10000000) ,
            'user_id' => fake()->numberBetween($min = 1, $max = 10) ,
            'equipment_id' => fake()->numberBetween($min = 1, $max = 5) ,
        ];
    }
}
