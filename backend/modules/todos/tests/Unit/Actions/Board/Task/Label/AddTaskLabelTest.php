<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Label;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Label\CreateBoardLabel;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\Label\AddTaskLabel;
use Tests\TestCase;

class AddTaskLabelTest extends TestCase
{
    use RefreshDatabase;

    protected AddTaskLabel $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected CreateBoardLabel $createLabelAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new AddTaskLabel;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
        $this->createLabelAction = new CreateBoardLabel;
    }

    /** @test */
    public function board_owner_can_add_label_to_task()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $label = $this->createLabelAction->create($user, $board, [
            'name' => 'Bug',
            'color' => '#FF0000',
        ]);

        $this->action->add($user, $task->fresh(['board']), [
            'label' => $label->id,
        ]);

        $this->assertTrue($task->fresh()->labels->contains('label_id', $label->id));
    }

    /** @test */
    public function subscriber_can_add_label_to_task()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($subscriber, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $label = $this->createLabelAction->create($owner, $board->fresh(['users', 'subscribers']), [
            'name' => 'Bug',
            'color' => '#FF0000',
        ]);

        $this->action->add($subscriber, $task->fresh(['board']), [
            'label' => $label->id,
        ]);

        $this->assertTrue($task->fresh()->labels->contains('label_id', $label->id));
    }

    /** @test */
    public function non_subscriber_cannot_add_label()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $label = $this->createLabelAction->create($owner, $board, [
            'name' => 'Bug',
            'color' => '#FF0000',
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->add($nonMember, $task, [
            'label' => $label->id,
        ]);
    }

    /** @test */
    public function label_must_belong_to_board()
    {
        $user = User::factory()->create();
        $board1 = $this->createBoardAction->create($user, ['title' => 'Board 1']);
        $board2 = $this->createBoardAction->create($user, ['title' => 'Board 2']);

        $task = $this->createTaskAction->create($user, $board1, ['title' => 'Task']);

        $label = $this->createLabelAction->create($user, $board2, [
            'name' => 'Bug',
            'color' => '#FF0000',
        ]);

        $this->expectException(ValidationException::class);

        $this->action->add($user, $task->fresh(['board']), [
            'label' => $label->id,
        ]);
    }

    /** @test */
    public function cannot_add_duplicate_label()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $label = $this->createLabelAction->create($user, $board, [
            'name' => 'Bug',
            'color' => '#FF0000',
        ]);

        $this->action->add($user, $task->fresh(['board']), [
            'label' => $label->id,
        ]);

        $this->expectException(ValidationException::class);

        $this->action->add($user, $task->fresh(['board', 'labels']), [
            'label' => $label->id,
        ]);
    }

    /** @test */
    public function label_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->add($user, $task->fresh(['board']), []);
    }
}
