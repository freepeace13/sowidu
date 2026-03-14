<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Actions;

use Illuminate\Support\Arr;
use Modules\WorkLogs\Contracts\Actions\UpdateManualWorkLog as UpdateManualWorkLogContract;
use Modules\WorkLogs\Contracts\External\AuthorizationContract;
use Modules\WorkLogs\Models\WorkLog;
use Modules\WorkLogs\Traits\AsAction;

class UpdateManualWorkLog extends BaseManualWorkLog implements UpdateManualWorkLogContract
{
    use AsAction;

    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function handle(mixed $user, mixed $company, WorkLog $workLog, array $inputs)
    {
        $this->authorization->authorize($user, 'update', $workLog);

        $validated = $this->validate($inputs);

        $validated['started_at'] = convert_to_timezone(
            $validated['started_at'],
            $validated['timezone'],
        );

        $validated['ended_at'] = convert_to_timezone(
            $validated['ended_at'],
            $validated['timezone'],
        );

        $workLog->update(
            array_merge(
                Arr::only($validated, ['started_at', 'ended_at', 'notes', 'event', 'payment_form']),
                [
                    'duration_in_seconds' => $this->getDurationInSeconds(
                        $validated['started_at'],
                        $validated['ended_at'],
                    ),
                ],
            ),
        );

        $workLog->user()->associate($validated['employee']);
        $workLog->author()->associate($user);
        $workLog->save();

        if (filled($validated['notes'])) {
            $report = $workLog->reports()->first();
            if (!$report) {
                $workLog->reports()->create([
                    'note' => $validated['notes'],
                ]);
            } else {
                $report->update([
                    'note' => $validated['notes'],
                ]);
            }
        }

        return $workLog;
    }
}
