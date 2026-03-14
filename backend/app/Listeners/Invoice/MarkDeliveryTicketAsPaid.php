<?php

namespace App\Listeners\Invoice;

use App\Events\Invoice\InvoiceWasFullyPaid;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketMaterial;
use Illuminate\Contracts\Queue\ShouldQueue;

class MarkDeliveryTicketAsPaid implements ShouldQueue
{
    public function handle(InvoiceWasFullyPaid $event)
    {
        $invoice = $event->invoice;

        if (!$invoice->is_paid) {
            return;
        }

        // Get invoice_item which are materials and mark them paid
        $invoice
            ->items()
            ->where('item_type', get_morph_alias(DeliveryTicketMaterial::class))
            ->with(['item'])
            ->get()
            ->each(function ($invoiceItem) {
                $material = $invoiceItem->item;
                $invoiceQuantity = $invoiceItem->quantity;
                $actualQuantity = $material->quantity;

                if ($actualQuantity != $invoiceQuantity) {
                    // Invoice qty is less than actual - duplicate material and mark as paid
                    if ($actualQuantity >= $invoiceQuantity) {
                        $actualQuantity
                            ->replicate()
                            ->fill([
                                'quantity' => $actualQuantity - $invoiceQuantity,
                                'is_paid' => false,
                            ])
                            ->save();
                    }

                    // Update material quantity
                    $material->update(['quantity' => $invoiceQuantity]);
                }

                // Mark as paid
                $material->markAsPaid();
            });

        // Iterate on delivery_tickets check if all materials are paid then mark also the delivery_tickets
        $invoice
            ->items()
            ->where('item_type', get_morph_alias(DeliveryTicket::class))
            ->with(['item.materials'])
            ->get()
            ->each(function ($item) {
                $deliveryTicket = $item->item;
                $allArePaid = $deliveryTicket->materials
                    ->every(fn ($material) => $material->is_paid);

                if (!$allArePaid) {
                    return;
                }

                // Mark delivery_ticket as `paid`
                $deliveryTicket->markAsPaid();
            });
    }
}
