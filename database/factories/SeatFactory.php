<?php

namespace Database\Factories;

use App\Models\Aircraft;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Seat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        $letter = $letters[array_rand($letters)];

        return [
            'name' => "{$letter}{$this->faker->numberBetween(1, 6)}",
            'aircraft_id' => Aircraft::factory()
        ];
    }
}
