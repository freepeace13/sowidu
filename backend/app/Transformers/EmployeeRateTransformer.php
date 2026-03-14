<?php

namespace App\Transformers;

use App\Services\CompanyService;

/**
 * @property \App\Models\EmployeeRate $resource
 */
class EmployeeRateTransformer extends Transformer
{
    use Traits\WithUrnAttribute;

    public function toArray($request)
    {

        $rates = [
            'id' => $this->resource->id,
            'updated_at' => $this->resource->updated_at,
            'created_at' => $this->resource->created_at,
        ];

        if ($this->resource->employee) {
            $employer = $this->resource->loadMissing(['employee'])
                ->employee->employer();

            $currency = CompanyService::make(
                $employer->first(),
            )
                ->getCompanyCurrency();

            return array_merge($rates, [
                'currency' => $currency,
                'symbol' => currency_symbol($currency),
                'rate' => $this->resource->rate,
            ]);
        }

        $currency = config('app.default.organization.settings.employee_rate.currency');
        $rate = config('app.default.organization.settings.employee_rate.rate');

        return array_merge($rates, [
            'currency' => $currency,
            'symbol' => currency_symbol($currency),
            'rate' => $rate,
        ]);

    }
}
