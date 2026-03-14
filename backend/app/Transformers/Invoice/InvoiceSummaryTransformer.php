<?php

namespace App\Transformers\Invoice;

use App\Transformers\Transformer;

class InvoiceSummaryTransformer extends Transformer
{
    public function toArray($request)
    {
        $currency = $this->resource->currency;

        return [
            'currency' => $currency,
            'subtotal' => $this->resource->subtotal,
            'grand_total' => $this->resource->grand_total,
            'net_amount' => $this->resource->net_amount,
            'subtotal_formatted' => number_to_money(
                $this->resource->subtotal,
                $currency,
            ),
            'grand_total_formatted' => number_to_money(
                $this->resource->grand_total,
                $currency,
            ),
            'net_amount_formatted' => number_to_money(
                $this->resource->net_amount,
                $currency,
            ),
        ];
    }

    public function withTaxes(array $taxes): self
    {
        return $this->state(fn () => [
            'taxes' => $taxes,
        ]);
    }

    public function withManualDeductions(array $manualDeductions): self
    {
        return $this->state(fn () => [
            'manual_deductions' => $manualDeductions,
        ]);
    }

    public function withInvoiceDeductions(array $invoiceDeductions): self
    {
        return $this->state(fn () => [
            'deductions' => $invoiceDeductions,
        ]);
    }

    public function withSubtotalAfterDeduction(float $amount): self
    {
        return $this->state(fn () => [
            'subtotal_after_deduction' => $amount,
            'subtotal_after_deduction_formatted' => number_to_money(
                $amount,
                $this->resource->currency,
            ),
        ]);
    }

    public function withVatOnSubtotalAfterDeduction(float $amount): self
    {
        return $this->state(fn () => [
            'vat_on_subtotal_after_deduction' => $amount,
            'vat_on_subtotal_after_deduction_formatted' => number_to_money(
                $amount,
                $this->resource->currency,
            ),
        ]);
    }

    public function withSubtotalAfterDeductionWithVat(float $amount): self
    {
        return $this->state(fn () => [
            'subtotal_after_deduction_with_vat' => $amount,
            'subtotal_after_deduction_with_vat_formatted' => number_to_money(
                $amount,
                $this->resource->currency,
            ),
        ]);
    }
}
