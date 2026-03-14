<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Group;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Group\CreatesBoardGroup;
use Modules\Todos\Actions\Board\Group\UpdateBoardGroup;
use Tests\TestCase;

class UpdateBoardGroupTest extends TestCase
{
    use RefreshDatabase;

    protected UpdateBoardGroup $action;

    protected CreatesBoardGroup $createGroupAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateBoardGroup;
        $this->createGroupAction = new CreatesBoardGroup;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_update_group_name()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createGroupAction->create($user, $board, ['name' => 'Custom Group']);

        $updatedGroup = $this->action->update($user, $board->fresh(), 'Custom Group', [
            'name' => 'Renamed Group',
        ]);

        $this->assertEquals('Renamed Group', $updatedGroup['name']);
    }

    /** @test */
    public function owner_can_update_group_color()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createGroupAction->create($user, $board, ['name' => 'Custom Group']);

        $updatedGroup = $this->action->update($user, $board->fresh(), 'Custom Group', [
            'name' => 'Custom Group',
            'color' => '#00FF00',
        ]);

        $this->assertEquals('#00FF00', $updatedGroup['color']);
    }

    /** @test */
    public function cannot_update_nonexistent_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board, 'NonExistent', [
            'name' => 'New Name',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_update_group()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->expectException(AuthorizationException::class);

        $this->action->update($nonMember, $board, 'Backlog', [
            'name' => 'Hacked',
        ]);
    }

    /** @test */
    public function co_owner_can_update_group()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');

        $updatedGroup = $this->action->update($coOwner, $board->fresh(['users']), 'Backlog', [
            'name' => 'Updated Backlog',
        ]);

        $this->assertEquals('Updated Backlog', $updatedGroup['name']);
    }

    /** @test */
    public function guest_cannot_update_group_by_default()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $this->expectException(AuthorizationException::class);

        $this->action->update($guest, $board->fresh(['users']), 'Backlog', [
            'name' => 'Guest Update',
        ]);
    }

    /** @test */
    public function update_requires_name()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board, 'Backlog', []);
    }
}
