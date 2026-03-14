<?php

namespace App\Support\Facades\Repositories;

use App\Repositories\CompanyRepository as Repository;
use Illuminate\Support\Facades\Facade;

class Company extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Repository::class;
    }
}
