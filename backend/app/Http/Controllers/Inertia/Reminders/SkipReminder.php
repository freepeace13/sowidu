<?php

namespace App\Http\Controllers\Inertia\Reminders;

use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\RemindersTrait;
use Illuminate\Http\Request;

class SkipReminder extends InertiaController
{
    use RemindersTrait;

    public function __invoke(Request $request)
    {
        $id = $request->route('id');
        $this->setRemindAfter($id, $request->duration); // seconds

        return back(303);
    }
}
