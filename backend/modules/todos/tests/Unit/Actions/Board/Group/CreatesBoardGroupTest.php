<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Group;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Group\CreatesBoardGroup;
use Tests\TestCase;

class CreatesBoardGroupTest extends TestCase
{
    use RefreshDatabase;

    protected CreatesBoardGroup $action;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreatesBoardGroup;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_create_group()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $group = $this->action->create($user, $board, [
            'name' => 'New Group',
            'color' => '#FF0000',
        ]);

        $this->assertEquals('New Group', $group['name']);
        $this->assertEquals('#FF0000', $group['color']);
    }

    /** @test */
    public function co_owner_can_create_group()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');

        $group = $this->action->create($coOwner, $board->fresh(['users']), [
            'name' => 'Co-Owner Group',
        ]);

        $this->assertEquals('Co-Owner Group', $group['name']);
    }

    /** @test */
    public function group_requires_name()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'name' => '',
        ]);
    }

    /** @test */
    public function group_name_must_be_unique()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'name' => 'Backlog',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_create_group()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->expectException(AuthorizationException::class);

        $this->action->create($nonMember, $board, [
            'name' => 'Unauthorized Group',
        ]);
    }

    /** @test */
    public function guest_cannot_create_group_by_default()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $this->expectException(AuthorizationException::class);

        $this->action->create($guest, $board->fresh(['users']), [
            'name' => 'Guest Group',
        ]);
    }

    /** @test */
    public function guest_can_create_group_when_permission_enabled()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $board->settings()->permissions()->update('members', 'can_manage_group', true);
        $board->addSubscriber($guest, 'guest');

        $group = $this->action->create($guest, $board->fresh(['users']), [
            'name' => 'Guest Group',
        ]);

        $this->assertEquals('Guest Group', $group['name']);
    }

    /** @test */
    public function group_color_is_optional()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $group = $this->action->create($user, $board, [
            'name' => 'No Color Group',
        ]);

        $this->assertEquals('No Color Group', $group['name']);
        $this->assertNull($group['color']);
    }
}
