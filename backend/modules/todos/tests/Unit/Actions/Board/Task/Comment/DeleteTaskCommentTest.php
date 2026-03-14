<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Comment;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\Comment\CreatesTaskComment;
use Modules\Todos\Actions\Board\Task\Comment\DeleteTaskComment;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Tests\TestCase;

class DeleteTaskCommentTest extends TestCase
{
    use RefreshDatabase;

    protected DeleteTaskComment $action;

    protected CreatesTaskComment $createCommentAction;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteTaskComment;
        $this->createCommentAction = new CreatesTaskComment;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function comment_owner_can_delete()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $comment = $this->createCommentAction->create($user, $task, [
            'message' => 'Comment to delete',
        ]);

        $commentId = $comment->id;

        $this->actingAs($user);
        $this->action->delete($user, $task, $comment);

        $this->assertDatabaseMissing('todo_task_comments', ['id' => $commentId]);
    }

    /** @test */
    public function non_owner_cannot_delete_comment()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($otherUser, 'guest');
        $board->settings()->permissions()->update('members', 'can_comment', true);

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $comment = $this->createCommentAction->create($owner, $task->fresh(['board', 'members']), [
            'message' => 'Owner comment',
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($otherUser, $task->fresh(['board']), $comment);
    }

    /** @test */
    public function non_subscriber_cannot_delete_comment()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $comment = $this->createCommentAction->create($owner, $task, [
            'message' => 'Comment',
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($nonMember, $task, $comment);
    }

    /** @test */
    public function subscriber_can_delete_own_comment()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($subscriber, 'guest');
        $board->settings()->permissions()->update('members', 'can_comment', true);

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $comment = $this->createCommentAction->create($subscriber, $task->fresh(['board', 'members']), [
            'message' => 'Subscriber comment',
        ]);

        $commentId = $comment->id;

        $this->actingAs($subscriber);
        $this->action->delete($subscriber, $task->fresh(['board']), $comment);

        $this->assertDatabaseMissing('todo_task_comments', ['id' => $commentId]);
    }
}
