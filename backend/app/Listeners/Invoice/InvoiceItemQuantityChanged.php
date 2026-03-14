<?php

namespace App\Listeners\Invoice;

use App\Events\Invoice\InvoiceItemUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceItemQuantityChanged implements ShouldQueue
{
    public function handle(InvoiceItemUpdated $event)
    {
        // Invoice Item quantity was updated
        $invoiceItem = $event->invoiceItem;
        $invoiceItemQuantity = $invoiceItem->quantity;

        // Disregard if `delivery_ticket`
        if ($invoiceItem->isDeliveryTicket()) {
            return;
        }

        $item = $invoiceItem->item;

        // Check item on order_product if quantity is equal
        if ((int) $item->quantity >= $invoiceItemQuantity) {
            return;
        }

        // `Quantity` not equal update `order_product`
        $item->update(['quantity' => $invoiceItemQuantity]);
    }
}
