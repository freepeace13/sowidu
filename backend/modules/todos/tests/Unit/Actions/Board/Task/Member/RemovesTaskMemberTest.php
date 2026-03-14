<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Member;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\Member\RemovesTaskMember;
use Tests\TestCase;

class RemovesTaskMemberTest extends TestCase
{
    use RefreshDatabase;

    protected RemovesTaskMember $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RemovesTaskMember;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function board_owner_can_remove_member_from_task()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $this->actingAs($owner);
        $task->addMember($member);
        $subscriber = $board->fresh(['subscribers'])->getSubscriber($member);

        $this->action->remove($owner, $task->fresh(['board', 'members']), $subscriber);

        $this->assertFalse($task->fresh(['members'])->hasMember($member));
    }

    /** @test */
    public function subscriber_can_remove_member_from_task()
    {
        $owner = User::factory()->create();
        $subscriber1 = User::factory()->create();
        $subscriber2 = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($subscriber1, 'guest');
        $board->addSubscriber($subscriber2, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $this->actingAs($owner);
        $task->addMember($subscriber2);
        $subscriber = $board->fresh(['subscribers'])->getSubscriber($subscriber2);

        $this->actingAs($subscriber1);
        $this->action->remove($subscriber1, $task->fresh(['board', 'members']), $subscriber);

        $this->assertFalse($task->fresh(['members'])->hasMember($subscriber2));
    }

    /** @test */
    public function non_subscriber_cannot_remove_member()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $this->actingAs($owner);
        $task->addMember($member);
        $subscriber = $board->fresh(['subscribers'])->getSubscriber($member);

        $this->expectException(AuthorizationException::class);

        $this->actingAs($nonMember);
        $this->action->remove($nonMember, $task->fresh(['board', 'members']), $subscriber);
    }

    /** @test */
    public function cannot_remove_non_member()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $subscriber = $board->fresh(['subscribers'])->getSubscriber($member);

        $this->expectException(ValidationException::class);

        $this->action->remove($owner, $task->fresh(['board', 'members']), $subscriber);
    }
}
