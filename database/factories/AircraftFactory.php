<?php

namespace Database\Factories;

use App\Models\Aircraft;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;

class AircraftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Aircraft::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = [Aircraft::TYPE_SHORT_RANGE];
        return [
            'aircraft_type' => $types[array_rand($types)],
            'seats_count' => 156,
            'rows' => 26,
            'row_arrangement' => 'A B C _ D E F',
        ];
    }
}
