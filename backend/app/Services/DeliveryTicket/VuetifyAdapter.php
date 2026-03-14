<?php

namespace App\Services\DeliveryTicket;

use App\Support\Vuetify\CreateOptions;
use Modules\DeliveryTicket\Contracts\External\VuetifyContract;

class VuetifyAdapter implements VuetifyContract
{
    public function createOptions(mixed $collection, string $valueKey = 'id', string $labelKey = 'name'): array
    {
        return CreateOptions::fromCollection($collection, $valueKey, $labelKey);
    }

    public function createEnumOptions(string $enumClass): array
    {
        return CreateOptions::fromEnum($enumClass);
    }
}
