<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Database\Seeders\SeatSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MultipleBookingControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testBookingOnSameRow()
    {
        $flight = Flight::factory()->create();
        (new SeatSeeder())->run();
        $response = $this->json('POST', route('multiple_bookings.book'), [
            'flight_id' => $flight->id,
            'user_id' => User::factory()->create()->id,
            'num_seats' => 3
        ]);
        $response->assertStatus(200);

        $seatsNames = array_map(function ($booking) {
            return $booking['seat']['name'];
        }, json_decode($response->content(), true));

        $this->assertTrue(
            in_array("A1", $seatsNames)
            && in_array("B1", $seatsNames)
            && in_array("C1", $seatsNames)
        );
    }

    public function testBookingAcrossRows()
    {
        $flight = Flight::factory()->create();
        (new SeatSeeder())->run();
        $response = $this->json('POST', route('multiple_bookings.book'), [
            'flight_id' => $flight->id,
            'user_id' => User::factory()->create()->id,
            'num_seats' => 4
        ]);
        $response->assertStatus(200);

        $seatsNames = array_map(function ($booking) {
            return $booking['seat']['name'];
        }, json_decode($response->content(), true));

        $this->assertTrue(
            in_array("A1", $seatsNames)
            && in_array("B1", $seatsNames)
            && in_array("A2", $seatsNames)
            && in_array("B2", $seatsNames)
        );
    }

    private function getSeatsNearTheWindow(int $rows)
    {
        $seatsNames = [];
        for ($i = 1; $i <= $rows; $i++) array_push($seatsNames, "A$i", "F$i");
        return $seatsNames;
    }

    public function testBookingNearbyAcrossTheAisle()
    {
        $flight = Flight::factory()->create();
        (new SeatSeeder())->run();

        $seatsIds = $flight->aircraft->seats
            ->whereIn('name', $this->getSeatsNearTheWindow($flight->aircraft->rows))
            ->pluck('id')->toArray();

        $user = User::factory()->create();
        foreach ($seatsIds as $seatId)
        {
            Booking::create([
                'user_id' => $user->id,
                'flight_id' => $flight->id,
                'seat_id' => $seatId,
            ]);
        }

        $response = $this->json('POST', route('multiple_bookings.book'), [
            'flight_id' => $flight->id,
            'user_id' => $user->id,
            'num_seats' => 4
        ]);
        echo $response->content();
        $response->assertStatus(200);

        $seatsNames = array_map(function ($booking) {
            return $booking['seat']['name'];
        }, json_decode($response->content(), true));

        $this->assertTrue(
            in_array("C1", $seatsNames)
            && in_array("D1", $seatsNames)
            && in_array("C2", $seatsNames)
            && in_array("D2", $seatsNames)
        );
    }

    private function getAlmostAllSeats(int $rows)
    {
        $seatsNames = [];
        for ($i = 1; $i < $rows; $i++)
            array_push($seatsNames, "A$i", "B$i", "C$i", "D$i", "E$i", "F$i");

        array_push($seatsNames, "A26", "C26", "D26", "F26");
        return $seatsNames;
    }

    public function testRandomBooking()
    {
        $flight = Flight::factory()->create();
        (new SeatSeeder())->run();

        $seatsIds = $flight->aircraft->seats
            ->whereIn('name', $this->getAlmostAllSeats($flight->aircraft->rows))
            ->pluck('id')->toArray();

        $user = User::factory()->create();
        foreach ($seatsIds as $seatId)
        {
            Booking::create([
                'user_id' => $user->id,
                'flight_id' => $flight->id,
                'seat_id' => $seatId,
            ]);
        }

        $response = $this->json('POST', route('multiple_bookings.book'), [
            'flight_id' => $flight->id,
            'user_id' => $user->id,
            'num_seats' => 2
        ]);
        echo $response->content();
        $response->assertStatus(200);

        $seatsNames = array_map(function ($booking) {
            return $booking['seat']['name'];
        }, json_decode($response->content(), true));

        $this->assertTrue(
            in_array("B26", $seatsNames)
            && in_array("E26", $seatsNames)
        );
    }
}
