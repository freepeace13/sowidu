<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Group;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Group\CreatesBoardGroup;
use Modules\Todos\Actions\Board\Group\DeletesBoardGroup;
use Tests\TestCase;

class DeletesBoardGroupTest extends TestCase
{
    use RefreshDatabase;

    protected DeletesBoardGroup $action;

    protected CreatesBoardGroup $createGroupAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeletesBoardGroup;
        $this->createGroupAction = new CreatesBoardGroup;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_delete_custom_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createGroupAction->create($user, $board, ['name' => 'Custom Group']);

        $this->action->delete($user, $board->fresh(), 'Custom Group');

        $this->assertFalse($board->fresh()->settings()->groups()->has('Custom Group'));
    }

    /** @test */
    public function cannot_delete_default_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->delete($user, $board, 'Backlog');
    }

    /** @test */
    public function cannot_delete_nonexistent_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->delete($user, $board, 'NonExistent');
    }

    /** @test */
    public function non_subscriber_cannot_delete_group()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $this->createGroupAction->create($owner, $board, ['name' => 'Custom']);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($nonMember, $board->fresh(), 'Custom');
    }

    /** @test */
    public function co_owner_can_delete_group()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');
        $this->createGroupAction->create($owner, $board->fresh(['users']), ['name' => 'Custom Group']);

        $this->action->delete($coOwner, $board->fresh(['users']), 'Custom Group');

        $this->assertFalse($board->fresh()->settings()->groups()->has('Custom Group'));
    }

    /** @test */
    public function guest_cannot_delete_group_by_default()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $this->createGroupAction->create($owner, $board, ['name' => 'Custom']);
        $board->addSubscriber($guest, 'guest');

        $this->expectException(AuthorizationException::class);

        $this->action->delete($guest, $board->fresh(['users']), 'Custom');
    }

    /** @test */
    public function delete_requires_group_name()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->delete($user, $board, '');
    }
}
