<?php

namespace App\Modules\Invoice\Services;

/**
 * @property-read \App\Models\Invoice $invoice
 */
class InvoicePaymentService extends InvoiceCalculationService
{
    public function hasPayments(): bool
    {
        return $this->invoice->payments()
            ->exists();
    }

    public function isOverPaid(): bool
    {
        return $this->getTotalAmountsPaid() > $this->grandTotal();
    }

    public function isFullyPaid(): bool
    {
        return $this->round($this->getTotalAmountsPaid()) == $this->round($this->grandTotal());
    }

    public function getTotalAmountsPaid(): float
    {
        return $this->round(
            $this->invoice->payments()
                ->sum('amount'),
        );
    }
}
