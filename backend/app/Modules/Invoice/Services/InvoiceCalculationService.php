<?php

namespace App\Modules\Invoice\Services;

use App\Models\DeductionManual;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Tax;
use App\Modules\Invoice\Services\Traits\WithInvoiceSettings;
use Illuminate\Support\Facades\Cache;
use Modules\WorkLogs\Models\WorkLog;

class InvoiceCalculationService
{
    use WithInvoiceSettings;

    const CACHE_PREFIX = 'invoice_calculation.';
    const SUBTOTAL = 'subtotal';
    const NET_AMOUNT = 'net_amount';
    const NET_MANUAL_DEDUCTION = 'net_manual_deduction';
    const NET_MANUAL_TAX = 'net_manual_tax';
    const NET_MANUAL_GRAND_TOTAL = 'net_manual_grand_total';
    const GRAND_TOTAL = 'grand_total';
    const TOTAL_DEDUCTIONS = 'total_deductions';
    const TOTAL_MANUAL_DEDUCTIONS = 'total_manual_deductions';
    const TOTAL_INVOICE_DEDUCTIONS = 'total_invoice_deductions';
    const TOTAL_TAXES = 'total_taxes';
    const OUTSTANDING_BALANCE = 'outstanding_balance';
    const TOTAL_AMOUNTS_PAID = 'total_amounts_paid';
    const TOTAL_WAGE = 'total_wage';
    const TTL = 10;
    const SUBTOTAL_AFTER_DEDUCTION_VAT = 'subtotal_after_deduction_vat';

    public function __construct(protected Invoice $invoice)
    {
        $this->forgetCache();
    }

    protected function ttl(): int
    {
        if (!app()->environment('production')) {
            return 1;
        }

        return self::TTL;
    }

    public static function run(Invoice $invoice): static
    {
        return new static($invoice);
    }

    protected function getCacheKey(string $name): string
    {
        return self::CACHE_PREFIX . $this->invoice->id . '.' . $name;
    }

    protected function forgetCache()
    {
        $reflection = new \ReflectionClass(__CLASS__);

        collect($reflection->getConstants())
            ->reject(fn ($key) => $key == self::CACHE_PREFIX)
            ->each(fn (string $value) => Cache::forget($this->getCacheKey($value)));
    }

    public function clearCache(): self
    {
        $this->forgetCache();

        return $this;
    }

    /**
     * This is total of items purchase! Before all the deductions, taxes, etc.
     */
    public function subtotal(): float
    {
        return Cache::remember(
            $this->getCacheKey(self::SUBTOTAL),
            $this->ttl(),
            function () {
                return $this->invoice->items()
                    ->select(['quantity', 'price', 'invoice_id'])
                    ->get()
                    ->sum(fn ($item) => $item->quantity * $item->price);
            },
        );
    }

    public function subtotalAfterDeduction(): float
    {
        return $this->round($this->subtotal() - $this->totalManualDeductions());
    }

    public function calculateVatOnSubtotalAfterDeductions(): float
    {
        $subtotalAfterDeduction = $this->subtotalAfterDeduction();

        return $this->round($this->cache(
            self::SUBTOTAL_AFTER_DEDUCTION_VAT,
            $this->invoice->taxes()
                ->select(['rate', 'id'])
                ->get()
                ->map(fn ($tax) => $this->calculateTaxFromAmount(
                    $subtotalAfterDeduction,
                    $tax,
                ))
                ->sum(),
        ));
    }

    public function subtotalAfterDeductionWithVat(): float
    {
        return $this->round(
            $this->subtotalAfterDeduction() + $this->calculateVatOnSubtotalAfterDeductions(),
        );
    }

    protected function cache(string $key, mixed $value)
    {
        return Cache::remember(
            $this->getCacheKey($key),
            $this->ttl(),
            fn () => $value,
        );
    }

    /**
     * This is total after all the deductions
     *
     * "Total without VAT" in the invoice
     */
    public function netAmount(): float
    {
        return Cache::remember(
            $this->getCacheKey(self::NET_AMOUNT),
            $this->ttl(),
            function () {
                return $this->subtotal() - $this->totalDeductions();
            },
        );
    }

    /**
     * This is total after all the taxes and deductions are applied
     *
     * "Total with VAT" in the invoice
     */
    public function grandTotal(): float
    {
        $netAmount = $this->netAmount();
        $totalTaxes = $this->totalTaxes();

        return $this->round($netAmount + $totalTaxes);
    }

    public function totalDeductions(): float
    {
        return $this->totalInvoiceDeductions() + $this->totalManualDeductions();
    }

    public function totalManualDeductions(): float
    {
        return Cache::remember(
            $this->getCacheKey(self::TOTAL_MANUAL_DEDUCTIONS),
            $this->ttl(),
            function () {
                return $this->invoice->deductions()
                    ->manual()
                    ->with('deductible')
                    ->get()
                    ->pluck('deductible')
                    ->sum(
                        fn (DeductionManual $deduction) => $this->getManualDeductionAmount($deduction),
                    );
            },
        );
    }

    public function totalInvoiceDeductions(): float
    {
        return Cache::remember(
            $this->getCacheKey(self::TOTAL_INVOICE_DEDUCTIONS),
            $this->ttl(),
            function () {
                return $this->invoice->deductions()
                    ->onlyInvoice()
                    ->with('deductible:id,final_data,subtotal')
                    ->get()
                    ->pluck('deductible')
                    ->sum(
                        function (Invoice $invoice) {
                            if (filled($invoice->net_amount)) {
                                return $invoice->net_amount;
                            }

                            $invoiceSummary = InvoiceSummaryService::run($invoice);

                            return $invoiceSummary->netAmount();
                        },
                    );
            },
        );
    }

    public function getManualDeductionAmount(DeductionManual $deductionManual): float
    {
        return $deductionManual->operator == '-'
            ? $deductionManual->amount
            : $this->calculateDeductionPercentage(
                $this->deductionBaseAmount(),
                $deductionManual->amount,
            );
    }

    protected function calculateDeductionPercentage($amount, $percentage): float
    {
        return $this->round($amount * ($percentage / 100));
    }

    public function deductionBaseAmount()
    {
        // Check if this invoice has a deduction invoice
        // if (
        //     $this->invoice->deductions()
        //         ->onlyInvoice()
        //         ->exists()
        // ) {
        //     // Exclude other invoices from the deduction
        //     $invoiceDeductionsTotals = $this->invoice->deductions()
        //         ->onlyInvoice()
        //         ->with('deductible:id,grand_total')
        //         ->get()
        //         ->pluck('deductible')
        //         ->sum(
        //             function (Invoice $invoice) {
        //                 if (filled($invoice->grand_total)) {
        //                     return $invoice->grand_total;
        //                 }

        //                 return InvoiceSummaryService::run($invoice)->grandTotal();
        //             },
        //         );

        //     return $this->subtotal() - $invoiceDeductionsTotals;
        // }

        return $this->subtotal();
    }

    /**
     * This is total of all the taxes applied to the invoice
     *
     * "Total VAT" in the invoice
     */
    public function totalTaxes(): float
    {
        return Cache::remember(
            $this->getCacheKey(self::TOTAL_TAXES),
            $this->ttl(),
            function () {
                return $this->invoice->taxes()
                    ->select(['rate', 'id'])
                    ->get()
                    ->map(fn ($tax) => $this->taxAmount($tax))
                    ->sum();
            },
        );
    }

    public function calculateTaxFromAmount($amount, Tax $tax): float
    {
        return $amount * ($tax->rate / 100);
    }

    public function taxAmount(Tax $tax): float
    {
        return $this->calculateTaxFromAmount($this->netAmount(), $tax);
    }

    public function outstandingBalance(): float
    {
        return (float) $this->round($this->grandTotal() - $this->totalAmountsPaid());
    }

    public function totalAmountsPaid(): float
    {
        return Cache::remember(
            $this->getCacheKey(self::TOTAL_AMOUNTS_PAID),
            $this->ttl(),
            function () {
                return $this->invoice->payments()
                    ->sum('amount');
            },
        );
    }

    protected function round(float|int $number, ?int $precision = 2): float
    {
        return round($number, $precision);
    }

    public function wageSubtotal(): float
    {
        return $this->invoice->items()
            ->whereMorphedTo('item', WorkLog::class)
            ->get()
            ->sum(fn (InvoiceItem $item) => $item->subtotal());
    }

    public function totalWage(): float
    {
        $wageSubtotal = $this->wageSubtotal();
        $taxRates = $this->invoice->taxes()
            ->select(['rate', 'id'])
            ->get()
            ->map(fn ($tax) => $this->calculateTaxFromAmount($wageSubtotal, $tax))
            ->sum();

        return $wageSubtotal + $taxRates;
    }
}
