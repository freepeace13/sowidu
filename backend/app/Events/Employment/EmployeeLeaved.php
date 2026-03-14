<?php

namespace App\Events\Employment;

use App\Models\Company;
use App\Models\Employee;
use App\Notifications\Organization\NotifyAdminOnEmployeeLeaved;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeLeaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public Company $company, public Employee $employee)
    {
        $company->founder->notify(new NotifyAdminOnEmployeeLeaved($company, $employee));
    }
}
