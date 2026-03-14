<?php

namespace App\Enums;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;

class ContactType extends Enum
{
    const User = User::class;
    const Employee = Employee::class;
    const Company = Company::class;
}
