<?php

namespace App\Listeners\Order\UsedProduct;

use App\Events\Order\OrderProductUpdated;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductInvoiceItem implements ShouldQueue
{
    use InteractsWithQueue;

    public $afterCommit = true;

    public function handle(OrderProductUpdated $event)
    {
        $orderProduct = $event->orderProduct;

        // Get OrderProduct - invoiceItem
        $invoiceItem = $orderProduct->invoiceItem()
            ->with(['invoice'])
            ->first();

        // Invoice item not found
        if (!$invoiceItem) {
            return;
        }

        // Check invoice status is not `Draft`
        if (
            InvoiceService::run($invoiceItem->invoice)->itemsCannotBeUpdated()
        ) {
            return;
        }

        // Update invoice_item
        $invoiceItem->update([
            'quantity' => $orderProduct->quantity,
        ]);
    }
}
