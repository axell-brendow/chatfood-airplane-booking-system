<?php

namespace App\Services;

use App\Models\Booking;

class BookingService extends BaseCrudService
{
    private $rules = [];

    public function model(): string
    {
        return Booking::class;
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
