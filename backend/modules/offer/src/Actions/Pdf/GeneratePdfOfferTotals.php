<?php

namespace Modules\Offer\Actions\Pdf;

use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Contracts\External\InvoiceServiceContract;

/**
 * Generates PDF totals for offers.
 * Delegates to InvoiceServiceContract for invoice-related calculations.
 */
class GeneratePdfOfferTotals
{
    use AsAction;

    protected array $summary = [];

    protected string $currency = 'EUR';

    public function __construct(
        protected InvoiceServiceContract $invoiceService,
    ) {}

    public function handle(Model $invoice): array
    {
        $this->summary = $this->invoiceService->getSummary($invoice);
        $this->currency = $this->summary['currency'] ?? 'EUR';

        $netAmount = $this->summary['net_amount'] ?? 0;
        $grandTotal = $this->summary['grand_total'] ?? 0;
        $subtotal = $this->summary['subtotal'] ?? 0;

        $items = collect([]);
        $subtotals = collect([]);

        // Subtotal
        $subtotals->push($this->buildInvoiceSummaryItem(
            trans('invoices.labels.subtotal'),
            $subtotal,
            ['colspan' => 4],
        ));

        // Manual deductions from summary
        $manualDeductions = collect($this->summary['manual_deductions'] ?? []);
        if ($manualDeductions->isNotEmpty()) {
            $deductionItems = $manualDeductions->map(fn ($deduction) => $this->buildInvoiceSummaryItem(
                $deduction['label'] ?? '',
                $deduction['amount'] ?? 0,
                [
                    'colspan' => 3,
                    'label_colspan' => 2,
                    'amount_prefix' => '-',
                ],
            ));

            $subtotals = $subtotals->concat($deductionItems);
            $subtotals = $subtotals->push($this->newLine());
        }

        $items = $items->push($subtotals->toArray());

        // Final totals
        $final = collect([]);
        $final->push($this->buildInvoiceSummaryItem(
            trans('invoices.labels.net-amount'),
            $netAmount,
            [
                'colspan' => 3,
                'label_colspan' => 2,
                'is_bold' => true,
            ],
        ));

        // Taxes from summary
        $taxes = collect($this->summary['taxes'] ?? []);
        $taxItems = $taxes->map(fn ($tax) => $this->buildInvoiceSummaryItem(
            ($tax['name'] ?? '') . ' ' . ($tax['rate'] ?? 0) . '%',
            $tax['amount'] ?? 0,
            [
                'colspan' => 3,
                'label_colspan' => 2,
                'is_bold' => true,
            ],
        ));
        $final = $final->concat($taxItems);

        // Grand total
        $final->push($this->buildInvoiceSummaryItem(
            trans('invoices.labels.grand-total'),
            $grandTotal,
            [
                'colspan' => 3,
                'label_colspan' => 2,
                'border_bottom_double' => true,
                'is_bold' => true,
            ],
        ));

        $items = $items->merge([$final]);

        return $items->toArray();
    }

    protected function newLine(): array
    {
        return ['new_line' => true];
    }

    protected function buildInvoiceSummaryItem(
        string $label,
        float $amount,
        array $settings = [],
    ): array {
        return array_merge([
            'label' => $label,
            'amount' => $amount,
            'amount_formatted' => number_to_money($amount, $this->currency),
        ], $settings);
    }
}
