<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Label;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Label\CreateBoardLabel;
use Modules\Todos\Actions\Board\Label\UpdateBoardLabel;
use Tests\TestCase;

class UpdateBoardLabelTest extends TestCase
{
    use RefreshDatabase;

    protected UpdateBoardLabel $action;

    protected CreateBoardLabel $createLabelAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateBoardLabel;
        $this->createLabelAction = new CreateBoardLabel;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function owner_can_update_label_name()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createLabelAction->create($user, $board, ['name' => 'Original', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->action->update($user, $board->fresh(), $labelId, [
            'name' => 'Updated Name',
            'color' => '#FF0000',
        ]);

        $updatedLabels = $board->fresh()->settings()->labels()->all();
        $this->assertTrue($updatedLabels->contains('name', 'Updated Name'));
    }

    /** @test */
    public function owner_can_update_label_color()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createLabelAction->create($user, $board, ['name' => 'Test', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->action->update($user, $board->fresh(), $labelId, [
            'name' => 'Test',
            'color' => '#00FF00',
        ]);

        $updatedLabels = $board->fresh()->settings()->labels()->all();
        $this->assertTrue($updatedLabels->contains('color', '#00FF00'));
    }

    /** @test */
    public function co_owner_can_update_label()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');
        $this->createLabelAction->create($owner, $board->fresh(['users']), ['name' => 'Test', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->action->update($coOwner, $board->fresh(['users']), $labelId, [
            'name' => 'Co-Owner Update',
            'color' => '#0000FF',
        ]);

        $updatedLabels = $board->fresh()->settings()->labels()->all();
        $this->assertTrue($updatedLabels->contains('name', 'Co-Owner Update'));
    }

    /** @test */
    public function cannot_update_nonexistent_label()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board, 999999, [
            'name' => 'Test',
            'color' => '#FF0000',
        ]);
    }

    /** @test */
    public function update_requires_valid_color()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createLabelAction->create($user, $board, ['name' => 'Test', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board->fresh(), $labelId, [
            'name' => 'Test',
            'color' => 'invalid-color',
        ]);
    }

    /** @test */
    public function update_requires_color()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createLabelAction->create($user, $board, ['name' => 'Test', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->expectException(ValidationException::class);

        $this->action->update($user, $board->fresh(), $labelId, [
            'name' => 'Test',
            'color' => '',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_update_label()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $this->createLabelAction->create($owner, $board, ['name' => 'Test', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->expectException(AuthorizationException::class);

        $this->action->update($nonMember, $board->fresh(), $labelId, [
            'name' => 'Hacked',
            'color' => '#FF0000',
        ]);
    }

    /** @test */
    public function guest_cannot_update_label_by_default()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');
        $this->createLabelAction->create($owner, $board->fresh(['users']), ['name' => 'Test', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->expectException(AuthorizationException::class);

        $this->action->update($guest, $board->fresh(['users']), $labelId, [
            'name' => 'Guest Update',
            'color' => '#FF0000',
        ]);
    }

    /** @test */
    public function guest_can_update_label_when_permission_enabled()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $board->settings()->permissions()->update('members', 'can_manage_label', true);
        $board->addSubscriber($guest, 'guest');
        $this->createLabelAction->create($owner, $board->fresh(['users']), ['name' => 'Test', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->action->update($guest, $board->fresh(['users']), $labelId, [
            'name' => 'Guest Update',
            'color' => '#00FF00',
        ]);

        $updatedLabels = $board->fresh()->settings()->labels()->all();
        $this->assertTrue($updatedLabels->contains('name', 'Guest Update'));
    }
}
