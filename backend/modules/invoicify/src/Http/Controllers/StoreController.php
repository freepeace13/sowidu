<?php

namespace Modules\Invoicify\Http\Controllers;

use Modules\Shared\Http\Controllers\InertiaController;

class StoreController extends InertiaController
{
    public function __invoke()
    {
        return static::render('invoicify::invoice-list');
    }
}
