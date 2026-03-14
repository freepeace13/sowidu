<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface ReminderHandler
{
    public function handle(Request $request, $teamId = null);
}
