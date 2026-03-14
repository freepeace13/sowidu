<?php

namespace App\Actions\Order\Outgoing;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DeleteOutgoingOrder
{
    public function delete(User $user, Order $order, ?Company $team)
    {
        Gate::forUser($user)->authorize('delete', $order);

        $order->delete();
    }
}
