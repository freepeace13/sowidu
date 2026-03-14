<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\TimeLog;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\TimeLog\AddTaskTimeLog;
use Modules\Todos\Actions\Board\Task\TimeLog\DeleteTaskTimeLog;
use Tests\TestCase;

class DeleteTaskTimeLogTest extends TestCase
{
    use RefreshDatabase;

    protected DeleteTaskTimeLog $action;

    protected AddTaskTimeLog $addTimeLogAction;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteTaskTimeLog;
        $this->addTimeLogAction = new AddTaskTimeLog;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function time_log_owner_can_delete()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->addTimeLogAction->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $timeLogId = $timeLog->id;

        $this->action->delete($user, $task, $timeLog);

        $this->assertDatabaseMissing('todo_task_time_logs', ['id' => $timeLogId]);
    }

    /** @test */
    public function non_owner_cannot_delete_time_log()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($otherUser, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $timeLog = $this->addTimeLogAction->add($owner, $task->fresh(['board']), [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($otherUser, $task->fresh(['board']), $timeLog);
    }

    /** @test */
    public function non_subscriber_cannot_delete_time_log()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $timeLog = $this->addTimeLogAction->add($owner, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($nonMember, $task, $timeLog);
    }

    /** @test */
    public function subscriber_can_delete_own_time_log()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($subscriber, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $timeLog = $this->addTimeLogAction->add($subscriber, $task->fresh(['board']), [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $timeLogId = $timeLog->id;

        $this->action->delete($subscriber, $task->fresh(['board']), $timeLog);

        $this->assertDatabaseMissing('todo_task_time_logs', ['id' => $timeLogId]);
    }
}
