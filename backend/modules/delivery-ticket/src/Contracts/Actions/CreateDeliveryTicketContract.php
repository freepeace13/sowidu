<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

interface CreateDeliveryTicketContract
{
    public function handle($user, $company, array $inputs);
}
