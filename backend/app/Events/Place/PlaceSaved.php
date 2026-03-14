<?php

namespace App\Events\Place;

use App\Models\Place;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlaceSaved
{
    use Dispatchable, SerializesModels;

    public function __construct(public Place $place)
    {
        //
    }
}
