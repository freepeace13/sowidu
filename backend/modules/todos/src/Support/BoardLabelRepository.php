<?php

namespace Modules\Todos\Support;

use Illuminate\Support\Arr;
use Modules\Todos\Models\Board;

class BoardLabelRepository
{
    protected $board;

    protected $storeKey;

    public function __construct(Board $board, $storeKey)
    {
        $this->board = $board;
        $this->storeKey = $storeKey;
    }

    public function all()
    {
        return collect($this->board->settings[$this->storeKey] ?? [])
            ->map(function ($label) {
                return array_replace($label, [
                    'isDefault' => $this->isDefault($label),
                ]);
            });
    }

    public function isDefault(array $label): bool
    {
        $defaults = collect(config('todo.board.settings.labels.defaults'));

        return $label['isDefault'] || $defaults->contains('color', $label['color']);
    }

    public function has($id)
    {
        return $this->find($id);
    }

    public function find($id)
    {
        $labels = $this->board->settings[$this->storeKey] ?? [];

        return Arr::first($labels, function ($label) use ($id) {
            return $label['id'] == $id;
        });
    }

    public function hasName($name)
    {
        return Arr::first($this->all(), function ($label) use ($name) {
            return !$label['isDefault'] && $label['name'] == $name;
        });
    }

    public function add($name, string $color, bool $isDefault = false)
    {
        abort_if($name && $this->hasName($name), 'Label name already exist.');

        $nextId = (int) $this->all()->sortBy('id')->last()['id'] + 1;

        $newLabels = $this->all()
            ->concat([
                [
                    'id' => $nextId,
                    'name' => $name,
                    'color' => $color,
                    'isDefault' => $isDefault,
                ],
            ])
            ->values()
            ->all();

        $this->save($newLabels);
    }

    public function update(int $id, string $name, string $color)
    {
        abort_unless($this->has($id), 'Board label does not exist.');

        $updatedLabels = $this->all()->map(function ($label) use ($id, $name, $color) {
            return $label['id'] == $id ? array_merge($label, [
                'name' => $name,
                'color' => $color,
            ]) : $label;
        })->toArray();

        $this->save($updatedLabels);
    }

    public function remove($labelId)
    {
        abort_unless($label = $this->has($labelId), 'Board label does not exist.');

        abort_if($label['isDefault'], 'This label is default from this board, you cannot delete it.');

        $updatedLabels = $this->all()
            ->reject(function ($lab) use ($labelId) {
                return $lab['id'] == $labelId;
            })
            ->values()
            ->all();

        $this->save($updatedLabels);
    }

    protected function save(array $newLabels)
    {
        $settings = $this->board->settings;

        Arr::set($settings, $this->storeKey, $newLabels);

        $this->board->forceFill([
            'settings' => $settings,
        ])->save();
    }

    public function clear()
    {
        $settings = $this->board->settings;

        $this->board->forceFill([
            'settings' => ['groups' => $settings['groups']],
        ])->save();
    }

    public function saveDefaultLabels()
    {
        $defaultLabels = collect(config('todo.board.settings.labels.defaults', []))
            ->map(function ($label, $id) {
                return [
                    'id' => $id + 1,
                    'name' => $label['name'],
                    'color' => $label['color'],
                    'isDefault' => true,
                ];
            })->toArray();

        $this->save($defaultLabels);
    }
}
