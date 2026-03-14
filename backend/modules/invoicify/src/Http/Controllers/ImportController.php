<?php

namespace Modules\Invoicify\Http\Controllers;

use Modules\Shared\Http\Controllers\InertiaController;

class ImportController extends InertiaController
{
    public function __invoke()
    {
        return static::render('invoicify::import');
    }
}
