<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\TimeLog;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\TimeLog\AddTaskTimeLog;
use Modules\Todos\Models\TaskTimeLog;
use Tests\TestCase;

class AddTaskTimeLogTest extends TestCase
{
    use RefreshDatabase;

    protected AddTaskTimeLog $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new AddTaskTimeLog;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function board_owner_can_add_time_log()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->action->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '2h 30m',
            'description' => 'Worked on feature',
        ]);

        $this->assertInstanceOf(TaskTimeLog::class, $timeLog);
        $this->assertEquals('Worked on feature', $timeLog->description);
    }

    /** @test */
    public function subscriber_can_add_time_log()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($subscriber, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $timeLog = $this->action->add($subscriber, $task->fresh(['board']), [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $this->assertInstanceOf(TaskTimeLog::class, $timeLog);
    }

    /** @test */
    public function time_log_requires_date()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->add($user, $task, [
            'duration' => '1h',
        ]);
    }

    /** @test */
    public function time_log_requires_duration()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->add($user, $task, [
            'date' => now()->format('Y-m-d'),
        ]);
    }

    /** @test */
    public function time_log_date_cannot_be_in_future()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->add($user, $task, [
            'date' => now()->addDay()->format('Y-m-d'),
            'duration' => '1h',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_add_time_log()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->add($nonMember, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);
    }

    /** @test */
    public function time_log_author_is_set_correctly()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->action->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '30m',
        ]);

        $this->assertNotNull($timeLog->author);
        $this->assertEquals($user->id, $timeLog->author->user_id);
    }

    /** @test */
    public function time_log_accepts_hours_only()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->action->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '3h',
        ]);

        $this->assertInstanceOf(TaskTimeLog::class, $timeLog);
    }

    /** @test */
    public function time_log_accepts_minutes_only()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->action->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '45m',
        ]);

        $this->assertInstanceOf(TaskTimeLog::class, $timeLog);
    }

    /** @test */
    public function time_log_description_is_optional()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $timeLog = $this->action->add($user, $task, [
            'date' => now()->format('Y-m-d'),
            'duration' => '1h',
        ]);

        $this->assertNull($timeLog->description);
    }
}
