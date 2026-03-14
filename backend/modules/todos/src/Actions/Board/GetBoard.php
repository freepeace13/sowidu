<?php

namespace Modules\Todos\Actions\Board;

use Illuminate\Support\Arr;
use Modules\Todos\Models\Board;

class GetBoard
{
    public function get($user, array $params)
    {
        $teamId = Arr::has($params, 'team_id') ? $params['team_id'] : null;

        return Board::query()
            ->with([
                'subscriberOwner.user',
            ])
            ->where('team_id', $teamId)
            ->filter($params)
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->getKey());
            })
            ->orderByTaskLastUpdated()
            ->get();
    }
}
