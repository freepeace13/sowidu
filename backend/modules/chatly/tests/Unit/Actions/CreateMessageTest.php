<?php

namespace Modules\Chatly\Tests\Unit\Actions;

use Mockery;
use Modules\Chatly\Actions\CreateMessage;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Message;
use Tests\TestCase;

class CreateMessageTest extends TestCase
{
    protected $authorization;

    protected $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorization = Mockery::mock(AuthorizationContract::class);
        $this->action = new CreateMessage($this->authorization);
    }

    protected function tearDown(): void
    {
        // Clear facade swaps to prevent conflicts
        Chat::clearResolvedInstances();
        \Illuminate\Support\Facades\Facade::clearResolvedInstances();

        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_authorizes_before_creating_message()
    {
        $user = Mockery::mock('User');
        $conversation = Mockery::mock(Conversation::class);
        $message = Mockery::mock(Message::class);

        $this->authorization->shouldReceive('authorize')
            ->once()
            ->with($user, 'sendMessage', $conversation);

        // Use a mock builder that returns self for chaining
        $chatMock = Mockery::mock();
        $chatMock->shouldReceive('message')
            ->once()
            ->with('Hello!')
            ->andReturnSelf();
        $chatMock->shouldReceive('type')
            ->once()
            ->with('text')
            ->andReturnSelf();
        $chatMock->shouldReceive('data')
            ->once()
            ->with([])
            ->andReturnSelf();
        $chatMock->shouldReceive('from')
            ->once()
            ->with($user)
            ->andReturnSelf();
        $chatMock->shouldReceive('to')
            ->once()
            ->with($conversation)
            ->andReturnSelf();
        $chatMock->shouldReceive('send')
            ->once()
            ->andReturn($message);

        Chat::swap($chatMock);

        $result = $this->action->create($user, $conversation, [
            'message' => 'Hello!',
            'type' => 'text',
            'data' => [],
        ]);

        $this->assertSame($message, $result);
    }

    /** @test */
    public function it_uses_default_values_for_missing_params()
    {
        $user = Mockery::mock('User');
        $conversation = Mockery::mock(Conversation::class);
        $message = Mockery::mock(Message::class);

        $this->authorization->shouldReceive('authorize')
            ->once();

        $chatMock = Mockery::mock();
        $chatMock->shouldReceive('message')
            ->once()
            ->with('')
            ->andReturnSelf();
        $chatMock->shouldReceive('type')
            ->once()
            ->with('text')
            ->andReturnSelf();
        $chatMock->shouldReceive('data')
            ->once()
            ->with([])
            ->andReturnSelf();
        $chatMock->shouldReceive('from')
            ->once()
            ->with($user)
            ->andReturnSelf();
        $chatMock->shouldReceive('to')
            ->once()
            ->with($conversation)
            ->andReturnSelf();
        $chatMock->shouldReceive('send')
            ->once()
            ->andReturn($message);

        Chat::swap($chatMock);

        $result = $this->action->create($user, $conversation, []);

        $this->assertSame($message, $result);
    }

    /** @test */
    public function it_creates_text_message()
    {
        $user = Mockery::mock('User');
        $conversation = Mockery::mock(Conversation::class);
        $message = Mockery::mock(Message::class);

        $this->authorization->shouldReceive('authorize')
            ->once();

        $chatMock = Mockery::mock();
        $chatMock->shouldReceive('message')
            ->with('Hello, World!')
            ->andReturnSelf();
        $chatMock->shouldReceive('type')
            ->with('text')
            ->andReturnSelf();
        $chatMock->shouldReceive('data')
            ->with([])
            ->andReturnSelf();
        $chatMock->shouldReceive('from')
            ->andReturnSelf();
        $chatMock->shouldReceive('to')
            ->with($conversation)
            ->andReturnSelf();
        $chatMock->shouldReceive('send')
            ->andReturn($message);

        Chat::swap($chatMock);

        $result = $this->action->create($user, $conversation, [
            'message' => 'Hello, World!',
            'type' => 'text',
            'data' => [],
        ]);

        $this->assertSame($message, $result);
    }

    /** @test */
    public function it_creates_attachment_message()
    {
        $user = Mockery::mock('User');
        $conversation = Mockery::mock(Conversation::class);
        $message = Mockery::mock(Message::class);

        $this->authorization->shouldReceive('authorize')
            ->once();

        $attachmentData = [
            'url' => 'https://example.com/file.pdf',
            'type' => 'pdf',
        ];

        $chatMock = Mockery::mock();
        $chatMock->shouldReceive('message')
            ->with('Sent an attachment')
            ->andReturnSelf();
        $chatMock->shouldReceive('type')
            ->with('attachment')
            ->andReturnSelf();
        $chatMock->shouldReceive('data')
            ->with($attachmentData)
            ->andReturnSelf();
        $chatMock->shouldReceive('from')
            ->with($user)
            ->andReturnSelf();
        $chatMock->shouldReceive('to')
            ->with($conversation)
            ->andReturnSelf();
        $chatMock->shouldReceive('send')
            ->andReturn($message);

        Chat::swap($chatMock);

        $result = $this->action->create($user, $conversation, [
            'message' => 'Sent an attachment',
            'type' => 'attachment',
            'data' => $attachmentData,
        ]);

        $this->assertSame($message, $result);
    }
}
