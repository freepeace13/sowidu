<?php

namespace Modules\Todos\Tests\Unit\Actions\Board;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\UpdateBoardPermissions;
use Tests\TestCase;

class UpdateBoardPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected UpdateBoardPermissions $action;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateBoardPermissions;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function board_owner_can_update_permissions()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $result = $this->action->update($user, $board, [
            'role' => 'members',
            'permission' => 'can_comment',
            'value' => false,
        ]);

        $this->assertFalse($board->fresh()->settings()->permissions()->allow('members', 'can_comment'));
    }

    /** @test */
    public function co_owner_can_update_permissions()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');

        $result = $this->action->update($coOwner, $board->fresh(['users', 'subscribers']), [
            'role' => 'members',
            'permission' => 'can_comment',
            'value' => false,
        ]);

        $this->assertFalse($board->fresh()->settings()->permissions()->allow('members', 'can_comment'));
    }

    /** @test */
    public function guest_cannot_update_permissions()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $this->expectException(AuthorizationException::class);

        $this->action->update($guest, $board->fresh(['users', 'subscribers']), [
            'role' => 'members',
            'permission' => 'can_comment',
            'value' => false,
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_update_permissions()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->expectException(AuthorizationException::class);

        $this->action->update($nonMember, $board, [
            'role' => 'members',
            'permission' => 'can_comment',
            'value' => false,
        ]);
    }

    /** @test */
    public function role_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board, [
            'permission' => 'can_comment',
            'value' => false,
        ]);
    }

    /** @test */
    public function permission_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board, [
            'role' => 'members',
            'value' => false,
        ]);
    }

    /** @test */
    public function value_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board, [
            'role' => 'members',
            'permission' => 'can_comment',
        ]);
    }

    /** @test */
    public function value_must_be_boolean()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board, [
            'role' => 'members',
            'permission' => 'can_comment',
            'value' => 'not-a-boolean',
        ]);
    }
}
