<?php

namespace Modules\Invoicify\Support\Pdf;

use Modules\Invoicify\Views\InvoicePdfView;

interface PathGenerator
{
    public function getPath(InvoicePdfView $view): string;

    public function getTempPath(InvoicePdfView $view): string;
}
