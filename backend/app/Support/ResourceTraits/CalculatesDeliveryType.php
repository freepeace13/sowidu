<?php

namespace App\Support\ResourceTraits;

use App\Models\Employee;
use App\Traits\InteractsWithImpersonator;

trait CalculatesDeliveryType
{
    use InteractsWithImpersonator;

    protected function calculateDeliveryType()
    {
        if (!is_null($this->impersonator())) {
            if ($this->isImpersonating(Employee::class)) {
                $impersonators = $this->impersonated()
                    ->authenticator()
                    ->employees()
                    ->pluck('impersonator');
            } else {
                $impersonators = array_wrap($this->impersonator());
            }

            foreach ($impersonators as $impersonator) {
                if ($impersonator->is($this->resource->contractor)) {
                    return 'incoming';
                }

                if ($impersonator->is($this->resource->customer)) {
                    return 'outgoing';
                }
            }

            if ($this->impersonator()->is($this->resource->contractor)) {
                return 'incoming';
            }

            if ($this->impersonator()->is($this->resource->customer)) {
                return 'outgoing';
            }
        }
    }
}
