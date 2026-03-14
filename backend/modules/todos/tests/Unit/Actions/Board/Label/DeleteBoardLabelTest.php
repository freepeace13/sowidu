<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Label;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Label\CreateBoardLabel;
use Modules\Todos\Actions\Board\Label\DeleteBoardLabel;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Tests\TestCase;

class DeleteBoardLabelTest extends TestCase
{
    use RefreshDatabase;

    protected DeleteBoardLabel $action;

    protected CreateBoardLabel $createLabelAction;

    protected CreatesBoard $createBoardAction;

    protected CreatesBoardTask $createTaskAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteBoardLabel;
        $this->createLabelAction = new CreateBoardLabel;
        $this->createBoardAction = new CreatesBoard;
        $this->createTaskAction = new CreatesBoardTask;
    }

    /** @test */
    public function owner_can_delete_custom_label()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createLabelAction->create($user, $board, ['name' => 'Custom', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->action->delete($user, $board->fresh(), $labelId);

        $updatedLabels = $board->fresh()->settings()->labels()->all();
        $this->assertFalse($updatedLabels->contains('name', 'Custom'));
    }

    /** @test */
    public function co_owner_can_delete_label()
    {
        $owner = User::factory()->create();
        $coOwner = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($coOwner, 'co-owner');
        $this->createLabelAction->create($owner, $board->fresh(['users']), ['name' => 'Custom', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->action->delete($coOwner, $board->fresh(['users', 'tasks']), $labelId);

        $updatedLabels = $board->fresh()->settings()->labels()->all();
        $this->assertFalse($updatedLabels->contains('name', 'Custom'));
    }

    /** @test */
    public function cannot_delete_nonexistent_label()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->delete($user, $board, 999999);
    }

    /** @test */
    public function cannot_delete_default_label()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $labels = $board->settings()->labels()->all();
        $defaultLabelId = $labels->first()['id'];

        $this->expectException(ValidationException::class);

        $this->action->delete($user, $board, $defaultLabelId);
    }

    /** @test */
    public function non_subscriber_cannot_delete_label()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $this->createLabelAction->create($owner, $board, ['name' => 'Custom', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->expectException(AuthorizationException::class);

        $this->action->delete($nonMember, $board->fresh(), $labelId);
    }

    /** @test */
    public function guest_cannot_delete_label_by_default()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($guest, 'guest');
        $this->createLabelAction->create($owner, $board->fresh(['users']), ['name' => 'Custom', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->expectException(AuthorizationException::class);

        $this->action->delete($guest, $board->fresh(['users']), $labelId);
    }

    /** @test */
    public function guest_can_delete_label_when_permission_enabled()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $board->settings()->permissions()->update('members', 'can_manage_label', true);
        $board->addSubscriber($guest, 'guest');
        $this->createLabelAction->create($owner, $board->fresh(['users']), ['name' => 'Custom', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $this->action->delete($guest, $board->fresh(['users', 'tasks']), $labelId);

        $updatedLabels = $board->fresh()->settings()->labels()->all();
        $this->assertFalse($updatedLabels->contains('name', 'Custom'));
    }

    /** @test */
    public function deleting_label_removes_it_from_tasks()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $this->createLabelAction->create($user, $board, ['name' => 'ToRemove', 'color' => '#FF0000']);

        $labels = $board->fresh()->settings()->labels()->all();
        $labelId = $labels->last()['id'];

        $task = $this->createTaskAction->create($user, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $task->addLabel($labelId);

        $this->assertTrue($task->fresh(['labels'])->labels->contains('label_id', $labelId));

        $this->action->delete($user, $board->fresh(['tasks']), $labelId);

        $this->assertFalse($task->fresh(['labels'])->labels->contains('label_id', $labelId));
    }
}
