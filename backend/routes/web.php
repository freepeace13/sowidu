<?php

use App\Http\Controllers\ChangeSessionLocale;
use App\Http\Controllers\ImpressumController;
use Illuminate\Support\Facades\Route;

// @TODO uncomment - RegisterController not exist!
// Route::get('/account/confirmation', [RegisterController::class, 'confirmation']);

Route::post('/change-language', ChangeSessionLocale::class)->name('change-language');

require base_path('routes/public.php');
require base_path('routes/auth.php');
require base_path('routes/admin.php');
require base_path('routes/account.php');
require base_path('routes/base.php');
// require base_path('routes/chat.php');
require base_path('routes/media.php');
// require base_path('routes/todo.php');
require base_path('routes/addressbook.php');
require base_path('routes/order.php');
// require base_path('routes/work_log.php');
// require base_path('routes/catalog.php');
// require base_path('routes/delivery_ticket.php');

/**
 * JSON routes
 */
require base_path('routes/json.php');

// require base_path('routes/admin.php');

if (config('reminder.enabled')) {
    require base_path('routes/reminders.php');
}

// Public pages
Route::get('impressum', ImpressumController::class)->name('impressum');

// Route::view('{path}', 'index')->where('path', '^(?!api).*$');
