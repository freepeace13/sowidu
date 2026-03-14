<?php

namespace Modules\Todos\Tests\Unit\Actions\Board;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Models\Board;
use Tests\TestCase;

class CreatesBoardTest extends TestCase
{
    use RefreshDatabase;

    protected CreatesBoard $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreatesBoard;
        Storage::fake('public');
    }

    /** @test */
    public function it_creates_a_board_with_title()
    {
        $user = User::factory()->create();
        $params = [
            'title' => 'My Project Board',
        ];

        $board = $this->action->create($user, $params);

        $this->assertInstanceOf(Board::class, $board);
        $this->assertEquals('My Project Board', $board->title);
        $this->assertTrue($board->users->contains($user));
    }

    /** @test */
    public function it_attaches_user_as_owner()
    {
        $user = User::factory()->create();
        $params = [
            'title' => 'Test Board',
        ];

        $board = $this->action->create($user, $params);

        $pivot = $board->users()->where('user_id', $user->id)->first()->pivot;
        $this->assertEquals('owner', $pivot->role);
    }
}
