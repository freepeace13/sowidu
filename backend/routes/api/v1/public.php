<?php

use App\Http\Api\Controllers\V1\Public\GetCountryCitiesController;
use App\Http\Api\Controllers\V1\Public\GetCountryListController;
use App\Http\Api\Controllers\V1\Public\GetCountryStatesController;
use App\Http\Api\Controllers\V1\Public\GetInstitutionTypesController;
use App\Http\Api\Controllers\V1\Public\GetLegalFormsController;
use Illuminate\Support\Facades\Route;

Route::get('/countries', GetCountryListController::class);
Route::get('/countries/{country}/states', GetCountryStatesController::class);
Route::get('/countries/{country}/cities', GetCountryCitiesController::class);
Route::get('/legal-forms', GetLegalFormsController::class);
Route::get('/institutions', GetInstitutionTypesController::class);
