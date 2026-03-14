<?php

namespace App\Modules\Invoice\Actions\Preview;

use App\Actions\Traits\AsAction;
use App\Models\Invoice;
use App\Models\InvoiceDeduction;
use App\Modules\Invoice\Services\InvoiceSummaryService;
use App\Transformers\Invoice\DeductionManualTransformer;
use App\Transformers\Invoice\InvoiceDeductionTransformer;
use Illuminate\Support\Collection;

class GenerateInvoiceLineItemsTotals
{
    use AsAction;

    protected InvoiceSummaryService $service;

    protected string $currency;

    public function handle(Invoice $invoice): array
    {
        $this->service = InvoiceSummaryService::run($invoice);
        $this->currency = $this->service->currency();

        $netAmount = $this->service->netAmount();
        $grandTotal = $this->service->grandTotal();

        $items = collect([]);

        $subtotals = collect([]);
        // Subtotal
        $subtotal = $this->service->subtotal();
        $subtotals->push($this->buildInvoiceSummaryItem(
            trans('invoices.labels.subtotal'),
            $subtotal,
            [
                'colspan' => 4,
            ],
        ));

        $manualDeductions = $this->service->manualDeductions();
        if ($manualDeductions->isNotEmpty()) {
            // Manual deductions
            $manualDeductions = $this->service->manualDeductions()
                ->pluck('deductible')
                ->map(function ($deduction) {
                    $tranformed = DeductionManualTransformer::make($deduction)
                        ->withAmount(
                            $this->service->getManualDeductionAmount($deduction),
                            $this->currency,
                        )
                        ->resolve();

                    return $this->buildInvoiceSummaryItem(
                        data_get($tranformed, 'label'),
                        data_get($tranformed, 'amount'),
                        [
                            'colspan' => 3,
                            'label_colspan' => 2,
                            'amount_prefix' => '-',
                        ],
                    );
                });

            $subtotals = $subtotals->concat($manualDeductions);
            $subtotals = $subtotals->push($this->newLine());

        }

        // Invoice Deductions
        $deductions = $this->buildInvoiceDeductions($invoice);

        if ($deductions->isNotEmpty() && $manualDeductions->isNotEmpty()) {
            // Subtotal after deduction
            $subtotalAfterDeduction = $this->service->subtotalAfterDeduction();
            $subtotals->push(
                $this->buildInvoiceSummaryItem(
                    trans('invoices.labels.net-amount'),
                    $subtotalAfterDeduction,
                    [
                        'colspan' => 4,
                    ],
                ),
            );

            // Taxes on subtotal after deduction
            $taxesOnSubtotal = $this->service->taxes()
                ->get()
                ->map(
                    fn ($tax) => $this->buildInvoiceSummaryItem(
                        $tax->name . ' ' . $tax->rate . '%',
                        $this->service->calculateTaxFromAmount($subtotalAfterDeduction, $tax),
                        [
                            'colspan' => 3,
                            'label_colspan' => 2,
                        ],
                    ),
                );
            $subtotals = $subtotals->concat($taxesOnSubtotal);

            // Total amount incl. VAT
            $subtotalAfterDeductionWithVat = $this->service->subtotalAfterDeductionWithVat();
            $subtotals->push(
                $this->buildInvoiceSummaryItem(
                    trans('invoices.labels.total-amount-incl-vat'),
                    $subtotalAfterDeductionWithVat,
                    [
                        'colspan' => 4,
                    ],
                ),
            );
        }

        $items = $items->push($subtotals->toArray());

        // Invoice deductions
        $items = $items->merge($deductions);

        // Final totals
        $final = collect([]);
        $net = $this->buildInvoiceSummaryItem(
            trans('invoices.labels.net-amount'),
            $netAmount,
            [
                'colspan' => 3,
                'label_colspan' => 2,
                'is_bold' => true,
            ],
        );
        $final->push($net);

        $taxes = $this->service->taxes()
            ->get()
            ->map(
                fn ($tax) => $this->buildInvoiceSummaryItem(
                    $tax->name . ' ' . $tax->rate . '%',
                    $this->service->taxAmount($tax),
                    [
                        'colspan' => 3,
                        'label_colspan' => 2,
                        'is_bold' => true,

                    ],
                ),
            );
        $final = $final->concat($taxes);

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

        return $items
            ->toArray();
    }

    protected function newLine(): array
    {
        return [
            'new_line' => true,
        ];
    }

    protected function buildFinalTotals() {}

    protected function buildInvoiceDeductions(Invoice $invoice): Collection
    {
        return $this->service->invoiceDeductions()
            ->map(
                function (InvoiceDeduction $invoiceDeduction) {
                    $deductionItems = collect([]);

                    // Insert blank row
                    $invoice = $invoiceDeduction->deductible;
                    $summary = InvoiceSummaryService::run($invoice);

                    $currency = $summary->currency();
                    $subtotal = $invoice->subtotal ?? $summary->subtotal();
                    $grandTotal = $invoice->grand_total ?? $summary->grandTotal();

                    $netAmount = $summary->netAmount();

                    $data = InvoiceDeductionTransformer::make($invoice)
                        ->withInvoiceSummary(
                            $subtotal,
                            $grandTotal,
                            $currency,
                        )
                        ->withAmount($netAmount)
                        ->withTaxes(
                            $summary->taxes()
                                ->get(),
                        )
                        ->resolve();

                    // Subtotal
                    $deductionItems->push(
                        $this->buildInvoiceSummaryItem(
                            trans('invoices.labels.subtotal'),
                            $netAmount,
                            [
                                'prefix' => data_get($data, 'label'),
                            ],
                        ),
                    );

                    // Tax
                    $taxes = collect(data_get($data, 'taxes'))->map(function ($tax) {
                        return $this->buildInvoiceSummaryItem(
                            $tax['name'] . ' ' . $tax['rate'] . '%',
                            $tax['amount'],
                        );
                    });

                    $deductionItems = $deductionItems->concat($taxes);

                    // Grand total
                    $deductionItems->push(
                        $this->buildInvoiceSummaryItem(
                            trans('invoices.labels.grand-total'),
                            $grandTotal,
                            [
                                'border_bottom_single' => true,
                            ],
                        ),
                    );

                    return $deductionItems->toArray();
                },
            );
    }

    protected function buildInvoiceSummaryItem(
        string $label,
        float $amount,
        array $settings = [],
    ) {
        return array_merge([
            'label' => $label,
            'amount' => $amount,
            'amount_formatted' => number_to_money($amount, $this->currency),
        ], $settings);
    }
}
