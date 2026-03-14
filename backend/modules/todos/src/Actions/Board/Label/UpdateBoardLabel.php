<?php

namespace Modules\Todos\Actions\Board\Label;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Board;

class UpdateBoardLabel
{
    public function update($user, Board $board, int $labelId, array $params)
    {
        $labels = $board->settings()->labels();

        throw_validation_unless($labels->has($labelId), 'Board label does not exist.');

        Gate::forUser($user)->authorize('manageLabel', $board);

        $validated = $this->validate($board, $params);

        $labels->update($labelId, $validated['name'], $validated['color']);
    }

    protected function validate($board, $params)
    {
        return Validator::make($params, [
            'name' => [
                'nullable',
            ],
            'color' => [
                'required',
                'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i',
            ],
        ])->validated();
    }
}
