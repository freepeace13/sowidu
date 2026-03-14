<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Orderable
{
    public function syncOrders(array $orderIds);

    public function addOrder($order);

    public function removeOrder($order);

    public function orders(): MorphToMany;
}
