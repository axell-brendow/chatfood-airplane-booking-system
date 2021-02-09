<?php

namespace Database\Factories;

use App\Models\Aircraft;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'flight_id' => Flight::factory(),
            'seat_id' => Seat::factory(),
        ];
    }
}
