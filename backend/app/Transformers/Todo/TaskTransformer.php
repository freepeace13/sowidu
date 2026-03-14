<?php

namespace App\Transformers\Todo;

use App\Models\Board;
use App\Transformers\Transformer;
use App\Transformers\UserTransformer;

class TaskTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'group' => $this->group,
            'description' => $this->description,
            'reporter_id' => $this->reporter_id,
            'task_id' => $this->task_id,
            'board_id' => $this->board_id,
            'is_subtask' => $this->isSubTask(),
        ];
    }

    public function withMembers()
    {
        return $this->state(function () {
            return [
                'members' => $this->members->map(
                    fn ($member) => (new SubscriberTransformer($member))->resolve(),
                ),
            ];
        });
    }

    public function withMembersUser()
    {
        return $this->state(function () {
            return [
                'members' => $this->members->loadMissing('user')->map(
                    fn ($member) => (new SubscriberTransformer($member))->withUser()->resolve(),
                ),
            ];
        });
    }

    public function withMembersSummary()
    {
        return $this->state(function () {
            return [
                'members' => $this->members->map(
                    fn ($subscriber) => (new UserTransformer($subscriber->user))
                        ->resolve(),
                ),
            ];
        });
    }

    public function withLabels(Board $board)
    {
        return $this->state(function () use ($board) {
            return [
                'labels' => $this->labels->map(
                    fn ($taskLabel) => (new LabelTransformer(
                        $board->settings()
                            ->labels()
                            ->find($taskLabel->label_id),
                    )
                    )->resolve(),
                ),
            ];
        });
    }

    public function withParentTask()
    {
        return $this->state(function () {
            $parentTask = $this->loadMissing(['parentTask'])->parentTask;

            return [
                'parent_task' => $parentTask ? (new self($parentTask))->resolve() : null,
            ];
        });
    }
}
