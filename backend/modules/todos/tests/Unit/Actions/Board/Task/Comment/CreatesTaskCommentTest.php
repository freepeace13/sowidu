<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Comment;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\Comment\CreatesTaskComment;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Models\TaskComment;
use Tests\TestCase;

class CreatesTaskCommentTest extends TestCase
{
    use RefreshDatabase;

    protected CreatesTaskComment $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreatesTaskComment;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function board_owner_can_create_comment()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $comment = $this->action->create($user, $task, [
            'message' => 'This is a comment',
        ]);

        $this->assertInstanceOf(TaskComment::class, $comment);
        $this->assertEquals('This is a comment', $comment->message);
    }

    /** @test */
    public function task_member_can_create_comment()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($member, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);
        $this->actingAs($owner);
        $task->addMember($member);

        $this->actingAs($member);
        $comment = $this->action->create($member, $task->fresh(['board', 'members']), [
            'message' => 'Member comment',
        ]);

        $this->assertEquals('Member comment', $comment->message);
    }

    /** @test */
    public function comment_requires_message()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $task, [
            'message' => '',
        ]);
    }

    /** @test */
    public function comment_message_must_be_at_least_3_characters()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $task, [
            'message' => 'ab',
        ]);
    }

    /** @test */
    public function non_subscriber_cannot_create_comment()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->create($nonMember, $task, [
            'message' => 'Unauthorized comment',
        ]);
    }

    /** @test */
    public function non_member_subscriber_cannot_comment_when_permission_disabled()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);

        $board->settings()->permissions()->update('members', 'can_comment', false);
        $board->addSubscriber($guest, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->create($guest, $task->fresh(['board', 'members']), [
            'message' => 'Guest comment',
        ]);
    }

    /** @test */
    public function comment_author_is_set_correctly()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $comment = $this->action->create($user, $task, [
            'message' => 'Test comment',
        ]);

        $this->assertNotNull($comment->author);
        $this->assertEquals($user->id, $comment->author->user_id);
    }
}
