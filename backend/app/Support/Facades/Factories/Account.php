<?php

namespace App\Support\Facades\Factories;

use App\Contracts\AccountProvider;
use Illuminate\Support\Facades\Facade;

class Account extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return AccountProvider::class;
    }
}
