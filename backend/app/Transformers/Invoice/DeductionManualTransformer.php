<?php

namespace App\Transformers\Invoice;

use App\Transformers\Transformer;

/**
 * @property \App\Models\DeductionManual $resource
 */
class DeductionManualTransformer extends Transformer
{
    public function toArray($request)
    {
        $label = $this->resource->name;

        if ($this->resource->operator == '%') {
            $label .= ' (' . $this->resource->amount . '%)';
        }

        return [
            'id' => $this->resource->id,
            'type' => 'manual_deduction',
            'deductible_id' => $this->resource->deductible_id,
            'name' => $this->resource->name,
            'operator' => $this->resource->operator,
            'label' => $label,
        ];
    }

    public function withAmount(int|float $amount, string $currency): self
    {
        return $this->state(
            fn () => [
                'amount' => $amount,
                'amount_formatted' => number_to_money(
                    $amount,
                    $currency,
                ),
            ],
        );
    }
}
