<?php

namespace Modules\Todos\Transformers;

use Modules\Todos\Models\Board;

class GroupTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'name' => $this['name'],
            'color' => $this['color'],
            'order' => $this['order'],
            'isDefault' => $this['isDefault'],
        ];
    }

    public function withTasksSummary(Board $board, array $filters = [])
    {
        return $this->state(function ($attributes) use ($board, $filters) {
            return [
                'tasks' => $board->tasks()
                    ->filter($filters)
                    ->with(['members.user.profile.avatar', 'labels'])
                    ->onGroup($attributes['name'])
                    ->get()
                    ->map(
                        fn ($task) => (new TaskTransformer($task))
                            ->withMembersSummary()
                            ->withLabels($board)
                            ->resolve(),
                    ),
            ];
        });
    }
}
