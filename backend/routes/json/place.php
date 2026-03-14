<?php

use App\Http\Controllers\Json\Place\CountryController;
use Illuminate\Support\Facades\Route;

/**
 * /place/country - retrieve countries
 * /place/country/PH/state - retrieve country states
 * /place/country/PH/city - retrieve country cities
 */
Route::get('/place/country', [CountryController::class, 'index'])
    ->name('json.place.country');

Route::get('/place/country/{country}/state', [CountryController::class, 'states'])
    ->name('json.place.country.state');

Route::get('/place/country/{country}/city', [CountryController::class, 'cities'])
    ->name('json.place.country.city');
