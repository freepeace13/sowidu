<?php

namespace Modules\Chatly\Tests\Integration;

use App\Models\User;
use App\Services\Chat\UserSearchAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSearchAdapterTest extends TestCase
{
    use RefreshDatabase;

    protected UserSearchAdapter $adapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = new UserSearchAdapter;
    }

    /** @test */
    public function it_searches_for_users()
    {
        // Create test users
        $user1 = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $user2 = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
        ]);

        // Act as a different user
        $currentUser = User::factory()->create();
        $this->actingAs($currentUser);

        $result = $this->adapter->search('John', [], 8);

        $this->assertArrayHasKey('people', $result);
        $this->assertArrayHasKey('groups', $result);
        $this->assertNotEmpty($result['people']);
    }

    /** @test */
    public function it_excludes_current_user_from_search()
    {
        $currentUser = User::factory()->create([
            'first_name' => 'Current',
            'last_name' => 'User',
        ]);

        $this->actingAs($currentUser);

        $result = $this->adapter->search('Current', [], 8);

        $this->assertArrayHasKey('people', $result);

        // Current user should not be in results
        $userIds = collect($result['people'])->pluck('id')->toArray();
        $this->assertNotContains($currentUser->id, $userIds);
    }

    /** @test */
    public function it_excludes_specified_users()
    {
        $user1 = User::factory()->create(['first_name' => 'Test']);
        $user2 = User::factory()->create(['first_name' => 'Test']);
        $currentUser = User::factory()->create();

        $this->actingAs($currentUser);

        $result = $this->adapter->search('Test', [
            'except' => [
                ['id' => $user1->id, 'type' => User::class],
            ],
        ], 8);

        $userIds = collect($result['people'])->pluck('id')->toArray();

        $this->assertNotContains($user1->id, $userIds);
        // user2 might be in results if search finds it
    }

    /** @test */
    public function it_finds_current_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $currentUser = $this->adapter->currentUser();

        $this->assertInstanceOf(User::class, $currentUser);
        $this->assertEquals($user->id, $currentUser->id);
    }

    /** @test */
    public function it_returns_null_for_current_team_when_not_set()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $currentTeam = $this->adapter->currentTeam();

        $this->assertNull($currentTeam);
    }

    /** @test */
    public function it_resolves_urn_to_user()
    {
        $user = User::factory()->create();
        $urn = "urn:person:{$user->id}";

        $resolved = $this->adapter->findByUrn($urn);

        $this->assertInstanceOf(User::class, $resolved);
        $this->assertEquals($user->id, $resolved->id);
    }
}
