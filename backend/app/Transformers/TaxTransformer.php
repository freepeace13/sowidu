<?php

namespace App\Transformers;

/**
 * @property \App\Models\Tax $resource
 */
class TaxTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'rate' => $this->resource->rate,
            'is_default' => $this->resource->is_default,
            'created_at' => $this->resource->created_at->toDateTimeString(),
            'updated_at' => $this->resource->updated_at->toDateTimeString(),
        ];
    }

    public function withCompany()
    {
        return $this->state(function ($attributes) {
            return [
                'company' => (new CompanyTransformer($this->resource->company))->resolve(),
            ];
        });
    }

    public function withAmount($amount): self
    {
        return $this->state(fn () => [
            'amount' => $amount,
            'amount_formatted' => format_currency(
                $amount,
                get_company_currency($this->resource->company_id),
            ),
        ]);
    }
}
