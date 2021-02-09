<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::resource('aircraft', 'AircraftController', ['except' => ['create', 'edit']]);
    Route::resource('bookings', 'BookingController', ['except' => ['create', 'edit']]);
    Route::resource('flights', 'FlightController', ['except' => ['create', 'edit']]);
    Route::resource('seats', 'SeatController', ['except' => ['create', 'edit']]);
    Route::post('multiple_bookings', 'Api\MultipleBookingController@book')
        ->name('multiple_bookings.book');
});
