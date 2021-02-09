<?php

namespace App\Providers;

use App\Http\Controllers\Api\AircraftController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\MultipleBookingController;
use App\Http\Controllers\Api\SeatController;
use App\Services\AircraftService;
use App\Services\BookingService;
use App\Services\FlightService;
use App\Services\MultipleBookingService;
use App\Services\SeatService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AircraftController::class, function () {
            return new AircraftController(new AircraftService());
        });
        $this->app->bind(BookingController::class, function () {
            return new BookingController(new BookingService());
        });
        $this->app->bind(FlightController::class, function () {
            return new FlightController(new FlightService());
        });
        $this->app->bind(SeatController::class, function () {
            return new SeatController(new SeatService());
        });
        $this->app->bind(MultipleBookingController::class, function () {
            return new MultipleBookingController(new MultipleBookingService());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
