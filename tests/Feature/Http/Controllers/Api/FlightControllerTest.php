<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Aircraft;
use App\Models\Flight;
use App\Models\Seat;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class FlightControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /** @var Flight */
    private $flight;

    public function testIndex()
    {
        $flight = Flight::factory()->create();
        $response = $this->get(route('flights.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$flight->toArray()]);
    }

    public function testShow()
    {
        $flight = Flight::factory()->create();
        $response = $this->get(route('flights.show', ['flight' => $flight->id]));

        $response
            ->assertStatus(200)
            ->assertJson($flight->toArray());
    }

    public function testStoreWithInvalidRelations()
    {
        $this->expectException(\Exception::class);
        $data = [
            'aircraft_id' => 0,
        ];
        $this->assertStore(
            $data,
            $data + ['deleted_at' => null]
        );
    }

    public function testStore()
    {
        $data = [
            'aircraft_id' => Aircraft::factory()->create()->id,
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
            'aircraft_id' => Aircraft::factory()->create()->id,
        ];
        $newFlightData = array_merge($data, ['aircraft_id' => 0]);
        $this->flight = Flight::factory()->create($data);
        $this->assertUpdate(
            $newFlightData,
            $newFlightData + ['deleted_at' => null],
            $newFlightData + ['deleted_at' => null]
        );
    }

    public function testUpdate()
    {
        $data = [
            'aircraft_id' => Aircraft::factory()->create()->id,
        ];
        $newFlightData = array_merge($data, ['aircraft_id' => Aircraft::factory()->create()->id]);
        $this->flight = Flight::factory()->create($data);
        $response = $this->assertUpdate(
            $newFlightData,
            $newFlightData + ['deleted_at' => null],
            $newFlightData + ['deleted_at' => null]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testDestroy()
    {
        $flight = Flight::factory()->create();
        $response = $this->json(
            'DELETE',
            route('flights.destroy', ['flight' => $flight->id])
        );
        $response->assertStatus(204);

        $this->assertNull(Flight::find($flight->id));
        $this->assertNotNull(Flight::withTrashed()->find($flight->id));
    }

    protected function routeStore(): string
    {
        return route('flights.store');
    }

    protected function routeUpdate(): string
    {
        return route('flights.update', ['flight' => $this->flight->id]);
    }

    protected function model(): string
    {
        return Flight::class;
    }
}
