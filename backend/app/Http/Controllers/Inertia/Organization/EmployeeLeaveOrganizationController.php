<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\Organization\Employee\LeaveOnOrganization;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class EmployeeLeaveOrganizationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Company $company)
    {
        (new LeaveOnOrganization)->execute($request->user(), $company);

        return back(303);
    }
}
