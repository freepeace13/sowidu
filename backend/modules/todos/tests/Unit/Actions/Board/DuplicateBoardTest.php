<?php

namespace Modules\Todos\Tests\Unit\Actions\Board;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\DuplicateBoard;
use Modules\Todos\Models\Board;
use Tests\TestCase;

class DuplicateBoardTest extends TestCase
{
    use RefreshDatabase;

    protected DuplicateBoard $action;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DuplicateBoard;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function user_can_duplicate_board()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, [
            'title' => 'Original Board',
            'description' => 'Board description',
        ]);

        $duplicatedBoard = $this->action->duplicate($user, $board);

        $this->assertInstanceOf(Board::class, $duplicatedBoard);
        $this->assertEquals('Original Board', $duplicatedBoard->title);
        $this->assertEquals('Board description', $duplicatedBoard->description);
        $this->assertNotEquals($board->id, $duplicatedBoard->id);
    }

    /** @test */
    public function duplicated_board_has_new_user_as_owner()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Original Board']);

        $duplicatedBoard = $this->action->duplicate($otherUser, $board);

        $this->assertTrue($duplicatedBoard->users->contains($otherUser));
        $this->assertEquals('owner', $duplicatedBoard->userRole($otherUser));
    }

    /** @test */
    public function duplicated_board_preserves_title()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, [
            'title' => 'My Project Board',
        ]);

        $duplicatedBoard = $this->action->duplicate($user, $board);

        $this->assertEquals('My Project Board', $duplicatedBoard->title);
    }

    /** @test */
    public function duplicated_board_preserves_description()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, [
            'title' => 'Test Board',
            'description' => 'Detailed description of the board',
        ]);

        $duplicatedBoard = $this->action->duplicate($user, $board);

        $this->assertEquals('Detailed description of the board', $duplicatedBoard->description);
    }

    /** @test */
    public function duplicated_board_is_separate_from_original()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Original Board']);

        $duplicatedBoard = $this->action->duplicate($user, $board);

        $this->assertNotEquals($board->id, $duplicatedBoard->id);
        $this->assertEquals(2, Board::count());
    }

    /** @test */
    public function duplicated_board_has_default_settings()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $board->settings()->permissions()->update('members', 'can_comment', false);

        $duplicatedBoard = $this->action->duplicate($user, $board->fresh());

        $this->assertTrue($duplicatedBoard->settings()->permissions()->allow('members', 'can_comment'));
    }

    /** @test */
    public function any_user_can_duplicate_any_board()
    {
        $owner = User::factory()->create();
        $anyUser = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Original Board']);

        $duplicatedBoard = $this->action->duplicate($anyUser, $board);

        $this->assertInstanceOf(Board::class, $duplicatedBoard);
        $this->assertTrue($duplicatedBoard->users->contains($anyUser));
    }
}
