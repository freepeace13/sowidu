<?php

namespace Modules\Todos\Tests\Unit\Actions\Board;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\UpdateBoard;
use Tests\TestCase;

class UpdateBoardTest extends TestCase
{
    use RefreshDatabase;

    protected UpdateBoard $action;

    protected CreatesBoard $createAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateBoard;
        $this->createAction = new CreatesBoard;
        Storage::fake('public');
    }

    /** @test */
    public function owner_can_update_board_title()
    {
        $user = User::factory()->create();
        $board = $this->createAction->create($user, ['title' => 'Original Title']);

        $updatedBoard = $this->action->update($user, $board, ['title' => 'Updated Title']);

        $this->assertEquals('Updated Title', $updatedBoard->title);
    }

    /** @test */
    public function owner_can_update_board_description()
    {
        $user = User::factory()->create();
        $board = $this->createAction->create($user, ['title' => 'Test Board']);

        $updatedBoard = $this->action->update($user, $board, [
            'title' => 'Test Board',
            'description' => 'New description',
        ]);

        $this->assertEquals('New description', $updatedBoard->description);
    }

    /** @test */
    public function non_owner_cannot_update_board()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $board = $this->createAction->create($owner, ['title' => 'Owner Board']);

        $this->expectException(AuthorizationException::class);

        $this->action->update($otherUser, $board, ['title' => 'Hacked Title']);
    }

    /** @test */
    public function co_owner_can_update_board()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createAction->create($owner, ['title' => 'Test Board']);

        $board->addSubscriber($coOwner, 'co-owner');

        $updatedBoard = $this->action->update($coOwner, $board->fresh(['users']), [
            'title' => 'Updated by Co-Owner',
        ]);

        $this->assertEquals('Updated by Co-Owner', $updatedBoard->title);
    }

    /** @test */
    public function guest_cannot_update_board()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createAction->create($owner, ['title' => 'Test Board']);

        $board->addSubscriber($guest, 'guest');

        $this->expectException(AuthorizationException::class);

        $this->action->update($guest, $board->fresh(['users']), ['title' => 'Guest Update']);
    }

    /** @test */
    public function update_requires_title()
    {
        $user = User::factory()->create();
        $board = $this->createAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board, ['title' => '']);
    }

    /** @test */
    public function predefined_board_title_cannot_be_changed()
    {
        $user = User::factory()->create();
        $board = $this->createAction->create($user, ['title' => 'Documents']);

        $board->update(['settings' => array_merge($board->settings ?? [], ['is_predefined' => true])]);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board->fresh(), ['title' => 'New Name']);
    }
}
