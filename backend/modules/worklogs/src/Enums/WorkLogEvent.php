<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Values;
use Illuminate\Support\Str;
use Modules\WorkLogs\Enums\MetaProperties\Color;

#[Meta(Color::class)]
enum WorkLogEvent: int
{
    use InvokableCases;
    use Metadata;
    use Values;

    #[Color('#4CAF50')]
    case FINISHED_WORKING = 1;

    #[Color('#FF9800')]
    case CURRENTLY_WORKING = 2;

    #[Color('#F44336')]
    case HOLIDAY = 3;

    #[Color('#FBC02D')]
    case SICK = 4;

    #[Color('#AFB42B')]
    case PROFESSIONAL_SCHOOL = 5;

    #[Color('#9C27B0')]
    case GUILD_COURSE = 6;

    #[Color('#4DD0E1')]
    case CELEBRATION_DAY = 7;

    public static function forManualEntry(): array
    {
        $cases = self::cases();
        $excludeCases = ['FINISHED_WORKING', 'CURRENTLY_WORKING'];

        return collect($cases)
            ->reject(fn ($case) => in_array($case->name, $excludeCases))
            ->map(fn ($case) => [
                'text' => Str::of($case->name)->title()
                    ->replace(['_'], ' ')
                    ->__toString(),
                'value' => $case->value,
            ])
            ->values()
            ->toArray();
    }

    public static function options(): array
    {
        $cases = self::cases();

        return collect($cases)
            ->map(fn ($case) => [
                'text' => Str::of($case->name)->title()
                    ->replace('_', ' ')
                    ->__toString(),
                'value' => $case->value,
                'color' => $case->color(),
            ])
            ->toArray();
    }
}
