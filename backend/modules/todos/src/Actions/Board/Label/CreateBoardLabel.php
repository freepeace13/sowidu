<?php

namespace Modules\Todos\Actions\Board\Label;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Board;

class CreateBoardLabel
{
    public function create($user, Board $board, array $params)
    {
        Gate::forUser($user)->authorize('manageLabel', $board);

        $validated = $this->validate($board, $params);

        return $board->settings()->labels()->add($validated['name'], $validated['color']);
    }

    protected function validate($board, $params)
    {
        return Validator::make($params, [
            'name' => [
                'nullable',
                function ($attribute, $value, $fail) use ($board) {
                    if ($value == '') {
                        return;
                    }

                    if ($board->settings()->labels()->hasName($value)) {
                        $fail('Label name already exist, please choose another one.');
                    }
                },
            ],
            'color' => [
                'required',
                'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i',
            ],
        ])->validated();
    }
}
