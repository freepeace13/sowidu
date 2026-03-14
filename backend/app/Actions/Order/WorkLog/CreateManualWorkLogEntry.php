<?php

namespace App\Actions\Order\WorkLog;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderTimeLogService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\WorkLogs\Models\WorkLog;

class CreateManualWorkLogEntry
{
    use AsAction;

    public function handle(User $user, Company $company, Order $order, array $inputs): WorkLog
    {
        Gate::forUser($user)->authorize('addManualTimeLog', $order);

        if (filled(data_get($inputs, 'employee'))) {
            // Validate if user can add manual-time-log to others
            Gate::forUser($user)->authorize('createForOtherEmployees', WorkLog::class);
        }

        $validated = $this->validate($inputs);

        data_set($validated, 'started_at', convert_to_timezone(
            $validated['started_at'],
            $validated['timezone'],
        ));

        data_set($validated, 'ended_at', convert_to_timezone(
            $validated['ended_at'],
            $validated['timezone'],
        ));

        return OrderTimeLogService::make($order)
            ->manualEntry($user, $company, $order, $validated);
    }

    public function validate(array $inputs)
    {
        return Validator::make(
            $inputs,
            [
                'started_at' => 'required|date_format:Y-m-d H:i',
                'ended_at' => 'required|date_format:Y-m-d H:i|after:started_at',
                'employee' => 'required|numeric|exists:users,id',
                'timezone' => 'required|timezone',
            ],
        )->validate();
    }
}
