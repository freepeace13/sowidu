<?php

namespace Modules\Todos\Actions\Board;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Modules\Todos\Events\BoardUpdated;
use Modules\Todos\Models\Board;

class UpdateBoard extends TodoBoard
{
    public function update($user, Board $board, array $params)
    {
        Gate::forUser($user)->authorize('update', $board);

        $validated = $this->validate($user, $params, ['title' => 'required']);

        $this->ensurePredefinedBoardCannotBeUpdated($board, $validated);

        // Check if $params has `logo` - if TRUE  save logo and update `board`
        if (isset($validated['logo']) && $validated['logo'] instanceof UploadedFile) {
            $this->saveLogo($board, $validated['logo']);
        }

        $board->update($validated);

        BoardUpdated::dispatch($board);

        return $board;
    }

    protected function ensurePredefinedBoardCannotBeUpdated(Board $board, array $params)
    {
        if (!$board->isPredefined()) {
            return;
        }

        // Board is `pre-defined` - disable update except for description
        throw_validation_unless(Arr::first(
            Arr::pluck(
                config('todo.board.predefined'),
                'title',
            ),
            fn ($value) => strtolower($value) === strtolower($params['title']),
        ), 'Pre-defined boards title cannot be edited.', 'title');

        throw_validation_if(
            $params['logo'] ?? null,
            'Pre-defined boards logo cannot be edited.',
            'logo',
        );
    }
}
