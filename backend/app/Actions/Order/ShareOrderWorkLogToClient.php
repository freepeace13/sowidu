<?php

namespace App\Actions\Order;

use App\Actions\Traits\AsAction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\WorkLogs\Models\WorkLog;

class ShareOrderWorkLogToClient
{
    use AsAction;

    public function handle(User $user, Order $order, WorkLog $workLog, array $inputs): WorkLog
    {
        Gate::forUser($user)->authorize('share-work-log', [$order, $workLog]);

        $validated = Validator::make($inputs, [
            'is_shared' => 'required|boolean',
        ])->validate();

        return tap($workLog)->update($validated);
    }
}
