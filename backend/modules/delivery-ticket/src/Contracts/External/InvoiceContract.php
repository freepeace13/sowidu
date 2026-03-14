<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for invoice operations.
 *
 * Provides invoice-related functionality for delivery tickets.
 */
interface InvoiceContract
{
    /**
     * Check if delivery ticket is invoiced.
     */
    public function isInvoiced(mixed $deliveryTicket): bool;

    /**
     * Get invoice for delivery ticket.
     */
    public function getInvoice(mixed $deliveryTicket): mixed;

    /**
     * Check invoice status.
     */
    public function getInvoiceStatus(mixed $invoice): ?string;
}
