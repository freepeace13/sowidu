<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\UpdatesBoardTask;
use Tests\TestCase;

class UpdatesBoardTaskTest extends TestCase
{
    use RefreshDatabase;

    protected UpdatesBoardTask $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdatesBoardTask;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_update_task_title()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Original Title']);

        $this->actingAs($user);
        $updatedTask = $this->action->update($user, $task, ['title' => 'Updated Title']);

        $this->assertEquals('Updated Title', $updatedTask->title);
    }

    /** @test */
    public function owner_can_update_task_description()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Test Task']);

        $this->actingAs($user);
        $updatedTask = $this->action->update($user, $task, [
            'description' => 'New description content',
        ]);

        $this->assertEquals('New description content', $updatedTask->description);
    }

    /** @test */
    public function owner_can_move_task_to_different_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->actingAs($user);
        $updatedTask = $this->action->update($user, $task, ['group' => 'Done']);

        $this->assertEquals('Done', $updatedTask->group);
    }

    /** @test */
    public function task_cannot_be_moved_to_invalid_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $task, ['group' => 'Invalid Group']);
    }

    /** @test */
    public function non_subscriber_cannot_update_task()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->update($nonMember, $task, ['title' => 'Hacked']);
    }

    /** @test */
    public function task_member_can_update_task()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $this->actingAs($owner);
        $task->addMember($member);

        $this->actingAs($member);
        $updatedTask = $this->action->update($member, $task->fresh(['board', 'members']), [
            'title' => 'Updated by Member',
        ]);

        $this->assertEquals('Updated by Member', $updatedTask->title);
    }

    /** @test */
    public function co_owner_can_update_any_task()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $this->actingAs($coOwner);
        $updatedTask = $this->action->update($coOwner, $task->fresh(['board']), [
            'title' => 'Updated by Co-Owner',
        ]);

        $this->assertEquals('Updated by Co-Owner', $updatedTask->title);
    }

    /** @test */
    public function update_requires_at_least_one_field()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $task, []);
    }
}
