<?php

namespace Modules\Invoicify\Support\Pdf;

use Modules\Invoicify\Views\InvoicePdfView;

/** @todo additional methods may require that accepts data needed to generate the path needed for specific
 * team or user and return the it the getPath method or using DI pattern.
 */
class DefaultPathGenerator implements PathGenerator
{
    public function getPath(InvoicePdfView $view): string
    {
        return storage_path("app/invoices/{$view->invoiceDetails->invoiceNo}.pdf");
    }

    public function getTempPath(InvoicePdfView $view): string
    {
        return storage_path("app/invoices/temp/{$view->invoiceDetails->invoiceNo}.pdf");
    }
}
