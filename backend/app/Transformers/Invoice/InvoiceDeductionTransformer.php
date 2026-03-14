<?php

namespace App\Transformers\Invoice;

use App\Modules\Invoice\Services\InvoiceSummaryService;
use App\Transformers\TaxTransformer;
use App\Transformers\Transformer;
use Illuminate\Support\Collection;

/**
 * @property \App\Models\Invoice $resource
 */
class InvoiceDeductionTransformer extends Transformer
{
    public function toArray($request)
    {
        $amount = $this->resource?->subtotal ?? data_get($this->resource?->final_data, 'invoice.subtotal', 0);
        $kindLabel = $this->resource->kind?->trans();
        $invoiceLabelId = $this->resource->external_id ?? $this->resource->internal_id;

        return [
            'id' => $this->resource->id,
            'type' => 'invoice_deduction',
            'label' => implode(' - ', array_filter([
                $kindLabel,
                $invoiceLabelId,
            ])),
            'amount' => $amount,
            'amount_formatted' => number_to_money(
                $amount,
                get_company_currency($this->resource->company_id),
            ),
            'operator' => '-',
        ];
    }

    public function withAmount(float $netAmount): self
    {
        return $this->state(fn () => [
            'amount' => $netAmount,
            'amount_formatted' => number_to_money($netAmount, get_company_currency($this->resource->company_id)),
        ]);
    }

    public function withInvoiceSummary(
        float $subtotal,
        float $grandTotal,
        string $currency,
    ): self {
        return $this->state(fn () => [
            'subtotal' => $subtotal,
            'grand_total' => $grandTotal,
            'subtotal_formatted' => number_to_money($subtotal, $currency),
            'grand_total_formatted' => number_to_money($grandTotal, $currency),
        ]);
    }

    public function withTaxes(Collection $taxes): self
    {
        $invoiceSummary = InvoiceSummaryService::run($this->resource);

        return $this->state(fn () => [
            'taxes' => $taxes
                ->map(
                    fn ($tax) => TaxTransformer::make($tax)
                        ->withAmount(
                            $invoiceSummary->taxAmount($tax),
                        )
                        ->resolve(),
                ),
        ]);
    }
}
