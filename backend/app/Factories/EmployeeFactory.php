<?php

namespace App\Factories;

use App\Events\Employment\EmployeeCreated;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;

class EmployeeFactory
{
    /**
     * @return \App\Models\Employee
     */
    public static function make(Company $employer, User $applicant, array $attributes = [])
    {
        $employee = $employer->employees()
            ->whose($applicant)
            ->first();

        if (is_null($employee)) {
            $attributes['confirmed'] = true;
            $attributes['user_id'] = $applicant->id;

            $employee = $employer->employees()
                ->create(
                    Arr::only($attributes, [
                        'specialization_id',
                        'user_id',
                        'confirmed',
                    ]),
                );
            EmployeeCreated::dispatch($employee);
        }

        return $employee;
    }
}
