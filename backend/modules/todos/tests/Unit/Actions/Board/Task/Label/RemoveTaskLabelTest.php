<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Label;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Label\CreateBoardLabel;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Actions\Board\Task\Label\AddTaskLabel;
use Modules\Todos\Actions\Board\Task\Label\RemoveTaskLabel;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RemoveTaskLabelTest extends TestCase
{
    use RefreshDatabase;

    protected RemoveTaskLabel $action;

    protected AddTaskLabel $addLabelAction;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected CreateBoardLabel $createLabelAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RemoveTaskLabel;
        $this->addLabelAction = new AddTaskLabel;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
        $this->createLabelAction = new CreateBoardLabel;
    }

    /** @test */
    public function board_owner_can_remove_label_from_task()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $label = $this->createLabelAction->create($user, $board, [
            'name' => 'Bug',
            'color' => '#FF0000',
        ]);

        $this->addLabelAction->add($user, $task->fresh(['board']), [
            'label' => $label->id,
        ]);

        $this->assertTrue($task->fresh()->labels->contains('label_id', $label->id));

        $this->action->remove($user, $task->fresh(['board', 'labels']), $label->id);

        $this->assertFalse($task->fresh()->labels->contains('label_id', $label->id));
    }

    /** @test */
    public function subscriber_can_remove_label_from_task()
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

        $this->addLabelAction->add($owner, $task->fresh(['board']), [
            'label' => $label->id,
        ]);

        $this->action->remove($subscriber, $task->fresh(['board', 'labels']), $label->id);

        $this->assertFalse($task->fresh()->labels->contains('label_id', $label->id));
    }

    /** @test */
    public function non_subscriber_cannot_remove_label()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $label = $this->createLabelAction->create($owner, $board, [
            'name' => 'Bug',
            'color' => '#FF0000',
        ]);

        $this->addLabelAction->add($owner, $task->fresh(['board']), [
            'label' => $label->id,
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->remove($nonMember, $task->fresh(['board', 'labels']), $label->id);
    }

    /** @test */
    public function cannot_remove_nonexistent_label()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(HttpException::class);

        $this->action->remove($user, $task->fresh(['board', 'labels']), 99999);
    }

    /** @test */
    public function cannot_remove_label_not_on_task()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $label = $this->createLabelAction->create($user, $board, [
            'name' => 'Bug',
            'color' => '#FF0000',
        ]);

        $this->expectException(HttpException::class);

        $this->action->remove($user, $task->fresh(['board', 'labels']), $label->id);
    }
}
