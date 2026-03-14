<?php

namespace Modules\Chatly\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Modules\Chatly\Contracts\External\UserSearchContract;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_searches_for_users_and_groups()
    {
        // Mock the UserSearchContract
        $mockSearch = Mockery::mock(UserSearchContract::class);
        $this->app->instance(UserSearchContract::class, $mockSearch);

        $mockSearch->shouldReceive('search')
            ->once()
            ->with('john', [], 8)
            ->andReturn([
                'people' => [
                    [
                        'id' => 1,
                        'name' => 'John Doe',
                        'photo' => 'https://example.com/avatar.jpg',
                        'type' => 'App\\Models\\User',
                        'is_user' => true,
                    ],
                ],
                'groups' => [],
            ]);

        // Make authenticated request
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('chatly.search', ['keyword' => 'john']));

        $response->assertStatus(200)
            ->assertJson([
                'people' => [
                    ['name' => 'John Doe'],
                ],
                'groups' => [],
            ]);
    }

    /** @test */
    public function it_passes_exception_filters()
    {
        $mockSearch = Mockery::mock(UserSearchContract::class);
        $this->app->instance(UserSearchContract::class, $mockSearch);

        $exceptFilters = [
            ['id' => 2, 'type' => 'App\\Models\\User'],
        ];

        $mockSearch->shouldReceive('search')
            ->once()
            ->with('jane', ['except' => $exceptFilters], 8)
            ->andReturn([
                'people' => [],
                'groups' => [],
            ]);

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('chatly.search', [
            'keyword' => 'jane',
            'except' => $exceptFilters,
        ]));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_people_and_groups()
    {
        $mockSearch = Mockery::mock(UserSearchContract::class);
        $this->app->instance(UserSearchContract::class, $mockSearch);

        $mockSearch->shouldReceive('search')
            ->once()
            ->with('test', [], 8)
            ->andReturn([
                'people' => [
                    [
                        'id' => 1,
                        'name' => 'Test User',
                        'photo' => 'https://example.com/1.jpg',
                        'type' => 'App\\Models\\User',
                        'is_user' => true,
                    ],
                ],
                'groups' => [
                    'Company A' => [
                        [
                            'id' => 10,
                            'name' => 'Test Employee',
                            'photo' => 'https://example.com/company.jpg',
                            'type' => 'App\\Models\\Employee',
                            'role' => 'Developer',
                            'is_user' => false,
                        ],
                    ],
                ],
            ]);

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('chatly.search', ['keyword' => 'test']));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'people' => [
                    '*' => ['id', 'name', 'photo', 'type', 'is_user'],
                ],
                'groups' => [
                    'Company A' => [
                        '*' => ['id', 'name', 'photo', 'type', 'role', 'is_user'],
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson(route('chatly.search', ['keyword' => 'test']));

        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
