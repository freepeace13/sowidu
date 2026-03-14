<?php

use App\Http\Controllers\Json\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/profile', [ProfileController::class, 'show'])
    ->name('json.profile.show');
