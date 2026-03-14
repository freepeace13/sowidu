<?php

namespace App\Actions\Order;

use App\Models\ActivityLogReport;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\WorkLogs\Models\WorkLog;

class UpdateWorkLogReport
{
    public function update(
        User $user,
        Company $company,
        WorkLog $workLog,
        ActivityLogReport $report,
        array $inputs,
    ): ActivityLogReport {
        throw_flash_unless(
            $company->isFounder($user) || $report->user->is($user),
            trans('validation.403'),
        );

        $validated = Validator::make($inputs, [
            'note' => 'required|string',
            'share_to_client' => 'nullable|boolean',
        ])->validate();

        return tap($report)->update($validated);
    }
}
