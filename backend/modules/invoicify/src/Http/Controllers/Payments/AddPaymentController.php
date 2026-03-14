<?php

namespace Modules\Invoicify\Http\Controllers\Payments;

use Modules\Shared\Http\Controllers\InertiaController;

class AddPaymentController extends InertiaController
{
    public function __invoke()
    {
        return static::render('invoicify::payments.store');
    }
}
