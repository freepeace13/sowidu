<?php

use App\Http\Controllers\Inertia\Reminders\FormSubmit;
use App\Http\Controllers\Inertia\Reminders\SkipReminder;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth', 'inertia'],
], function () {
    Route::post('/reminders/{id}/skip', SkipReminder::class)
        ->name('reminders.skip');

    Route::post('/reminders/{id}', FormSubmit::class)
        ->name('reminders.submit');
});
