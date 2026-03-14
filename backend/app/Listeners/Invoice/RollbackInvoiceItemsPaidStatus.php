<?php

namespace App\Listeners\Invoice;

use App\Events\Invoice\InvoicePaymentVoided;
use App\Models\DeliveryTicketMaterial;
use App\Models\InvoiceItem;
use App\Models\OrderProduct;
use Illuminate\Contracts\Queue\ShouldQueue;

class RollbackInvoiceItemsPaidStatus implements ShouldQueue
{
    public function handle(InvoicePaymentVoided $event)
    {
        /** @var \App\Models\Invoice $invoice */
        $invoice = $event->invoice;

        // Update invoice ticket materials
        $invoice
            ->items()
            ->where('item_type', get_morph_alias(DeliveryTicketMaterial::class))
            ->with(['item'])
            ->get()
            ->each(function (InvoiceItem $invoiceItem) {
                /** @var \App\Models\DeliveryTicketMaterial $material */
                $material = $invoiceItem->item;

                // Mark as `unpaid`
                $material->markAsUnPaid();

                $ticket = $material->deliveryTicket()->first();
                $ticket->markAsUnPaid();
            });

        // Fetch invoice used_products
        $invoice->items()
            ->where('item_type', get_morph_alias(OrderProduct::class))
            ->with(['item'])
            ->get()
            ->each(function ($invoiceItem) {
                $usedProduct = $invoiceItem->item;

                if (!morph_is(OrderProduct::class, $usedProduct)) {
                    return;
                }

                $usedProduct->markAsUnPaid();
            });
    }
}
