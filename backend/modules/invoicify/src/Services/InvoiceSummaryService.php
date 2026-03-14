<?php

namespace Modules\Invoicify\Services;

use App\Models\Invoice;
use Modules\Invoicify\Traits\WithInvoiceSettings;

class InvoiceSummaryService extends InvoiceCalculationService
{
    use WithInvoiceSettings;

    public function __construct(protected Invoice $invoice) {}

    public static function run(Invoice $invoice): static
    {
        return new static($invoice);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany|\App\Models\InvoiceDeduction */
    public function deductions()
    {
        return $this->invoice->deductions();
    }

    /** @return \Illuminate\Support\Collection<\App\Models\InvoiceDeduction> */
    public function invoiceDeductions()
    {
        return $this->deductions()
            ->where('deductible_type', get_morph_class(Invoice::class))
            ->with('deductible')
            ->get();
    }

    /** @return \Illuminate\Support\Collection<\App\Models\InvoiceDeduction> */
    public function manualDeductions()
    {
        return $this->deductions()
            ->manual()
            ->with('deductible')
            ->get();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany */
    public function taxes()
    {
        return $this->invoice->taxes();
    }

    public function saveInvoiceSummarries()
    {
        if (!$this->invoice->isAlreadySent()) {
            return;
        }

        $this->invoice->update([
            'subtotal' => $this->subtotal(),
            'total_vat' => $this->totalTaxes(),
            'grand_total' => $this->grandTotal(),
            'net_amount' => $this->netAmount(),
        ]);
    }
}
