<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $aircraft = Aircraft::first();
//        $seats_ids = $aircraft->seats->pluck('id')->toArray();
//        Booking::factory(10)->create([
//            'flight_id' => Flight::first()->id,
//            'seat_id' => $seats_ids[array_rand($seats_ids)]
//        ]);
    }
}
