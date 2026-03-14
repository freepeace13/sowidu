<?php

namespace Modules\Todos\Tests\Unit\Actions\Board;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\GetBoard;
use Tests\TestCase;

class GetBoardTest extends TestCase
{
    use RefreshDatabase;

    protected GetBoard $action;

    protected CreatesBoard $createAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetBoard;
        $this->createAction = new CreatesBoard;
    }

    /** @test */
    public function it_returns_boards_for_user()
    {
        $user = User::factory()->create();
        $this->createAction->create($user, ['title' => 'Board 1']);
        $this->createAction->create($user, ['title' => 'Board 2']);

        $boards = $this->action->get($user, []);

        $this->assertCount(2, $boards);
    }

    /** @test */
    public function it_only_returns_boards_where_user_is_subscriber()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->createAction->create($user, ['title' => 'My Board']);
        $this->createAction->create($otherUser, ['title' => 'Other Board']);

        $boards = $this->action->get($user, []);

        $this->assertCount(1, $boards);
        $this->assertEquals('My Board', $boards->first()->title);
    }

    /** @test */
    public function it_returns_boards_where_user_is_guest()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();

        $board = $this->createAction->create($owner, ['title' => 'Shared Board']);
        $board->addSubscriber($guest, 'guest');

        $boards = $this->action->get($guest, []);

        $this->assertCount(1, $boards);
        $this->assertEquals('Shared Board', $boards->first()->title);
    }

    /** @test */
    public function it_filters_boards_by_keyword()
    {
        $user = User::factory()->create();
        $this->createAction->create($user, ['title' => 'Project Alpha']);
        $this->createAction->create($user, ['title' => 'Project Beta']);
        $this->createAction->create($user, ['title' => 'Other Work']);

        $boards = $this->action->get($user, ['q' => 'Project']);

        $this->assertCount(2, $boards);
    }

    /** @test */
    public function it_returns_empty_collection_for_user_without_boards()
    {
        $user = User::factory()->create();

        $boards = $this->action->get($user, []);

        $this->assertCount(0, $boards);
    }
}
