<?php

namespace App\Listeners\DeliveryTicket;

use App\Events\DeliveryTicket\DeliveryTicketMaterialsAdded;
use App\Models\DeliveryTicketMaterial;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddNewMaterialToInvoice implements ShouldQueue
{
    public function handle(DeliveryTicketMaterialsAdded $event)
    {
        $deliveryTicketMaterials = $event->deliveryTicketMaterials;
        $causer = $event->causer;

        // Iterate materials and add them to the invoice
        $deliveryTicketMaterials
            ->each(
                function (DeliveryTicketMaterial $deliveryTicketMaterial) use ($causer) {
                    $deliveryTicket = $deliveryTicketMaterial->deliveryTicket;
                    $invoice = $deliveryTicket
                        ->draftInvoice()
                        ->first();

                    if (!$invoice) {
                        return; // Not yet invoiced
                    }

                    $invoiceItem = InvoiceService::run($invoice)
                        ->materialToLineItem(
                            $deliveryTicket,
                            $deliveryTicketMaterial,
                            $causer,
                        );

                    $invoice->items()
                        ->save($invoiceItem);
                },
            );
    }
}
