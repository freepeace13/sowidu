<?php

namespace App\Services\Order\Traits;

use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Order;

trait CanIdentifyOrderParties
{
    public function clientIs(Order $order, $model)
    {
        return $order->client->is($model);
    }

    public function contractorIs(Order $order, ?Company $company = null): bool
    {
        if (!$company) {
            return false;
        }

        return $order->loadMissing(['contractor'])
            ->contractor->is($company);
    }

    public function clientIsForeign(Order $order): bool
    {
        return morph_is($order->client, Addressbook::class);
    }
}
