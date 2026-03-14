<?php

namespace App\Support\Todo;

use App\Exceptions\Todo\BoardGroupAlreadyExist;
use App\Exceptions\Todo\BoardGroupNotExist;
use App\Exceptions\Todo\CannotRemoveDefaultBoardGroup;
use App\Models\Board;
use Illuminate\Support\Arr;

class BoardGroupRepository
{
    protected $board;

    protected $storeKey;

    public function __construct(Board $board, $storeKey)
    {
        $this->board = $board;
        $this->storeKey = $storeKey;
    }

    protected function fallbackGroup(): string
    {
        return config('todo.board.settings.groups.fallback');
    }

    public function all()
    {
        return collect($this->board->settings[$this->storeKey] ?? [])
            ->map(function ($group) {
                return array_replace($group, [
                    'isDefault' => $this->isDefault($group['name']),
                ]);
            });
    }

    public function isDefault($groupName)
    {
        if (!$group = $this->get($groupName)) {
            return false;
        }

        $defaults = collect(config('todo.board.settings.groups.defaults'));

        return $group['isDefault'] ||
            $defaults->contains('name', $groupName);
    }

    public function has($name)
    {
        return $this->get($name) !== null;
    }

    public function get($name)
    {
        $groups = $this->board->settings[$this->storeKey] ?? [];

        return Arr::first($groups, function ($group) use ($name) {
            return $group['name'] == $name;
        });
    }

    public function add($name, $color = null, bool $isDefault = false)
    {
        if ($this->has($name)) {
            throw new BoardGroupAlreadyExist;
        }

        $nextOrder = ((int) $this->all()->max('order') ?? 0) + 1;

        $newGroups = $this->all()
            ->concat([
                [
                    'name' => $name,
                    'color' => $color,
                    'order' => $nextOrder,
                    'isDefault' => $isDefault,
                ],
            ])
            ->sortBy('order')
            ->values()
            ->all();

        $this->save($newGroups);
    }

    public function update(string $name, array $newValues): array
    {
        throw_unless($this->get($name), 'Board group does not exist.');

        $updatedGroups = $this->all()->map(function ($group) use ($name, $newValues) {
            return $group['name'] == $name ? array_merge($group, $newValues) : $group;
        })->toArray();

        $this->save($updatedGroups);

        return $this->get($newValues['name']);
    }

    public function updateOrder(array $newGroupsOrder)
    {
        $updatedGroupOrders = $this->all()->map(function ($group) use ($newGroupsOrder) {
            $newOrder = Arr::first($newGroupsOrder, fn ($g) => $g['name'] == $group['name']);

            abort_unless($newOrder, 'Group data is not found.');

            return array_replace($group, [
                'order' => $newOrder['order'],
            ]);
        })
            ->sortBy('order')
            ->values()
            ->toArray();

        $this->save($updatedGroupOrders);
    }

    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new BoardGroupNotExist;
        }

        if ($this->isDefault($name)) {
            throw new CannotRemoveDefaultBoardGroup;
        }

        $newGroups = $this->all()
            ->reject(function ($group) use ($name) {
                return $group['name'] == $name;
            })
            ->sortBy('order')
            ->values()
            ->all();

        $this->save($newGroups);
    }

    public function moveTasksToFallback(string $from)
    {
        $this->moveTasks($from, $this->fallbackGroup());
    }

    public function moveTasks(string $from, string $to)
    {
        abort_unless($this->has($to), 'Board group does not exist.');

        $this->board->tasks()->onGroup($from)->update([
            'group' => $to,
        ]);
    }

    protected function save(array $newGroups)
    {
        $settings = $this->board->settings;

        Arr::set($settings, $this->storeKey, $newGroups);

        $this->board->forceFill([
            'settings' => $settings,
        ])->save();
    }
}
