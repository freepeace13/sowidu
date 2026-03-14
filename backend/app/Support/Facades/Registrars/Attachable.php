<?php

namespace App\Support\Facades\Registrars;

use App\Registrars\AttachableRegistrar as Registrar;
use Illuminate\Support\Facades\Facade;

class Attachable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Registrar::class;
    }
}
