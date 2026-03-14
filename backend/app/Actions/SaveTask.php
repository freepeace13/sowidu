<?php

namespace App\Actions;

use App\Events\Task\TaskUpdate;
use App\Models\States\TaskStates\FinishedState;
use App\Models\States\TaskStates\InProgressState;
use App\Models\States\TaskStates\ProgressState;
use App\Models\Task;
use App\Notifications\Common\MemberAdded;
use App\Notifications\Common\StateUpdated;
use App\Support\Collections\MembersCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**@todo this is not used */
class SaveTask
{
    protected $task;

    protected $actor;

    public function __construct(Task $task, $actor = null)
    {
        $this->task = $task;
        $this->actor = $actor ?? $task->creator;
    }

    /**
     * @return \App\Models\Task
     */
    public function __invoke(Request $request)
    {
        $attributes = $request->only('title', 'description');

        $this->task->fill($attributes)->save();

        $this->syncMembers($this->task, $request);
        $this->syncAttachments($this->task, $request);

        $this->handleStateTransitions($this->task, $request);

        TaskUpdate::broadcast($this->task);

        return $this->task;
    }

    protected function syncMembers(Task $instance, Request $request)
    {
        $original = $instance->members;

        $instance = $instance->syncMembers(MembersCollection::make(
            $members = $request->input('members', $original),
        ));

        MembersCollection::make($original)
            ->diffWith($members)
            ->each(function ($member) use ($instance) {
                $member->notify(new MemberAdded($instance, $this->actor));
            });
    }

    protected function syncAttachments(Task $instance, Request $request)
    {
        $instance
            ->syncDeliveries($request->deliveries)
            ->syncOrders($request->orders)
            ->syncMedia($request->media);
    }

    protected function handleStateTransitions(Task $instance, Request $request)
    {
        $newState = $request->input('state', $instance->state->getValue());

        if ($instance->state->is($newState)) {
            return;
        }

        $newStateClass = ProgressState::resolveStateClass($newState);

        if ($newStateClass === InProgressState::class) {
            $instance->forceFill([
                'started_at' => Carbon::now(),
            ])->save();
        } elseif ($newStateClass === FinishedState::class) {
            $instance->forceFill([
                'ended_at' => Carbon::now(),
            ])->save();
        }

        $instance->state->transitionTo(
            ProgressState::resolveStateClass($newState),
            $instance,
        );

        $this->sendStateUpdateNotification(
            $instance->notifiables()->filter(function ($member) {
                return !$member->is($this->actor);
            }),
        );
    }

    protected function sendStateUpdateNotification($notifiables)
    {
        collect($notifiables)->each(function ($notifiable) {
            $notifiable->notify(new StateUpdated($this->task, $this->actor));
        });
    }
}
