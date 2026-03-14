<?php

namespace App\Actions\Order;

use App\Models\Activity;
use App\Models\ActivityLogReport;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\WorkLogs\Contracts\WorkLogServiceInterface;

class DeleteWorkLogReport
{
    public function __construct(
        protected WorkLogServiceInterface $workLogService,
    ) {}

    public function delete(User $user, Company $company, Order $order, Activity $workLog, array $inputs): ActivityLogReport
    {
        $validated = Validator::make($inputs, [
            'note' => 'required|string',
        ])->validate();

        // Ensure employee is currently working on this order
        throw_validation_unless(
            $this->workLogService->make($user, $company)
                ->byEmployee($user)
                ->onOrder($order)
                ->currentlyWorking()
                ->exists(),
            trans('order.errors.employee-cannot-create-report'),
        );

        $report = (new ActivityLogReport)->fill($validated);

        $report->activityLog()
            ->associate($workLog);

        $report->user()
            ->associate($user);

        return $report;
    }
}
