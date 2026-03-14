<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Subscriber;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Subscriber\RemovesSubscriber;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Tests\TestCase;

class RemovesSubscriberTest extends TestCase
{
    use RefreshDatabase;

    protected RemovesSubscriber $action;

    protected CreatesBoard $createBoardAction;

    protected CreatesBoardTask $createTaskAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RemovesSubscriber;
        $this->createBoardAction = new CreatesBoard;
        $this->createTaskAction = new CreatesBoardTask;
    }

    /** @test */
    public function owner_can_remove_guest_subscriber()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $subscriber = $board->fresh(['subscribers'])->getSubscriber($guest);
        $this->action->remove($owner, $board->fresh(['users']), $subscriber);

        $this->assertFalse($board->fresh(['users'])->hasUser($guest));
    }

    /** @test */
    public function cannot_remove_owner()
    {
        $owner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $subscriber = $board->fresh(['subscribers'])->getSubscriber($owner);

        $this->expectException(ValidationException::class);

        $this->action->remove($owner, $board->fresh(['users']), $subscriber);
    }

    /** @test */
    public function cannot_remove_co_owner()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');

        $subscriber = $board->fresh(['subscribers'])->getSubscriber($coOwner);

        $this->expectException(ValidationException::class);

        $this->action->remove($owner, $board->fresh(['users']), $subscriber);
    }

    /** @test */
    public function non_subscriber_cannot_remove_subscriber()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $subscriber = $board->fresh(['subscribers'])->getSubscriber($guest);

        $this->expectException(AuthorizationException::class);

        $this->action->remove($nonMember, $board->fresh(['users']), $subscriber);
    }

    /** @test */
    public function co_owner_can_remove_guest()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');
        $board->addSubscriber($guest, 'guest');

        $subscriber = $board->fresh(['subscribers'])->getSubscriber($guest);
        $this->action->remove($coOwner, $board->fresh(['users']), $subscriber);

        $this->assertFalse($board->fresh(['users'])->hasUser($guest));
    }

    /** @test */
    public function guest_cannot_remove_subscriber_by_default()
    {
        $owner = User::factory()->create();
        $guest1 = User::factory()->create();
        $guest2 = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest1, 'guest');
        $board->addSubscriber($guest2, 'guest');

        $subscriber = $board->fresh(['subscribers'])->getSubscriber($guest2);

        $this->expectException(AuthorizationException::class);

        $this->action->remove($guest1, $board->fresh(['users']), $subscriber);
    }

    /** @test */
    public function removing_subscriber_detaches_from_tasks()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $this->actingAs($owner);
        $task->addMember($guest);

        $this->assertTrue($task->fresh(['members'])->hasMember($guest));

        $subscriber = $board->fresh(['subscribers'])->getSubscriber($guest);
        $this->action->remove($owner, $board->fresh(['users']), $subscriber);

        $this->assertFalse($task->fresh(['members'])->hasMember($guest));
    }
}
