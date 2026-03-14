<?php

namespace App\Events\Organization;

use App\Models\Company;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganizationInvoiceSettingsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Company $company)
    {
        //
    }
}
