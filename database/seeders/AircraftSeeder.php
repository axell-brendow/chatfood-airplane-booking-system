<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use Illuminate\Database\Seeder;

class AircraftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Aircraft::factory(1)->create();
    }
}
