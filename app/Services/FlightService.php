<?php

namespace App\Services;

use App\Models\Flight;

class FlightService extends BaseCrudService
{
    private $rules = [];

    public function model(): string
    {
        return Flight::class;
    }

    public function rulesStore(): array
    {
        return $this->rules;
    }

    public function rulesUpdate(): array
    {
        return $this->rules;
    }
}
