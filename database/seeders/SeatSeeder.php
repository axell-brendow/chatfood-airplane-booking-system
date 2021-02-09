<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aircraft = Aircraft::first();
        $num_columns = substr_count($aircraft->row_arrangement, " ");
        $letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        for ($i = 0; $i < $aircraft->seats_count; $i++)
        {
            $row = intdiv($i, $num_columns);
            $column = $i % $num_columns;
            Seat::create([
                'name' => "{$letters[$column]}" . ($row + 1),
                'aircraft_id' => $aircraft->id
            ]);
        }
    }
}
