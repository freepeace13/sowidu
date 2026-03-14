<?php

namespace App\Transformers;

/**
 * @property \App\Models\InvoicePayment $resource
 */
class InvoicePaymentTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'invoice_id' => $this->resource->invoice_id,
            'payment_date' => $this->resource->payment_date,
            'amount' => $this->resource->amount,
            'reference_number' => $this->resource->reference_number,
            'method' => $this->resource->method,
            'payer_name' => $this->resource->payer_name,
            'notes' => $this->resource->notes,
            'updated_at' => $this->resource->updated_at,
            'created_at' => $this->resource->created_at,
        ];
    }

    public function withAmountFormatted(string $currency): self
    {
        return $this->state(fn () => [
            'amount_formatted' => number_to_money(
                $this->resource->amount,
                $currency,
            ),
        ]);
    }

    public function withMethodMeta(): self
    {
        $method = $this->resource->method;

        return $this->state(fn () => [
            'method_label' => $method->trans(),
            'method_color' => $method->color(),
        ]);
    }
}
