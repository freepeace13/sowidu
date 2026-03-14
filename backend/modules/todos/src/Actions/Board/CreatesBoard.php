<?php

namespace Modules\Todos\Actions\Board;

use Illuminate\Http\UploadedFile;
use Modules\Todos\Models\Board;

class CreatesBoard extends TodoBoard
{
    public function create($user, array $params)
    {
        $board = Board::create(
            $validated = $this->validate($user, $params, ['title' => 'required']),
        );

        // Check if $params has `logo` - if TRUE  save logo and update `board`
        if (isset($validated['logo']) && $validated['logo'] instanceof UploadedFile) {
            $this->saveLogo($board, $validated['logo']);
        }

        $board->users()->attach($user->getKey(), [
            'role' => 'owner',
            'settings' => $validated['settings'] ?? [],
        ]);

        return $board;
    }
}
