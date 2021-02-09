<?php

namespace App\Services;

use App\Models\Aircraft;

class AircraftService extends BaseCrudService
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'rows' => 'required|numeric|min:1|max:50',
            'seats_count' => 'required|numeric|min:2|max:400',
            'row_arrangement' => 'required',
            'aircraft_type' => 'required|numeric|in:' . implode(
                    ',', [Aircraft::TYPE_SHORT_RANGE]
                )
        ];
    }

    public function model(): string
    {
        return Aircraft::class;
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
