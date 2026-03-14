<?php

namespace Modules\Invoicify\Http\Controllers\LineItems;

use Modules\Shared\Http\Controllers\InertiaController;

class UpdateManualLineItemController extends InertiaController
{
    public function __invoke()
    {
        return static::render('invoicify::line-items.update-manual');
    }
}
