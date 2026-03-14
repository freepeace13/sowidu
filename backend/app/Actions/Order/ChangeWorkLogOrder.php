<?php

namespace App\Actions\Order;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Rules\OwnedByCompany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\WorkLogs\Models\WorkLog;

class ChangeWorkLogOrder
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Order $order,
        WorkLog $workLog,
        array $inputs,
    ): WorkLog {
        // Validate action
        Gate::forUser($user)->authorize('canChangeWorkLogOrder', [$order, $workLog]);

        $validated = $this->validate($inputs);

        $workLog->order()
            ->associate(Order::findOrFail(data_get($validated, 'order.id')))
            ->save();

        return $workLog;
    }

    public function validate(array $inputs)
    {
        return Validator::make(
            $inputs,
            [
                'order' => 'required|array',
                'order.id' => [
                    'required',
                    'integer',
                    new OwnedByCompany(Order::class, 'team_id'),
                ],
            ],
        )->validate();
    }
}
