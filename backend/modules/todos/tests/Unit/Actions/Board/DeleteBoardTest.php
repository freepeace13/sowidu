<?php

namespace Modules\Todos\Tests\Unit\Actions\Board;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\DeleteBoard;
use Tests\TestCase;

class DeleteBoardTest extends TestCase
{
    use RefreshDatabase;

    protected DeleteBoard $action;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteBoard;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function user_can_delete_their_board()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $result = $this->action->destroy($user, $board);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('todo_boards', ['id' => $board->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_board()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Owner Board']);

        $this->expectException(AuthorizationException::class);

        $this->action->destroy($otherUser, $board);
    }

    /** @test */
    public function board_is_removed_from_database_after_deletion()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $boardId = $board->id;

        $this->action->destroy($user, $board);

        $this->assertDatabaseMissing('todo_boards', ['id' => $boardId]);
    }
}
