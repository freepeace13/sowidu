<?php

namespace Modules\Invoicify\Http\Controllers;

use Modules\Shared\Http\Controllers\InertiaController;

class PreviewPdfController extends InertiaController
{
    public function __invoke()
    {
        return static::render('invoicify::preview-pdf');
    }
}
