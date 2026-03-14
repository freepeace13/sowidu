<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Actions;

use Illuminate\Support\Arr;
use Modules\WorkLogs\Contracts\Actions\CreateManualWorkLog as CreateManualWorkLogContract;
use Modules\WorkLogs\Contracts\External\AuthorizationContract;
use Modules\WorkLogs\Models\WorkLog;
use Modules\WorkLogs\Traits\AsAction;

class CreateManualWorkLog extends BaseManualWorkLog implements CreateManualWorkLogContract
{
    use AsAction;

    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function handle(mixed $user, mixed $company, array $inputs): WorkLog
    {
        $validated = $this->validate($inputs);

        if ($user->getKey() !== $validated['employee']) {
            $this->authorization->authorize($user, 'createForOtherEmployees', WorkLog::class);
        }

        $validated['started_at'] = convert_to_timezone(
            $validated['started_at'],
            $validated['timezone'],
        );

        $validated['ended_at'] = convert_to_timezone(
            $validated['ended_at'],
            $validated['timezone'],
        );

        $workLog = WorkLog::make(Arr::only($validated, ['started_at', 'ended_at', 'notes', 'event', 'payment_form']));
        $workLog->duration_in_seconds = $this->getDurationInSeconds(
            $validated['started_at'],
            $validated['ended_at'],
        );

        $workLog->user()
            ->associate($validated['employee']);
        $workLog->company()
            ->associate($company);
        $workLog->author()
            ->associate($user);
        $workLog->save();

        if (filled($validated['notes'])) {
            $workLog->reports()
                ->create([
                    'note' => $validated['notes'],
                ]);
        }

        return $workLog;
    }
}
