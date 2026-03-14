<?php

namespace App\Listeners\Order\UsedProduct;

use App\Events\Order\AddedOrderProduct;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddNewUsedProductOnCurrentInvoice implements ShouldQueue
{
    public function handle(AddedOrderProduct $event)
    {
        $order = $event->order;
        $causer = $event->causer;
        $orderProduct = $event->orderProduct;

        // Find order invoices that have a status of `Draft`
        $orderDraftInvoices = $order->invoices()
            ->draft()
            ->get();

        if ($orderDraftInvoices->isEmpty()) {
            return;
        }

        if ($orderDraftInvoices->count() > 1) {
            return; // Let user decide where to attach this product
        }

        $draftInvoice = $orderDraftInvoices->first();

        if (InvoiceService::run($draftInvoice)->itemsCannotBeUpdated()
        ) {
            return;
        }

        InvoiceService::run($draftInvoice)
            ->addUsedProduct($causer, $orderProduct);
    }
}
