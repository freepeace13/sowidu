<?php

namespace Modules\Todos\Actions\Board;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Todos\Models\Board;

class UpdateBoardPermissions
{
    public function update($user, Board $board, array $params): Board
    {
        Gate::forUser($user)->authorize('updatePermission', $board);

        $validated = $this->validate($board, $params);

        $board->settings()->permissions()->update(...$validated);

        return $board;
    }

    protected function validate(Board $board, array $params): array
    {
        return Validator::make($params, [
            'role' => [
                'required',
                Rule::in(array_keys(config('todo.board.defaults.permissions'))),
            ],
            'permission' => 'required',
            'value' => 'required|boolean',
        ])->validate();
    }
}
