<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Gate;
use Modules\DeliveryTicket\Contracts\Actions\RemoveMaterialOnDeliveryTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

class RemoveMaterialOnDeliveryTicket implements RemoveMaterialOnDeliveryTicketContract
{
    public function handle(
        $user,
        $company,
        DeliveryTicket $deliveryTicket,
        DeliveryTicketMaterial $material,
    ) {
        Gate::forUser($user)->authorize(
            'manageMaterials',
            $deliveryTicket,
        );

        if (
            $invoiceItem = $material->invoiceItem()
                ->with('invoice')
                ->first()
        ) {
            if (
                InvoiceService::run($invoiceItem->invoice)
                    ->itemsCannotBeUpdated()
            ) {
                return;
            }

            // Remove invoice_item first
            $invoiceItem->delete();
        }

        // Remove
        $material->delete();
    }
}
