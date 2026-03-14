<?php

namespace Modules\Todos\Actions\Board\Group;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Board;

class UpdateBoardGroup
{
    public function update($user, Board $board, string $groupName, array $params)
    {
        Gate::forUser($user)->authorize('updateGroup', $board);

        $boardGroups = $board->settings()->groups();

        throw_validation_unless($boardGroups->has($groupName), 'Board group does not exist.');

        $validated = $this->validate($board, $params);

        if (array_key_exists('groups', $validated)) {
            // Update groups order
            $boardGroups->updateOrder($validated['groups']);

            return $boardGroups->all();
        }

        $newGroupName = $boardGroups->update($groupName, [
            'name' => $validated['name'],
            'color' => $validated['color'] ?? null,
        ])['name'];

        $board->tasks()->onGroup($groupName)->update([
            'group' => $newGroupName,
        ]);

        return $boardGroups->get($validated['name']);
    }

    protected function validate($board, array $params): array
    {
        $groupOrders = $board->settings()->groups()->all()->map(function ($group) {
            return $group['order'];
        });

        return Validator::make($params, [
            'name' => 'required_without:groups',
            'color' => 'nullable',
            'groups' => [
                'nullable',
                'array',
            ],
            'groups.*.name' => [
                'required_with:groups',
                'string',
            ],
            'groups.*.order' => [
                'required_with:groups',
                'between:' . implode(',', [$groupOrders->min(), $groupOrders->max()]),
            ],
        ])->validate();
    }
}
