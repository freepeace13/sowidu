<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Models\Task;
use Tests\TestCase;

class CreatesBoardTaskTest extends TestCase
{
    use RefreshDatabase;

    protected CreatesBoardTask $action;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_create_task()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $task = $this->action->create($user, $board, [
            'title' => 'New Task',
        ]);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('New Task', $task->title);
        $this->assertEquals($board->id, $task->board_id);
    }

    /** @test */
    public function task_is_assigned_to_default_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $task = $this->action->create($user, $board, [
            'title' => 'New Task',
        ]);

        $this->assertEquals('Backlog', $task->group);
    }

    /** @test */
    public function task_can_be_created_in_specific_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $task = $this->action->create($user, $board, [
            'title' => 'Task In Progress',
            'group' => 'In Progress',
        ]);

        $this->assertEquals('In Progress', $task->group);
    }

    /** @test */
    public function task_requires_title()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'title' => '',
        ]);
    }

    /** @test */
    public function task_cannot_be_created_in_invalid_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'title' => 'Task',
            'group' => 'NonExistent Group',
        ]);
    }

    /** @test */
    public function subscriber_can_create_task_when_permission_allowed()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $task = $this->action->create($guest, $board->fresh(['users', 'subscribers']), [
            'title' => 'Guest Task',
        ]);

        $this->assertInstanceOf(Task::class, $task);
    }

    /** @test */
    public function non_subscriber_cannot_create_task()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->expectException(AuthorizationException::class);

        $this->action->create($nonMember, $board, [
            'title' => 'Unauthorized Task',
        ]);
    }

    /** @test */
    public function subtask_can_be_created_under_parent_task()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $parentTask = $this->action->create($user, $board, ['title' => 'Parent Task']);

        $subtask = $this->action->create($user, $board, [
            'title' => 'Subtask',
            'task_id' => $parentTask->id,
        ]);

        $this->assertTrue($subtask->isSubTask());
        $this->assertEquals($parentTask->id, $subtask->task_id);
    }

    /** @test */
    public function subtask_cannot_have_subtask()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $parentTask = $this->action->create($user, $board, ['title' => 'Parent Task']);
        $subtask = $this->action->create($user, $board, [
            'title' => 'Subtask',
            'task_id' => $parentTask->id,
        ]);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'title' => 'Sub-Subtask',
            'task_id' => $subtask->id,
        ]);
    }

    /** @test */
    public function task_reporter_is_set_correctly()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $task = $this->action->create($user, $board, ['title' => 'Test Task']);

        $this->assertNotNull($task->reporter);
        $this->assertEquals($user->id, $task->reporter->user_id);
    }
}
