<?php

namespace App\Listeners\DeliveryTicket;

use App\Events\DeliveryTicket\DeliveryTicketMaterialsUpdated;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateInvoiceItemLinkedToTicketMaterials implements ShouldQueue
{
    public function handle(DeliveryTicketMaterialsUpdated $event)
    {
        $ticketMaterial = $event->deliveryTicketMaterial;

        // Check if ticket_material is linked to an invoice_item
        $invoiceItem = $ticketMaterial->invoiceItem()
            ->with('invoice')
            ->first();

        // Invoice item not found.
        if (!$invoiceItem) {
            return;
        }

        // Invoice item. Cannot be updated.
        if (
            InvoiceService::run($invoiceItem->invoice)->itemsCannotBeUpdated()
        ) {
            return;
        }

        // Update invoice_item
        $invoiceItem->update([
            'quantity' => $ticketMaterial->quantity,
            'price' => $ticketMaterial->selling_price,
        ]);
    }
}
