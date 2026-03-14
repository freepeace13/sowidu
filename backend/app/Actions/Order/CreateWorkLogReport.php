<?php

namespace App\Actions\Order;

use App\Models\ActivityLogReport;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\WorkLogs\Contracts\WorkLogServiceInterface;
use Modules\WorkLogs\Models\WorkLog;

class CreateWorkLogReport
{
    public function __construct(
        protected WorkLogServiceInterface $workLogService,
    ) {}

    public function handle(User $user, Company $company, Order $order, WorkLog $workLog, array $inputs): ?ActivityLogReport
    {
        $validated = Validator::make($inputs, [
            'note' => 'required|string',
        ])->validate();

        // Ensure employee is currently working on this order
        throw_flash_unless(
            $this->workLogService->make($user, $company)
                ->byEmployee($user)
                ->onOrder($order)
                ->currentlyWorking()
                ->exists(),
            trans('order.errors.employee-cannot-create-report'),
        );

        $report = $this->workLogService->addReport($workLog, $validated);

        flash_success('Report created successfully.');

        return $report;
    }
}
