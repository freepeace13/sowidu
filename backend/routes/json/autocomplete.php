<?php

use App\Http\Controllers\Json\Autocomplete\AddressbookAutocompleteController;
use App\Http\Controllers\Json\Autocomplete\AddressController;
use App\Http\Controllers\Json\Autocomplete\JobtitleController;
use App\Http\Controllers\Json\Autocomplete\OrderAutocompleteController;
use App\Http\Controllers\Json\Autocomplete\PlaceController;
use Illuminate\Support\Facades\Route;

/**
 * /place/autocomplete?field=house_number&text=test&size=10 - Give 10 house_numbers matches search text
 * /place/autocomplete?field=street&text=test&size=10 - Give 10 streets matches search text
 * /place/autocomplete?field=zipcode&text=test&size=10 - Give 10 zipcodes matches search text
 */
Route::get('/place/autocomplete', PlaceController::class)
    ->name('json.autocomplete.place');

/**
 * /jobtitle/autocomplete?text=test&size=10 - Give 10 jobtitle matches search text
 */
Route::get('/jobtitle/autocomplete', JobtitleController::class)
    ->name('json.autocomplete.jobtitle');

/**
 * /address/autocomplete?text=query&size=10 - Give 10 address that matches query text
 */
Route::get('/address/autocomplete', AddressController::class)
    ->name('json.autocomplete.address');

Route::get('/addressbook/autocomplete', AddressbookAutocompleteController::class)
    ->name('json.autocomplete.addressbook');

Route::get('/orders/autocomplete', OrderAutocompleteController::class)
    ->name('json.autocomplete.orders');
