<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;

/**
 * Outgoing port for invoice-related services needed by the Offer module.
 * Used for PDF generation totals and deduction transformations.
 */
interface InvoiceServiceContract
{
    /**
     * Find an invoice by ID.
     */
    public function find(int $id): ?Model;

    /**
     * Get invoice summary data for PDF generation.
     */
    public function getSummary(Model $invoice): array;

    /**
     * Transform a deduction manual for display.
     */
    public function transformDeduction(Model $deduction): array;
}
