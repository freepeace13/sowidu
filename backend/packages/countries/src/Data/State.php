<?php

namespace Packages\Countries\Data;

use Illuminate\Support\Arr;
use Packages\Data\Data;

class State extends Data
{
    protected $fillable = [
        'name',
        'country',
    ];

    public static function from($payload): State
    {
        return new self([
            'name' => Arr::get($payload, 'name'),
            'country' => Arr::get($payload, 'cca2'),
        ]);
    }
}
