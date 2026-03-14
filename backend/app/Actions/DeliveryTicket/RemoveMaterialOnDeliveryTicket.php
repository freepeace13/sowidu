<?php

namespace App\Actions\DeliveryTicket;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketMaterial;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Gate;

class RemoveMaterialOnDeliveryTicket
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
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
