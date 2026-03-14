<?php

namespace Modules\Todos\Actions\Board\Task\TimeLog;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TaskTimeLogBase
{
    protected function validate(array $params): array
    {
        $validated = Validator::make($params, [
            'date' => 'required|string|date|date_format:Y-m-d|before_or_equal:today',
            'duration' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $hour = $this->validateHour($value);
                    $minute = $this->validateMinute($value);

                    // Must have a minute value or hour
                    if (!$hour && !$minute) {
                        $fail('Must enter a value for hours or minutes.');
                    }
                },
            ],
            'description' => 'nullable|string',
        ])->validate();

        // Transform `duration` to time
        return array_merge($validated, ['duration' => $this->durationToTime($validated['duration'])]);
    }

    protected function durationToTime(string $duration)
    {
        return rescue(function () use ($duration) {
            $duration = CarbonInterval::make($duration);

            return now()->hour($duration->hours)->minute($duration->minutes)->second($duration->seconds)->format('H:i');
        }, function () {
            throw_validation('Must follow format for entering hours or minutes.', 'duration');
        }, true); // @todo change to `false`
    }

    protected function validateHour(string $duration): ?string
    {
        $duration = Str::of($duration)->trim();
        $hour = null;

        if ($duration->contains('h')) {
            $hour = (string) $duration->before('h');
            throw_validation_unless(is_numeric($hour), 'Hour must be a numeric value.', 'duration');
        }

        return $hour;
    }

    protected function validateMinute(string $duration): ?string
    {
        $duration = Str::of($duration)->trim();
        $minute = null;

        if ($duration->contains('m')) {
            $minute = $duration->contains('h')
                ? (string) $duration->between('h ', 'm') : (string) $duration->before('m');

            throw_validation_unless(is_numeric($minute), 'Minute must be a numeric value.', 'duration');
        }

        return $minute;
    }
}
