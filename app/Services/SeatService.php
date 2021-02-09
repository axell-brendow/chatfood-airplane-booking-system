<?php

namespace App\Services;

use App\Models\Seat;

class SeatService extends BaseCrudService
{
    private $rules = [
        'name' => 'required|string|min:2|max:3',
    ];

    public function model(): string
    {
        return Seat::class;
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
