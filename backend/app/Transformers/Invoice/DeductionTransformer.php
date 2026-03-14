<?php

namespace App\Transformers\Invoice;

use App\Models\DeductionManual;
use App\Models\Invoice;
use App\Transformers\Transformer;

/**
 * @property \App\Models\InvoiceDeduction $resource
 */
class DeductionTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'deductible_id' => $this->resource->deductible_id,
            'deductible_type' => $this->resource->deductible_type,
        ];
    }

    public function withDeductible(
        Invoice|DeductionManual $deductible,
        float $amount,
        string $currency,
    ): self {
        if ($deductible instanceof Invoice) {
            $deductibleData = InvoiceDeductionTransformer::make($deductible)
                ->withAmount($amount)
                ->resolve();
        }

        if ($deductible instanceof DeductionManual) {
            $deductibleData = DeductionManualTransformer::make($deductible)
                ->withAmount(
                    $amount,
                    $currency,
                )
                ->resolve();
        }

        return $this->state(
            fn () => [
                'deductible' => $deductibleData,
            ],
        );
    }
}
