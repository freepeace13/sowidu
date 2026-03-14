<?php

namespace App\Events\Organization;

use App\Models\Company;
use App\Support\Models\InteractsWithModelChanges;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganizationMediaSettingsUpdated
{
    use Dispatchable, InteractsWithModelChanges, InteractsWithSockets, SerializesModels;

    public array $changes = [];

    public function __construct(public Company $organization)
    {
        $this->changes = $this->getModelColumnChanges($organization);
    }
}
