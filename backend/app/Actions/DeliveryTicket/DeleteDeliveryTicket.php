<?php

namespace App\Actions\DeliveryTicket;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\DeliveryTicket;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DeleteDeliveryTicket
{
    use AsAction;

    public function handle(User $user, Company $company, DeliveryTicket $deliveryTicket)
    {
        Gate::forUser($user)->authorize('delete', $deliveryTicket);

        $deliveryTicket->delete();
    }
}
