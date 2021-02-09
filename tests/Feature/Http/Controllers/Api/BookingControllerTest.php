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

class BookingControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /** @var Booking */
    private $booking;

    public function testIndex()
    {
        $booking = Booking::factory()->create();
        $response = $this->get(route('bookings.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$booking->toArray()]);
    }

    public function testShow()
    {
        $booking = Booking::factory()->create();
        $response = $this->get(route('bookings.show', ['booking' => $booking->id]));

        $response
            ->assertStatus(200)
            ->assertJson($booking->toArray());
    }

    public function testStoreWithInvalidRelations()
    {
        $this->expectException(\Exception::class);
        $data = [
            'user_id' => 0,
            'flight_id' => 0,
            'seat_id' => 0,
        ];
        $this->assertStore(
            $data,
            $data + ['deleted_at' => null]
        );
    }

    public function testStore()
    {
        $data = [
            'user_id' => User::factory()->create()->id,
            'flight_id' => Flight::factory()->create()->id,
            'seat_id' => Seat::factory()->create()->id,
        ];
        $response = $this->assertStore(
            $data,
            $data + ['deleted_at' => null]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testUpdateWithInvalidRelations()
    {
        $this->expectException(\Exception::class);
        $data = [
            'user_id' => User::factory()->create()->id,
            'flight_id' => Flight::factory()->create()->id,
            'seat_id' => Seat::factory()->create()->id,
        ];
        $newBookingData = array_merge($data, ['seat_id' => 0]);
        $this->booking = Booking::factory()->create($data);
        $this->assertUpdate(
            $newBookingData,
            $newBookingData + ['deleted_at' => null],
            $newBookingData + ['deleted_at' => null]
        );
    }

    public function testUpdate()
    {
        $data = [
            'user_id' => User::factory()->create()->id,
            'flight_id' => Flight::factory()->create()->id,
            'seat_id' => Seat::factory()->create()->id,
        ];
        $newBookingData = array_merge($data, ['seat_id' => Seat::factory()->create()->id]);
        $this->booking = Booking::factory()->create($data);
        $response = $this->assertUpdate(
            $newBookingData,
            $newBookingData + ['deleted_at' => null],
            $newBookingData + ['deleted_at' => null]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testDestroy()
    {
        $booking = Booking::factory()->create();
        $response = $this->json(
            'DELETE',
            route('bookings.destroy', ['booking' => $booking->id])
        );
        $response->assertStatus(204);

        $this->assertNull(Booking::find($booking->id));
        $this->assertNotNull(Booking::withTrashed()->find($booking->id));
    }

    protected function routeStore(): string
    {
        return route('bookings.store');
    }

    protected function routeUpdate(): string
    {
        return route('bookings.update', ['booking' => $this->booking->id]);
    }

    protected function model(): string
    {
        return Booking::class;
    }
}
