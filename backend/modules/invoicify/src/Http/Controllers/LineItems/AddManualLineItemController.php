<?php

namespace Modules\Invoicify\Http\Controllers\LineItems;

use Modules\Shared\Http\Controllers\InertiaController;

class AddManualLineItemController extends InertiaController
{
    public function __invoke()
    {
        return static::render('invoicify::line-items.add-manual');
    }
}
