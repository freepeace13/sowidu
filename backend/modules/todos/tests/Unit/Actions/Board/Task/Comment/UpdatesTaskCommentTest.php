<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Comment;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\Comment\CreatesTaskComment;
use Modules\Todos\Actions\Board\Task\Comment\UpdatesTaskComment;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Tests\TestCase;

class UpdatesTaskCommentTest extends TestCase
{
    use RefreshDatabase;

    protected UpdatesTaskComment $action;

    protected CreatesTaskComment $createCommentAction;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdatesTaskComment;
        $this->createCommentAction = new CreatesTaskComment;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function comment_owner_can_update()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $comment = $this->createCommentAction->create($user, $task, [
            'message' => 'Original message',
        ]);

        $this->action->update($user, $task, $comment, [
            'message' => 'Updated message',
        ]);

        $this->assertEquals('Updated message', $comment->fresh()->message);
    }

    /** @test */
    public function non_owner_cannot_update_comment()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($otherUser, 'guest');
        $board->settings()->permissions()->update('members', 'can_comment', true);

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $comment = $this->createCommentAction->create($owner, $task->fresh(['board', 'members']), [
            'message' => 'Original message',
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->update($otherUser, $task->fresh(['board']), $comment, [
            'message' => 'Attempted update',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_update_comment()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $comment = $this->createCommentAction->create($owner, $task, [
            'message' => 'Original message',
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->update($nonMember, $task, $comment, [
            'message' => 'Attempted update',
        ]);
    }

    /** @test */
    public function update_requires_message()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $comment = $this->createCommentAction->create($user, $task, [
            'message' => 'Original message',
        ]);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $task, $comment, [
            'message' => '',
        ]);
    }

    /** @test */
    public function update_message_must_be_at_least_3_characters()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $comment = $this->createCommentAction->create($user, $task, [
            'message' => 'Original message',
        ]);

        $this->expectException(ValidationException::class);

        $this->action->update($user, $task, $comment, [
            'message' => 'ab',
        ]);
    }
}
