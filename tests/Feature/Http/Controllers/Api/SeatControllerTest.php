<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Aircraft;
use App\Models\Seat;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class SeatControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /** @var Seat */
    private $seat;

    public function testIndex()
    {
        $seat = Seat::factory()->create();
        $response = $this->get(route('seats.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$seat->toArray()]);
    }

    public function testShow()
    {
        $seat = Seat::factory()->create();
        $response = $this->get(route('seats.show', ['seat' => $seat->id]));

        $response
            ->assertStatus(200)
            ->assertJson($seat->toArray());
    }

    public function testValidationNameRequired()
    {
        $data = [
            'name' => null,
            'aircraft_id' => Aircraft::factory()->create()->id
        ];
        $this->assertInvalidationInStoreAction($data, 'name', 'required');
        $this->seat = Seat::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'name', 'required');
    }

    public function testValidationNameMinSize()
    {
        $data = [
            'name' => 'A',
            'aircraft_id' => Aircraft::factory()->create()->id
        ];
        $this->assertInvalidationInStoreAction($data, 'name', 'min.string', ['min' => 2]);
        $this->seat = Seat::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'name', 'min.string', ['min' => 2]);
    }

    public function testValidationNameMaxSize()
    {
        $data = [
            'name' => 'A100',
            'aircraft_id' => Aircraft::factory()->create()->id
        ];
        $this->assertInvalidationInStoreAction($data, 'name', 'max.string', ['max' => 3]);
        $this->seat = Seat::factory()->create();
        $this->assertInvalidationInUpdateAction($data, 'name', 'max.string', ['max' => 3]);
    }

    public function testStoreWithInvalidRelations()
    {
        $this->expectException(\Exception::class);
        $data = [
            'name' => 'A1',
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
            'name' => 'A1',
            'aircraft_id' => Aircraft::factory()->create()->id
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
            'name' => 'A1',
            'aircraft_id' => Aircraft::factory()->create()->id
        ];
        $newSeatData = array_merge($data, ['name' => 'A2']);
        $this->seat = Seat::factory()->create($data);
        $response = $this->assertUpdate(
            $newSeatData,
            $newSeatData + ['deleted_at' => null],
            $newSeatData + ['deleted_at' => null]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testDestroy()
    {
        $seat = Seat::factory()->create();
        $response = $this->json(
            'DELETE',
            route('seats.destroy', ['seat' => $seat->id])
        );
        $response->assertStatus(204);

        $this->assertNull(Seat::find($seat->id));
        $this->assertNotNull(Seat::withTrashed()->find($seat->id));
    }

    protected function routeStore(): string
    {
        return route('seats.store');
    }

    protected function routeUpdate(): string
    {
        return route('seats.update', ['seat' => $this->seat->id]);
    }

    protected function model(): string
    {
        return Seat::class;
    }
}
