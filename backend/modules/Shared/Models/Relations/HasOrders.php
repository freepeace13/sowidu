<?php

namespace Modules\Shared\Models\Relations;

use App\Models\Company;
use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasOrders
{
    public function orders(): MorphOne
    {
        return $this->morphOne(Order::class, 'clientable');
    }

    public function deals(): MorphOne
    {
        return $this->morphOne(Order::class, 'contractable');
    }

    public function incomingOrders()
    {
        return $this->deals()
            ->whereMorphRelation('contractor', Company::class, 'id', $this->getKey());
    }

    public function outgoingOrders()
    {
        return $this->orders()
            ->whereMorphRelation('client', '*', 'id', $this->getKey());
    }
}
