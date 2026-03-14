<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;
use Illuminate\Container\Container;

trait RemindersTrait
{
    protected function setRemindAfter(string $id, int $duration)
    {
        $request = Container::getInstance()->make('request');

        $future = Carbon::now()
            ->addSeconds($duration)
            ->toDateTimeString();

        $request->session()->put($id, $future);
    }
}
