<?php

namespace Modules\DeliveryTicket\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Policies\DeliveryTicketPolicy;

class PolicyServiceProvider extends ServiceProvider
{
    protected $policies = [
        DeliveryTicket::class => DeliveryTicketPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }

    protected function registerPolicies()
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
