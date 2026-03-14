<?php

namespace Modules\Todos\Tests\Unit\Actions\Board\Task\Attachment;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\Task\Attachment\DeleteTaskAttachment;
use Modules\Todos\Actions\Board\Task\CreatesBoardTask;
use Modules\Todos\Models\TaskAttachment;
use Tests\TestCase;

class DeleteTaskAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected DeleteTaskAttachment $action;

    protected CreatesBoardTask $createTaskAction;

    protected CreatesBoard $createBoardAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteTaskAttachment;
        $this->createTaskAction = new CreatesBoardTask;
        $this->createBoardAction = new CreatesBoard;
        Storage::fake('public');
    }

    /** @test */
    public function attachment_owner_can_delete_attachment()
    {
        $user = User::factory()->create();
        $board = $this->createBoardAction->create($user, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($user, $board, ['title' => 'Task']);

        $subscriber = $board->getSubscriber($user);
        $attachment = TaskAttachment::create([
            'task_id' => $task->id,
            'author_id' => $subscriber->id,
            'path' => '/test/path.jpg',
            'properties' => ['file_path' => 'test/path'],
        ]);
        $attachmentId = $attachment->id;

        $this->action->delete($user, $task->fresh(['board']), $attachment);

        $this->assertDatabaseMissing('todo_task_attachments', ['id' => $attachmentId]);
    }

    /** @test */
    public function non_owner_cannot_delete_attachment()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $board->addSubscriber($otherUser, 'guest');

        $task = $this->createTaskAction->create($owner, $board->fresh(['users', 'subscribers']), ['title' => 'Task']);

        $subscriber = $board->getSubscriber($owner);
        $attachment = TaskAttachment::create([
            'task_id' => $task->id,
            'author_id' => $subscriber->id,
            'path' => '/test/path.jpg',
            'properties' => ['file_path' => 'test/path'],
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($otherUser, $task->fresh(['board']), $attachment);
    }

    /** @test */
    public function non_subscriber_cannot_delete_attachment()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $board = $this->createBoardAction->create($owner, ['title' => 'Test Board']);
        $task = $this->createTaskAction->create($owner, $board, ['title' => 'Task']);

        $subscriber = $board->getSubscriber($owner);
        $attachment = TaskAttachment::create([
            'task_id' => $task->id,
            'author_id' => $subscriber->id,
            'path' => '/test/path.jpg',
            'properties' => ['file_path' => 'test/path'],
        ]);

        $this->expectException(AuthorizationException::class);

        $this->action->delete($nonMember, $task->fresh(['board']), $attachment);
    }
}
