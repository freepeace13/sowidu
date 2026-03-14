<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\WorkLogs\Contracts\External\EmployeeContract;
use Modules\WorkLogs\Contracts\External\ImpersonatorContract;
use Modules\WorkLogs\Enums\PaymentForm;
use Modules\WorkLogs\Enums\WorkLogEvent;

class WorkLogIndexController extends Controller
{
    public function __construct(
        protected ImpersonatorContract $impersonator,
        protected EmployeeContract $employeeService,
    ) {}

    public function __invoke(Request $request)
    {
        abort_unless($this->impersonator->isImpersonating(), 403, 'You are not allowed to access this resource.');
        $team = $this->impersonator->getCompany();

        return Inertia::render('Index', [
            'employees' => $this->employeeService->getEmployeesForTeam($team, $request->user()),

            'workLogEvents' => fn () => WorkLogEvent::forManualEntry(),

            'filterByEvents' => fn () => WorkLogEvent::options(),

            'paymentForms' => fn () => PaymentForm::options(),
        ]);
    }
}
