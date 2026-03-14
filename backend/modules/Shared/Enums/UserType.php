<?php

namespace Modules\Shared\Enums;

use App\Models\Employee;
use App\Models\User;

class UserType extends Enum
{
    const Employee = Employee::class;
    const User = User::class;
}
