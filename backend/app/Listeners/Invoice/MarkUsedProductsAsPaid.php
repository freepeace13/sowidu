<?php

namespace App\Listeners\Invoice;

use App\Events\Invoice\InvoiceWasFullyPaid;
use App\Models\OrderProduct;
use Illuminate\Contracts\Queue\ShouldQueue;

class MarkUsedProductsAsPaid implements ShouldQueue
{
    public function handle(InvoiceWasFullyPaid $event)
    {
        $invoice = $event->invoice;

        if (!$invoice->is_paid) {
            return;
        }

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

                $invoiceQuantity = $invoiceItem->quantity;
                $actualQuantity = $usedProduct->quantity;

                // Match quantity
                if ($actualQuantity != $invoiceQuantity) {
                    // Invoice qty is less than actual - duplicate material and mark as paid
                    if ($actualQuantity >= $invoiceQuantity) {
                        $usedProduct
                            ->replicate()
                            ->fill([
                                'quantity' => $actualQuantity - $invoiceQuantity,
                                'is_paid' => false,
                            ])
                            ->save();
                    }

                    // Update material quantity
                    $usedProduct->update(['quantity' => $invoiceQuantity]);
                }

                $usedProduct->markAsPaid();
            });
    }
}
