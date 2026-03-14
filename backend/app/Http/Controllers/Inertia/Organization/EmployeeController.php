<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\Organization\Employee\UpdateEmployeeRoles;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\CompanyInvitation as Invitation;
use App\Models\Employee;
use App\Support\Facades\Impersonate;
use App\Traits\WithOrganizationEssentials;
use App\Transformers\CompanyInvitationTransformer;
use App\Transformers\EmployeeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class EmployeeController extends InertiaController
{
    use WithOrganizationEssentials;

    public function index(Request $request)
    {
        if (!$organization = Impersonate::tenant()) {
            return Redirect::route('account.profile.index');
        }

        $employees = $organization->employees()
            ->with([
                'user',
                'employer',
                'roles',
                'rate',
            ])
            ->get();

        $invitations = Invitation::query()->onCompany($organization);

        $pendingInvitations = $invitations->pending()
            ->get();
        $failedInvitations = $invitations->failed()
            ->get();

        return Inertia::render('Account/Employees', $this->withParams([
            'pendingInvitations' => $pendingInvitations->map(fn ($invitation) => (new CompanyInvitationTransformer($invitation))->resolve()),

            'failedInvitations' => $failedInvitations->map(fn ($invitation) => (new CompanyInvitationTransformer($invitation))->resolve()),

            'employees' => $employees->map(fn ($employee) => (new EmployeeTransformer($employee))->withCompany()
                ->withRoles()
                ->withRate($employee->rate)
                ->resolve()),

            'roles' => $this->getOrganizationRolesWithoutFounder(),

            'currency' => [
                'name' => $organization->currency,
                'symbol' => currency_symbol($organization->currency),
            ],

            'employee' => Inertia::lazy(function () use ($request) {
                $employeeId = $request->get('employee');
                $employee = Employee::query()->with(['roles', 'rate'])
                    ->find($employeeId);

                if (!$employee) {
                    return [];
                }

                return EmployeeTransformer::make($employee)
                    ->withUserDetails()
                    ->withRoles()
                    ->withRate($employee->rate)
                    ->resolve();
            }),
        ]));
    }

    public function update(Request $request, Employee $employee)
    {
        UpdateEmployeeRoles::run($request->user(), auth_company(), $employee, $request->all());

        return back(303);
    }

    protected function withParams(array $params)
    {
        return array_merge([
            'title' => 'Employees',
        ], $params);
    }
}
