<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\DuplicateBoardTask;
use Modules\Todos\Models\Task;
use Tests\TestCase;

class DuplicateBoardTaskTest extends TestCase
{
    use RefreshDatabase;

    protected DuplicateBoardTask $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DuplicateBoardTask;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function board_owner_can_duplicate_task()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, [
            'title' => 'Original Task',
            'description' => 'Task description',
        ]);

        $duplicatedTask = $this->action->duplicate($user, $board, $task);

        $this->assertInstanceOf(Task::class, $duplicatedTask);
        $this->assertStringStartsWith('Original Task', $duplicatedTask->title);
        $this->assertEquals('Task description', $duplicatedTask->description);
        $this->assertNotEquals($task->id, $duplicatedTask->id);
    }

    /** @test */
    public function subscriber_can_duplicate_task_when_permission_enabled()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->settings()->permissions()->update('members', 'can_manage_task', true);
        $board->addSubscriber($subscriber, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $duplicatedTask = $this->action->duplicate($subscriber, $board->fresh(['users', 'subscribers']), $task);

        $this->assertInstanceOf(Task::class, $duplicatedTask);
        $this->assertStringStartsWith('Task', $duplicatedTask->title);
    }

    /** @test */
    public function subscriber_cannot_duplicate_task_when_permission_disabled()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->settings()->permissions()->update('members', 'can_manage_task', false);
        $board->addSubscriber($subscriber, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->duplicate($subscriber, $board->fresh(['users', 'subscribers']), $task);
    }

    /** @test */
    public function non_subscriber_cannot_duplicate_task()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->duplicate($nonMember, $board, $task);
    }

    /** @test */
    public function duplicated_task_has_new_reporter()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->settings()->permissions()->update('members', 'can_manage_task', true);
        $board->addSubscriber($subscriber, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $duplicatedTask = $this->action->duplicate($subscriber, $board->fresh(['users', 'subscribers']), $task);

        $this->assertNotEquals($task->reporter_id, $duplicatedTask->reporter_id);
        $this->assertEquals($board->getSubscriber($subscriber)->id, $duplicatedTask->reporter_id);
    }

    /** @test */
    public function duplicated_task_preserves_original_data()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, [
            'title' => 'Task Title',
            'description' => 'Task Description',
            'group' => 'Backlog',
        ]);

        $duplicatedTask = $this->action->duplicate($user, $board, $task);

        $this->assertStringStartsWith($task->title, $duplicatedTask->title);
        $this->assertEquals($task->description, $duplicatedTask->description);
        $this->assertEquals($task->group, $duplicatedTask->group);
    }
}
