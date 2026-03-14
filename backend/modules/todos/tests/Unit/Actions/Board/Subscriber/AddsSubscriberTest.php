<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Subscriber;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Subscriber\AddsSubscriber;
use Tests\TestCase;

class AddsSubscriberTest extends TestCase
{
    use RefreshDatabase;

    protected AddsSubscriber $action;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new AddsSubscriber;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_add_subscriber()
    {
        $owner = User::factory()->create();
        $newMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->action->add($owner, $board, [
            'email' => $newMember->email,
            'role' => 'guest',
        ]);

        $this->assertTrue($board->fresh(['users'])->hasUser($newMember));
    }

    /** @test */
    public function owner_can_add_co_owner()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->action->add($owner, $board, [
            'email' => $coOwner->email,
            'role' => 'co-owner',
        ]);

        $board = $board->fresh(['users']);
        $this->assertTrue($board->hasUser($coOwner));
        $this->assertEquals('co-owner', $board->userRole($coOwner));
    }

    /** @test */
    public function co_owner_can_add_subscriber()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $newMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');

        $this->action->add($coOwner, $board->fresh(['users']), [
            'email' => $newMember->email,
            'role' => 'guest',
        ]);

        $this->assertTrue($board->fresh(['users'])->hasUser($newMember));
    }

    /** @test */
    public function cannot_add_already_subscribed_user()
    {
        $owner = User::factory()->create();
        $existingMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($existingMember, 'guest');

        $this->expectException(ValidationException::class);

        $this->action->add($owner, $board->fresh(['users']), [
            'email' => $existingMember->email,
            'role' => 'guest',
        ]);
    }

    /** @test */
    public function cannot_add_nonexistent_user()
    {
        $owner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->add($owner, $board, [
            'email' => 'nonexistent@example.com',
            'role' => 'guest',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_add_subscriber()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $newMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->expectException(AuthorizationException::class);

        $this->action->add($nonMember, $board, [
            'email' => $newMember->email,
            'role' => 'guest',
        ]);
    }

    /** @test */
    public function guest_cannot_add_subscriber_by_default()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $newMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $this->expectException(AuthorizationException::class);

        $this->action->add($guest, $board->fresh(['users']), [
            'email' => $newMember->email,
            'role' => 'guest',
        ]);
    }

    /** @test */
    public function guest_can_add_subscriber_when_permission_enabled()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $newMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $board->settings()->permissions()->update('members', 'can_manage_subscriber', true);
        $board->addSubscriber($guest, 'guest');

        $this->action->add($guest, $board->fresh(['users']), [
            'email' => $newMember->email,
            'role' => 'guest',
        ]);

        $this->assertTrue($board->fresh(['users'])->hasUser($newMember));
    }

    /** @test */
    public function email_is_required()
    {
        $owner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->add($owner, $board, [
            'email' => '',
            'role' => 'guest',
        ]);
    }
}
