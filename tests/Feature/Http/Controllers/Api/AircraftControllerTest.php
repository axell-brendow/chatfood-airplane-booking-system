<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Aircraft;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class AircraftControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /** @var Aircraft */
    private $aircraft;

    public function testIndex()
    {
        $aircraft = Aircraft::factory()->create();
        $response = $this->get(route('aircraft.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$aircraft->toArray()]);
    }

    public function testShow()
    {
        $aircraft = Aircraft::factory()->create();
        $response = $this->get(route('aircraft.show', ['aircraft' => $aircraft->id]));

        $response
            ->assertStatus(200)
            ->assertJson($aircraft->toArray());
    }

    public function testValidationRowsRequired()
    {
        $data = [
            'rows' => null,
            'seats_count' => 156,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $this->assertInvalidationInStoreAction($data, 'rows', 'required');
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'rows', 'required');
    }

    public function testValidationRowsMinimum()
    {
        $data = [
            'rows' => 0,
            'seats_count' => 156,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $this->assertInvalidationInStoreAction($data, 'rows', 'min.numeric', ['min' => 1]);
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'rows', 'min.numeric', ['min' => 1]);
    }

    public function testValidationRowsMaximum()
    {
        $data = [
            'rows' => 51,
            'seats_count' => 156,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $this->assertInvalidationInStoreAction($data, 'rows', 'max.numeric', ['max' => 50]);
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'rows', 'max.numeric', ['max' => 50]);
    }

    public function testValidationSeatsCountRequired()
    {
        $data = [
            'rows' => 26,
            'seats_count' => null,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $this->assertInvalidationInStoreAction($data, 'seats_count', 'required');
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'seats_count', 'required');
    }

    public function testValidationSeatsCountMinimum()
    {
        $data = [
            'rows' => 26,
            'seats_count' => 1,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $this->assertInvalidationInStoreAction($data, 'seats_count', 'min.numeric', ['min' => 2]);
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'seats_count', 'min.numeric', ['min' => 2]);
    }

    public function testValidationSeatsCountMaximum()
    {
        $data = [
            'rows' => 26,
            'seats_count' => 401,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $this->assertInvalidationInStoreAction($data, 'seats_count', 'max.numeric', ['max' => 400]);
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'seats_count', 'max.numeric', ['max' => 400]);
    }

    public function testValidationRowArrangementRequired()
    {
        $data = [
            'rows' => 26,
            'seats_count' => 156,
            'row_arrangement' => null,
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $this->assertInvalidationInStoreAction($data, 'row_arrangement', 'required');
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'row_arrangement', 'required');
    }

    public function testValidationAircraftTypeRequired()
    {
        $data = [
            'rows' => 26,
            'seats_count' => 156,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => null
        ];
        $this->assertInvalidationInStoreAction($data, 'aircraft_type', 'required');
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'aircraft_type', 'required');
    }

    public function testValidationAircraftTypeEnum()
    {
        $data = [
            'rows' => 26,
            'seats_count' => 156,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => 999
        ];
        $this->assertInvalidationInStoreAction($data, 'aircraft_type', 'in');
        $this->aircraft = Aircraft::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'aircraft_type', 'in');
    }

    public function testStore()
    {
        $data = [
            'rows' => 26,
            'seats_count' => 156,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $response = $this->assertStore(
            $data,
            $data + ['deleted_at' => null]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testUpdate()
    {
        $data = [
            'rows' => 26,
            'seats_count' => 156,
            'row_arrangement' => 'A B C _ D E F',
            'aircraft_type' => Aircraft::TYPE_SHORT_RANGE
        ];
        $newAircraftData = array_merge($data, ['rows' => 25]);
        $this->aircraft = Aircraft::factory()->create($data);
        $response = $this->assertUpdate(
            $newAircraftData,
            $newAircraftData + ['deleted_at' => null],
            $newAircraftData + ['deleted_at' => null]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testDestroy()
    {
        $aircraft = Aircraft::factory()->create();
        $response = $this->json(
            'DELETE',
            route('aircraft.destroy', ['aircraft' => $aircraft->id])
        );
        $response->assertStatus(204);

        $this->assertNull(Aircraft::find($aircraft->id));
        $this->assertNotNull(Aircraft::withTrashed()->find($aircraft->id));
    }

    protected function routeStore(): string
    {
        return route('aircraft.store');
    }

    protected function routeUpdate(): string
    {
        return route('aircraft.update', ['aircraft' => $this->aircraft->id]);
    }

    protected function model(): string
    {
        return Aircraft::class;
    }
}
