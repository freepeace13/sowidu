<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Member;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\Member\AddsTaskMember;
use Tests\TestCase;

class AddsTaskMemberTest extends TestCase
{
    use RefreshDatabase;

    protected AddsTaskMember $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new AddsTaskMember;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function board_owner_can_add_member_to_task()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $subscriber = $board->fresh(['subscribers'])->getSubscriber($member);

        $this->actingAs($owner);
        $this->action->add($owner, $task->fresh(['board']), [
            'subscriber_id' => $subscriber->id,
        ]);

        $this->assertTrue($task->fresh(['members'])->hasMember($member));
    }

    /** @test */
    public function subscriber_can_add_member_to_task()
    {
        $owner = User::factory()->create();
        $subscriber1 = User::factory()->create();
        $subscriber2 = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($subscriber1, 'guest');
        $board->addSubscriber($subscriber2, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $subscriber = $board->fresh(['subscribers'])->getSubscriber($subscriber2);

        $this->actingAs($subscriber1);
        $this->action->add($subscriber1, $task->fresh(['board']), [
            'subscriber_id' => $subscriber->id,
        ]);

        $this->assertTrue($task->fresh(['members'])->hasMember($subscriber2));
    }

    /** @test */
    public function cannot_add_non_subscriber_as_member()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->add($owner, $task->fresh(['board']), [
            'subscriber_id' => 999999,
        ]);
    }

    /** @test */
    public function cannot_add_same_member_twice()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $this->actingAs($owner);
        $task->addMember($member);
        $subscriber = $board->fresh(['subscribers'])->getSubscriber($member);

        $this->expectException(ValidationException::class);

        $this->action->add($owner, $task->fresh(['board', 'members']), [
            'subscriber_id' => $subscriber->id,
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_add_member()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $subscriber = $board->fresh(['subscribers'])->getSubscriber($member);

        $this->expectException(AuthorizationException::class);

        $this->action->add($nonMember, $task->fresh(['board']), [
            'subscriber_id' => $subscriber->id,
        ]);
    }

    /** @test */
    public function subscriber_id_is_required()
    {
        $owner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->add($owner, $task->fresh(['board']), []);
    }
}
