<?php

use Illuminate\Support\Facades\Route;
use Modules\WorkLogs\Http\Controllers\GetEmployeeWorkLogsController;
use Modules\WorkLogs\Http\Controllers\ManualEntryWorkLogController;
use Modules\WorkLogs\Http\Controllers\WorkLogIndexController;

Route::get('', WorkLogIndexController::class)->name('index');
Route::get('json', GetEmployeeWorkLogsController::class)->name('json.get');

Route::post('store', [ManualEntryWorkLogController::class, 'store'])->name('manual_entries.store');
Route::patch('{workLog}/update', [ManualEntryWorkLogController::class, 'update'])->name('manual_entries.update');
Route::delete('{workLog}/destroy', [ManualEntryWorkLogController::class, 'destroy'])->name('manual_entries.destroy');
Route::patch('{workLog}/update-payment-form', [ManualEntryWorkLogController::class, 'updatePaymentForm'])->name('manual_entries.updatePaymentForm');
