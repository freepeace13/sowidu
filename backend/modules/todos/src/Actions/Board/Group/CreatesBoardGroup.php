<?php

namespace Modules\Todos\Actions\Board\Group;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Board;

class CreatesBoardGroup
{
    public function create($user, Board $board, array $params)
    {
        Gate::forUser($user)->authorize('createGroup', $board);

        Validator::make($params, [
            'name' => 'required',
            'color' => 'nullable',
        ])->after($this->ensureGroupNameIsUnique($board, $params['name']))
            ->validateWithBag('createBoardGroup');

        $settings = $board->settings()->groups();

        $settings->add(
            $params['name'],
            $params['color'] ?? null,
        );

        return $settings->get($params['name']);
    }

    protected function ensureGroupNameIsUnique($board, $groupName)
    {
        return function ($validator) use ($board, $groupName) {
            $validator->errors()->addIf(
                $board->settings()->groups()->has($groupName),
                'name',
                'The group name already exists.',
            );
        };
    }
}
