<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Modules\Todos\Actions\Board\GetBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\DeleteBoardTask;
use Modules\Todos\Actions\Board\Task\UpdatesBoardTask;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;
use Modules\Todos\Support\BoardInertiaProps;
use Modules\Todos\Transformers\BoardTransformer;
use Modules\Todos\Transformers\SubscriberTransformer;
use Modules\Todos\Transformers\TaskAttachmentTransformer;
use Modules\Todos\Transformers\TaskLabelTransformer;
use Modules\Todos\Transformers\TaskTransformer;

class TaskController extends InertiaController
{
    public function index(Request $request, Board $board)
    {

        $this->authorize('viewTasks', $board);

        $boardProps = new BoardInertiaProps($board);

        return Inertia::render('Show', [
            'board' => $boardProps->board(),
            'subscribers' => $boardProps->subscribers(),
            'groups' => $boardProps->groups(),

            'boards' => Inertia::lazy(
                fn () => app(GetBoard::class)->get(
                    $request->user(),
                    ['team_id' => $this->getCurrentTeamId()],
                )->map(fn ($board) => (new BoardTransformer($board))->resolve()),
            ),
            'taskMembers' => [],
        ]);
    }

    public function showTaskGroup(Request $request, Board $board)
    {
        $this->authorize('viewTasks', $board);

        $boardProps = new BoardInertiaProps($board);

        return Inertia::render('TaskGroup', [
            'board' => $boardProps->board(),
            'subscribers' => $boardProps->subscribers(),
            'groups' => $boardProps->groups(),
            'filters' => $boardProps->filters(),
            'labels' => $boardProps->labels(),
            'settings' => $boardProps->settings(),
            'policies' => $boardProps->policies($request->user()),
            'details' => Inertia::lazy(
                fn () => $boardProps->boardDetails(),
            ),
            'boards' => Inertia::lazy(
                fn () => app(GetBoard::class)->get(
                    $request->user(),
                    ['team_id' => $this->getCurrentTeamId()],
                )->map(fn ($board) => (new BoardTransformer($board))->resolve()),
            ),
            'task' => null,
            'subtasks' => [],
            'attachments' => [],
            'taskLabels' => [],
            'taskMembers' => [],
        ]);
    }

    public function show(Request $request, Board $board, Task $task)
    {
        $this->authorize('view', $task);
        $boardProps = new BoardInertiaProps($board);

        $authSubscription = $board->getSubscription(auth_user());

        return Inertia::render('TaskGroup', [
            'task' => (new TaskTransformer($task))->withParentTask()->resolve(),
            'taskMembers' => $task->loadMissing(['members.user.profile.avatar'])->members->map(
                fn ($member) => (new SubscriberTransformer($member))->withUser()->resolve(),
            ),
            'taskLabels' => $task->labels->map(
                fn ($label) => (new TaskLabelTransformer($label))->withLabel()->resolve(),
            ),
            'subtasks' => fn () => $task->subtasks()->get()->map(
                fn ($task) => (new TaskTransformer($task->loadMissing(['members'])))->withMembersUser()->resolve(),
            ),
            'attachments' => Inertia::lazy(fn () => $task->attachments()->get()->map(
                fn ($attachment) => (new TaskAttachmentTransformer($attachment))
                    ->withIsMineTag($authSubscription)->resolve(),
            )),
            'board' => $boardProps->board(),
            'subscribers' => $boardProps->subscribers(),
            'policies' => $boardProps->policies($request->user()),
            'groups' => Inertia::lazy(fn () => $boardProps->groups()),
            'filters' => Inertia::lazy(fn () => $boardProps->filters()),
            'labels' => Inertia::lazy(fn () => $boardProps->labels()),
            'settings' => Inertia::lazy(fn () => $boardProps->settings()),
        ]);
    }

    public function store(Request $request, Board $board)
    {
        app(CreatesBoardTask::class)->create(
            $request->user(),
            $board,
            $request->all(),
        );

        return back(303);
    }

    public function update(Request $request, Board $board, Task $task)
    {
        app(UpdatesBoardTask::class)->update(
            $request->user(),
            $task,
            $request->all(),
        );

        return back(303);
    }

    public function destroy(Request $request, Board $board, Task $task)
    {
        app(DeleteBoardTask::class)->delete($request->user(), $task);

        return Redirect::route('todos.boards.task.tasks-group', ['board' => $board], 303);
    }
}
