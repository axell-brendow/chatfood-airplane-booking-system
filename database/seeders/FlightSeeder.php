<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Flight;
use Illuminate\Database\Seeder;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Flight::factory(3)->create([
            'aircraft_id' => Aircraft::first()->id
        ]);
    }
}
