<?php

declare(strict_types=1);

namespace App\Services\WorkLogs;

use App\Transformers\ActivityLogReportTransformer;
use App\Transformers\PlaceTransformer;
use App\Transformers\UserTransformer;
use Modules\WorkLogs\Contracts\External\TransformerContract;

class TransformerAdapter implements TransformerContract
{
    public function transformUser(mixed $user): array
    {
        return (new UserTransformer($user))->resolve();
    }

    public function transformPlaceWithId(mixed $place): ?array
    {
        if ($place === null) {
            return null;
        }

        return (new PlaceTransformer($place))
            ->withId()
            ->resolve();
    }

    public function transformActivityLogReport(mixed $report): array
    {
        return (new ActivityLogReportTransformer($report))->resolve();
    }
}
