<?php

namespace App\Services\DeliveryTicket;

use App\Enums\InvoiceStatus;
use Modules\DeliveryTicket\Contracts\External\InvoiceContract;

class InvoiceAdapter implements InvoiceContract
{
    public function isInvoiced(mixed $deliveryTicket): bool
    {
        return $deliveryTicket->invoice_id !== null;
    }

    public function getInvoice(mixed $deliveryTicket): mixed
    {
        return $deliveryTicket->invoice;
    }

    public function getInvoiceStatus(mixed $invoice): ?string
    {
        if (!$invoice) {
            return null;
        }

        return $invoice->status instanceof InvoiceStatus
            ? $invoice->status->value
            : $invoice->status;
    }
}
