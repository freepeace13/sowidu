<?php

namespace Modules\Todos\Support;

use Modules\Todos\Models\Board;

class BoardSettings
{
    protected $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    public function groups()
    {
        return new BoardGroupRepository($this->board, 'groups');
    }

    public function labels(): BoardLabelRepository
    {
        return new BoardLabelRepository($this->board, 'labels');
    }

    public function permissions(): BoardPermissionsRepository
    {
        return new BoardPermissionsRepository($this->board, 'permissions');
    }

    public function saveDefault()
    {
        // Add groups
        collect(config('todo.board.settings.groups.defaults', []))
            ->each(function ($group, $key) {
                $this->groups()->add($group['name'], $group['color'], true);
            });

        $this->labels()->saveDefaultLabels();

        $this->permissions()->saveDefault();
    }
}
