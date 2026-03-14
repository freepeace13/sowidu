<?php

namespace App\Listeners\Invoice;

use App\Events\Invoice\InvoiceLayoutChanged;
use App\Modules\Invoice\InvoiceService;

class ResetInvoiceLayout
{
    public function handle(InvoiceLayoutChanged $event)
    {
        InvoiceService::run($event->invoice)->resetPreviewLayout();
    }
}
