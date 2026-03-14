<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\WorkLogs\Enums\PaymentForm;
use Modules\WorkLogs\Enums\WorkLogEvent;

class BaseManualWorkLog
{
    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'date_start' => 'required|date_format:Y-m-d',
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i',
            'started_at' => 'required|date_format:Y-m-d H:i',
            'ended_at' => 'required|date_format:Y-m-d H:i|after:started_at',
            'notes' => 'nullable',
            'event' => 'required|' . Rule::in(Arr::pluck(WorkLogEvent::options(), 'value')),
            'payment_form' => ['nullable', 'integer', Rule::in(PaymentForm::values())],
            'employee' => 'required|numeric|exists:users,id',
            'timezone' => 'required|timezone',
        ])->validate();
    }

    public function getDurationInSeconds(string|Carbon $startedAt, string|Carbon $endedAt): int
    {
        $start = $startedAt instanceof Carbon ? $startedAt : Carbon::parse($startedAt);
        $end = $endedAt instanceof Carbon ? $endedAt : Carbon::parse($endedAt);

        return $start->diffInSeconds($end);
    }
}
