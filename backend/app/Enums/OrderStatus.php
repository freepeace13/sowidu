<?php

namespace App\Enums;

use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;
use Illuminate\Support\Str;

enum OrderStatus: int
{
    use From, InvokableCases, Names, Options, Values;

    // Codes:
    // - `0` = ending #'s are on client's view
    // - `1` = ending #'s are on contractor's view

    case IN_PREPARATION = 0;

    case WAITING_FOR_CLIENT_CONFIRMATION = 120;
    case WAITING_FOR_CONTRACTOR_CONFIRMATION = 121;
    case CONTRACTOR_WAITING_FOR_CLIENT_CONFIRMATION = 122;

    case COMMISSIONED = 1; // Approved by client party

    case WAITING_FOR_CONTRACTOR_TO_START = 13; // Confirmed but not yet started contractor
    case STARTED = 3; // Contractor started working

    case ONGOING = 4; // Order is in progress

    case READY_FOR_REVIEW = 5;
    case WAITING_FOR_CLIENT_REVIEW = 56;

    case REJECT = 6; // Client rejected the service of the `contractor`
    case NEEDS_REVISION = 61; // Contractor need to revise/update the order
    case WAITING_FOR_CONTRACTOR_REVISION = 60; // Contractor need to revise/update the order
    case WORK_ON_REVISIONS = 8;

    case FINISHED = 7; // Accepted by client party OR `FINISHED`
    case FULFILLED = 9; // Accepted by client party OR `FINISHED`

    case CANCELLED = 2; // Client `Cancelled` the order

    public static function asSelectItems(): array
    {
        $validCases = [0, 1, 3, 4, 5, 6, 8, 7, 9, 2];

        return collect(self::cases())
            ->filter(fn ($case) => in_array($case->value, $validCases))
            ->values()
            ->map(fn ($case) => [
                'value' => $case->value,
                'text' => Str::of($case->name)->replace('_', ' ')->title()->__toString(),
            ])
            ->toArray();
    }
}
