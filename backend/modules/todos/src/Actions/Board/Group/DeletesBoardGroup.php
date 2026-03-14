<?php

namespace Modules\Todos\Actions\Board\Group;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Board;

class DeletesBoardGroup
{
    public function delete($user, Board $board, string $groupName)
    {
        Gate::forUser($user)->authorize('deleteGroup', $board);

        Validator::make([
            'name' => $groupName,
        ], [
            'name' => 'required',
        ])->validateWithBag('deleteBoardGroup');

        $this->ensureGroupIsExistsAndNotDefault($board, $groupName);

        $boardGroups = $board->settings()->groups();

        $boardGroups->remove($groupName);

        // Move tasks that under on this group
        $boardGroups->moveTasksToFallback($groupName);
    }

    protected function ensureGroupIsExistsAndNotDefault(Board $board, $groupName)
    {
        $settings = $board->settings()->groups();

        throw_validation_unless(
            $settings->has($groupName),
            'The board group not exists.',
            'name',
        );

        throw_validation_if(
            $settings->isDefault($groupName),
            'The board group is default and cannot be deleted.',
            'name',
        );
    }
}
