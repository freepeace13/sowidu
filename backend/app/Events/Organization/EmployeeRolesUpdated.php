<?php

namespace App\Events\Organization;

use App\Models\Employee;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeRolesUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Employee $employee, public array $previousRoles = []) {}
}
