<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Attachment;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\Attachment\CreateTaskAttachment;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Tests\TestCase;

class CreateTaskAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected CreateTaskAttachment $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateTaskAttachment;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
    }

    /** @test */
    public function board_owner_can_create_attachment()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $task, []);
    }

    /** @test */
    public function subscriber_can_create_attachment()
    {
        $owner = User::factory()->create();
        $subscriber = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($subscriber, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->create($subscriber, $task->fresh(['board']), []);
    }

    /** @test */
    public function non_subscriber_cannot_create_attachment()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $this->expectException(AuthorizationException::class);

        $this->action->create($nonMember, $task, [
            'media_id' => 1,
        ]);
    }

    /** @test */
    public function attachment_requires_file_or_media_id()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $task, []);
    }

    /** @test */
    public function attachment_validates_resumable_type()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $this->expectException(ValidationException::class);

        $this->action->create($user, $task, [
            'resumableType' => 'invalid/mimetype',
        ]);
    }
}
