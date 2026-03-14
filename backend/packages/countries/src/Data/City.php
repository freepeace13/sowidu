<?php

namespace Packages\Countries\Data;

use Illuminate\Support\Arr;
use Packages\Data\Data;

class City extends Data
{
    protected $fillable = [
        'name',
        'country',
    ];

    public static function from($payload): City
    {
        return new self([
            'name' => Arr::get($payload, 'name'),
            'country' => Arr::get($payload, 'cca2'),
        ]);
    }
}
