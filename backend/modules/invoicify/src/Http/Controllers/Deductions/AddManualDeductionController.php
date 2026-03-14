<?php

namespace Modules\Invoicify\Http\Controllers\Deductions;

use Modules\Shared\Http\Controllers\InertiaController;

class AddManualDeductionController extends InertiaController
{
    public function __invoke()
    {
        return static::render('invoicify::deductions.add-manual');
    }
}
