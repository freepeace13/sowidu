<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\DeleteBoardTask;
use Tests\TestCase;

class DeleteBoardTaskTest extends TestCase
{
    use RefreshDatabase;

    protected DeleteBoardTask $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteBoardTask;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_delete_task()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task to Delete']);
        $taskId = $task->id;

        $result = $this->action->delete($user, $task);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('todo_tasks', ['id' => $taskId]);
    }

    /** @test */
    public function non_subscriber_cannot_delete_task()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($nonMember, $task);
    }

    /** @test */
    public function task_member_can_delete_task()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $this->actingAs($owner);
        $task->addMember($member);
        $taskId = $task->id;

        $this->actingAs($member);
        $result = $this->action->delete($member, $task->fresh(['board', 'members']));

        $this->assertTrue($result);
        $this->assertDatabaseMissing('todo_tasks', ['id' => $taskId]);
    }

    /** @test */
    public function co_owner_can_delete_any_task()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $taskId = $task->id;

        $result = $this->action->delete($coOwner, $task->fresh(['board']));

        $this->assertTrue($result);
        $this->assertDatabaseMissing('todo_tasks', ['id' => $taskId]);
    }

    /** @test */
    public function deleting_parent_task_is_allowed()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $parentTask = $this->createTaskAction->create($user, $board, ['title' => 'Parent']);
        $this->createTaskAction->create($user, $board, [
            'title' => 'Subtask',
            'task_id' => $parentTask->id,
        ]);
        $parentId = $parentTask->id;

        $result = $this->action->delete($user, $parentTask);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('todo_tasks', ['id' => $parentId]);
    }

    /** @test */
    public function guest_without_permission_cannot_delete_task()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $board->settings()->permissions()->update('members', 'can_manage_task', false);
        $board->addSubscriber($guest, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($guest, $task->fresh(['board']));
    }
}
