<?php

namespace App\Actions\Auth;

use App\Models\Addressbook;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class LinkUserToOrders
{
    public function link(User $user)
    {
        $addressbooks = Addressbook::where('email', $user->email)->get('id');

        // Get all orders with client on these addressbook ids
        $addressbooks->each(function (Addressbook $addressbook) use ($user) {
            Order::query()
                ->whereHasMorph(
                    'client',
                    [Addressbook::class],
                    function (Builder $query) use ($addressbook) {
                        $query->where('id', $addressbook->id);
                    },
                )
                ->get()
                ->each(function (Order $order) use ($user) {
                    $order->client()->associate($user)->save();
                });
        });
    }
}
