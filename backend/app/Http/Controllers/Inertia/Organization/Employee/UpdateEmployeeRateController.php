<?php

namespace App\Http\Controllers\Inertia\Organization\Employee;

use App\Actions\Organization\Employee\UpdateEmployeeRates;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class UpdateEmployeeRateController extends Controller
{
    public function __invoke(Request $request, Employee $employee)
    {
        UpdateEmployeeRates::run(
            $request->user(),
            $request->company(),
            $employee,
            $request->all(),
        );

        flash_success(__('account.employees.messages.rates.updated'));

        return redirect()->back();
    }
}
