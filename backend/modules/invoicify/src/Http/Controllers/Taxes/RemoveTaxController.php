<?php

namespace Modules\Invoicify\Http\Controllers\Taxes;

use Modules\Shared\Http\Controllers\InertiaController;

class RemoveTaxController extends InertiaController
{
    public function __invoke()
    {
        return static::render('invoicify::taxes.remove');
    }
}
