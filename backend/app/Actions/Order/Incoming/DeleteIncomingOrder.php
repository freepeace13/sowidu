<?php

namespace App\Actions\Order\Incoming;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DeleteIncomingOrder
{
    public function delete(User $user, Order $order, ?Company $team)
    {
        Gate::forUser($user)->authorize('delete', $order);

        $order->delete();
    }
}
