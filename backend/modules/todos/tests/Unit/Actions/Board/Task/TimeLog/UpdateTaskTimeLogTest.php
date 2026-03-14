<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\TimeLog;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\TimeLog\AddTaskTimeLog;
use Modules\Todos\Actions\Board\Task\TimeLog\UpdateTaskTimeLog;
use Tests\TestCase;

class UpdateTaskTimeLogTest extends TestCase
{
    use RefreshDatabase;

    protected UpdateTaskTimeLog $action;

    protected AddTaskTimeLog $addTimeLogAction;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateTaskTimeLog;
        $this->addTimeLogAction = new AddTaskTimeLog;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function time_log_owner_can_update()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->addTimeLogAction->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
            'description' => 'Original description',
        ]);

        $updatedTimeLog = $this->action->update($user, $task, $timeLog, [
            'date' => now()->subDay()->format('Y-m-d'),
            'duration' => '2h 30m',
            'description' => 'Updated description',
        ]);

        $this->assertEquals('Updated description', $updatedTimeLog->description);
    }

    /** @test */
    public function non_owner_cannot_update_time_log()
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

        $this->action->update($otherUser, $task->fresh(['board']), $timeLog, [
            'date' => now()->format('Y-m-d'),
            'duration' => '2h',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_update_time_log()
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

        $this->action->update($nonMember, $task, $timeLog, [
            'date' => now()->format('Y-m-d'),
            'duration' => '2h',
        ]);
    }

    /** @test */
    public function update_requires_date()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->addTimeLogAction->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $task, $timeLog, [
            'duration' => '2h',
        ]);
    }

    /** @test */
    public function update_requires_duration()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->addTimeLogAction->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $task, $timeLog, [
            'date' => now()->format('Y-m-d'),
        ]);
    }

    /** @test */
    public function update_date_cannot_be_in_future()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->addTimeLogAction->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $task, $timeLog, [
            'date' => now()->addDay()->format('Y-m-d'),
            'duration' => '1h',
        ]);
    }
}
