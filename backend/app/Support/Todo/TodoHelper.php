<?php

namespace App\Support\Todo;

use Carbon\CarbonInterval;

class TodoHelper
{
    public static function durationForHumans(string $time): string
    {
        return CarbonInterval::createFromFormat('H:i:s', $time)->forHumans(['short' => true]);
    }
}
