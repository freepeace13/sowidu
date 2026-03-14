<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Manual;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Group\CreatesBoardGroup;
use Modules\Todos\Actions\Board\Manual\CreateManualTask;
use Modules\Todos\Actions\Board\Task\Member\AddsTaskMember;
use Modules\Todos\Models\Task;
use Tests\TestCase;

class CreateManualTaskTest extends TestCase
{
    use RefreshDatabase;

    protected CreateManualTask $action;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateManualTask(
            new CreatesBoardGroup,
            new AddsTaskMember,
        );
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function board_owner_can_create_manual_task()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->actingAs($user);
        $task = $this->action->create($user, $board, [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'start_date' => now()->format('Y-m-d'),
            'finish_date' => now()->addDays(5)->format('Y-m-d'),
            'subscriber' => [$user->id],
        ]);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Manual Task', $task->title);
    }

    /** @test */
    public function subscriber_can_create_manual_task_when_permission_enabled()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->settings()->permissions()->update('members', 'can_manage_task', true);
        $board->addSubscriber($subscriber, 'guest');

        $this->actingAs($subscriber);
        $task = $this->action->create($subscriber, $board->fresh(['users', 'subscribers']), [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'start_date' => now()->format('Y-m-d'),
            'finish_date' => now()->addDays(5)->format('Y-m-d'),
            'subscriber' => [$subscriber->id],
        ]);

        $this->assertInstanceOf(Task::class, $task);
    }

    /** @test */
    public function subscriber_cannot_create_manual_task_when_permission_disabled()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->settings()->permissions()->update('members', 'can_manage_task', false);
        $board->addSubscriber($subscriber, 'guest');

        $this->expectException(AuthorizationException::class);

        $this->action->create($subscriber, $board->fresh(['users', 'subscribers']), [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'start_date' => now()->format('Y-m-d'),
            'finish_date' => now()->addDays(5)->format('Y-m-d'),
            'subscriber' => [$subscriber->id],
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'group' => 'Backlog',
            'start_date' => now()->format('Y-m-d'),
            'finish_date' => now()->addDays(5)->format('Y-m-d'),
            'subscriber' => [$user->id],
        ]);
    }

    /** @test */
    public function start_date_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'finish_date' => now()->addDays(5)->format('Y-m-d'),
            'subscriber' => [$user->id],
        ]);
    }

    /** @test */
    public function finish_date_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'start_date' => now()->format('Y-m-d'),
            'subscriber' => [$user->id],
        ]);
    }

    /** @test */
    public function finish_date_must_be_after_or_equal_start_date()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'finish_date' => now()->format('Y-m-d'),
            'subscriber' => [$user->id],
        ]);
    }

    /** @test */
    public function subscriber_is_required()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'start_date' => now()->format('Y-m-d'),
            'finish_date' => now()->addDays(5)->format('Y-m-d'),
        ]);
    }

    /** @test */
    public function subscriber_must_be_board_member()
    {
        $user = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $board, [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'start_date' => now()->format('Y-m-d'),
            'finish_date' => now()->addDays(5)->format('Y-m-d'),
            'subscriber' => [$nonMember->id],
        ]);
    }

    /** @test */
    public function creates_group_if_not_exists()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->actingAs($user);
        $task = $this->action->create($user, $board, [
            'title' => 'Manual Task',
            'group' => 'new-group-name',
            'start_date' => now()->format('Y-m-d'),
            'finish_date' => now()->addDays(5)->format('Y-m-d'),
            'subscriber' => [$user->id],
        ]);

        $this->assertTrue($board->fresh()->settings()->groups()->has('new-group-name'));
    }

    /** @test */
    public function creates_manual_task_record()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);

        $this->actingAs($user);
        $task = $this->action->create($user, $board, [
            'title' => 'Manual Task',
            'group' => 'Backlog',
            'start_date' => '2024-01-01',
            'finish_date' => '2024-01-15',
            'subscriber' => [$user->id],
        ]);

        $this->assertDatabaseHas('todo_manual_tasks', [
            'todo_task_id' => $task->id,
            'start_date' => '2024-01-01',
            'finish_date' => '2024-01-15',
        ]);
    }
}
