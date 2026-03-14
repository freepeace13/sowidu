<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Label;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Label\CreateBoardLabel;
use Tests\TestCase;

class CreateBoardLabelTest extends TestCase
{
    use RefreshDatabase;

    protected CreateBoardLabel $action;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateBoardLabel;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_create_label()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->action->create($user, $board, [
            'name' => 'Priority',
            'color' => '#FF0000',
        ]);

        $labels = $board->fresh()->settings()->labels()->all();
        $this->assertTrue($labels->contains('name', 'Priority'));
    }

    /** @test */
    public function co_owner_can_create_label()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');

        $this->action->create($coOwner, $board->fresh(['users']), [
            'name' => 'Urgent',
            'color' => '#FF5500',
        ]);

        $labels = $board->fresh()->settings()->labels()->all();
        $this->assertTrue($labels->contains('name', 'Urgent'));
    }

    /** @test */
    public function label_requires_valid_color()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'name' => 'Invalid',
            'color' => 'not-a-color',
        ]);
    }

    /** @test */
    public function label_color_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'name' => 'No Color',
            'color' => '',
        ]);
    }

    /** @test */
    public function label_name_must_be_unique()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->action->create($user, $board, [
            'name' => 'Duplicate',
            'color' => '#FF0000',
        ]);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board->fresh(), [
            'name' => 'Duplicate',
            'color' => '#00FF00',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_create_label()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $this->expectException(AuthorizationException::class);

        $this->action->create($nonMember, $board, [
            'name' => 'Unauthorized',
            'color' => '#FF0000',
        ]);
    }

    /** @test */
    public function guest_cannot_create_label_by_default()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');

        $this->expectException(AuthorizationException::class);

        $this->action->create($guest, $board->fresh(['users']), [
            'name' => 'Guest Label',
            'color' => '#FF0000',
        ]);
    }

    /** @test */
    public function guest_can_create_label_when_permission_enabled()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $board->settings()->permissions()->update('members', 'can_manage_label', true);
        $board->addSubscriber($guest, 'guest');

        $this->action->create($guest, $board->fresh(['users']), [
            'name' => 'Guest Label',
            'color' => '#FF0000',
        ]);

        $labels = $board->fresh()->settings()->labels()->all();
        $this->assertTrue($labels->contains('name', 'Guest Label'));
    }

    /** @test */
    public function label_name_can_be_empty()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->action->create($user, $board, [
            'name' => '',
            'color' => '#123456',
        ]);

        $labels = $board->fresh()->settings()->labels()->all();
        $this->assertTrue($labels->contains('color', '#123456'));
    }
}
