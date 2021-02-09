<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class MultipleBookingControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testBookingOnSameRow()
    {
        $flight = Flight::factory()->create();
        $user = User::factory()->create();
        $response = $this->json('POST', route('multiple_bookings.book'), [
            'flight_id' => $flight->id,
            'user_id' => $user->id,
            'num_seats' => 3
        ]);

        $response->assertStatus(200);
        $this->assertEquals($response->decodeResponseJson()->count(), 3);
    }
}
