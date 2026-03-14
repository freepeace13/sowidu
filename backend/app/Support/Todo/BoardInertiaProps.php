<?php

namespace App\Support\Todo;

use App\Models\Board;
use App\Transformers\Todo\BoardTransformer;
use App\Transformers\Todo\GroupTransformer;
use App\Transformers\Todo\LabelTransformer;
use App\Transformers\Todo\SubscriberTransformer;
use Illuminate\Support\Facades\Gate;

class BoardInertiaProps
{
    public function __construct(protected Board $board) {}

    public function board()
    {
        return (new BoardTransformer($this->board))->resolve();
    }

    public function subscribers()
    {
        $this->board->loadMissing(['subscribers.user.profile.avatar']);

        return $this->board->subscribers->map(
            fn ($subsriber) => (new SubscriberTransformer($subsriber))
                ->withUser()
                ->resolve(),
        );
    }

    public function groups()
    {
        $filters = $this->queryFilters();

        return $this->board->settings()->groups()->all()->map(
            fn ($group) => (new GroupTransformer($group))->withTasksSummary(
                $this->board,
                $filters,
            )->resolve(),
        );
    }

    public function queryFilters()
    {
        return request()->query('filters', [
            'q' => null,
            'members' => [],
            'labels' => [],
            'unassigned' => false,
        ]);
    }

    public function filters()
    {
        return array_map(function ($filter) {
            if (is_array($filter)) {
                return array_map(function ($value) {
                    return (int) $value;
                }, $filter);
            }

            return $filter;
        }, $this->queryFilters());
    }

    public function labels()
    {
        return $this->board->settings()->labels()->all()->map(
            fn ($label) => (new LabelTransformer($label))->resolve(),
        );
    }

    public function boardDetails()
    {
        return (new BoardTransformer($this->board))->withOwner()->resolve();
    }

    public function settings()
    {
        return [
            'permissions' => $this->board->settings()->permissions()->all(),
            'labels_available_colors' => config('todo.board.settings.labels.available_colors'),
        ];
    }

    public function policies($user): array
    {
        $gate = Gate::forUser($user);

        return [
            'can_update_permissions' => $gate->check('updatePermission', $this->board),
            'can_manage_subscriber' => $gate->check('addSubscriber', $this->board),
            'can_manage_task' => $gate->check('createTask', $this->board),
            'can_manage_group' => $gate->check('createGroup', $this->board),
            'can_manage_label' => $gate->check('manageLabel', $this->board),
        ];
    }
}
