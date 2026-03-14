<?php

namespace Modules\Todos\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Subscriber;
use Tests\TestCase;

class SearchUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_searches_users_by_keyword()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $searchUser = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/todos/search/users?keyword=John');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results',
            ]);
    }

    /** @test */
    public function it_excludes_current_user_when_not_impersonating()
    {
        $currentUser = User::factory()->create([
            'first_name' => 'Current',
            'last_name' => 'User',
        ]);
        $otherUser = User::factory()->create([
            'first_name' => 'Other',
            'last_name' => 'User',
        ]);

        $this->actingAs($currentUser);

        $response = $this->getJson('/todos/search/users?keyword=');

        $response->assertStatus(200);

        $results = $response->json('results');
        $resultIds = collect($results)->pluck('id')->toArray();

        $this->assertNotContains($currentUser->id, $resultIds);
        $this->assertContains($otherUser->id, $resultIds);
    }

    /** @test */
    public function it_filters_users_by_board_subscription()
    {
        $user = User::factory()->create();
        $boardUser = User::factory()->create();
        $nonBoardUser = User::factory()->create();

        $board = Board::create([
            'title' => 'Test Board',
            'user_id' => $user->id,
        ]);

        Subscriber::create([
            'board_id' => $board->id,
            'user_id' => $boardUser->id,
        ]);

        $this->actingAs($user);

        $response = $this->getJson("/todos/search/users?keyword=&board={$board->id}");

        $response->assertStatus(200);

        $results = $response->json('results');
        $resultIds = collect($results)->pluck('id')->toArray();

        $this->assertContains($boardUser->id, $resultIds);
        $this->assertNotContains($nonBoardUser->id, $resultIds);
    }

    /** @test */
    public function it_returns_empty_results_when_no_matches_found()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/todos/search/users?keyword=NonExistentUser');

        $response->assertStatus(200)
            ->assertJson([
                'results' => [],
            ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/todos/search/users?keyword=test');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_combines_board_filter_with_user_exclusion()
    {
        $currentUser = User::factory()->create();
        $boardUser = User::factory()->create();

        $board = Board::create([
            'title' => 'Test Board',
            'user_id' => $currentUser->id,
        ]);

        Subscriber::create([
            'board_id' => $board->id,
            'user_id' => $currentUser->id,
        ]);

        Subscriber::create([
            'board_id' => $board->id,
            'user_id' => $boardUser->id,
        ]);

        $this->actingAs($currentUser);

        $response = $this->getJson("/todos/search/users?keyword=&board={$board->id}");
        $response->assertStatus(200);

        $results = $response->json('results');
        $resultIds = collect($results)->pluck('id')->toArray();

        $this->assertNotContains($currentUser->id, $resultIds);
        $this->assertContains($boardUser->id, $resultIds);
    }
}
