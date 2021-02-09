<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AircraftSeeder::class);
        $this->call(SeatSeeder::class);
        $this->call(FlightSeeder::class);
        $this->call(BookingSeeder::class);
    }
}
