<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MultipleBookingService;
use Illuminate\Http\Request;

class MultipleBookingController extends Controller
{
    private $service;

    public function __construct(MultipleBookingService $service)
    {
        $this->service = $service;
    }

    public function book(Request $request)
    {
        return $this->service->book($request->all());
    }
}
