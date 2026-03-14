<?php

namespace Modules\Chatly\Tests\Unit\Actions;

use Mockery;
use Modules\Chatly\Actions\CreateConversation;
use Modules\Chatly\Contracts\CreatesMessages;
use Modules\Chatly\Contracts\External\UrnResolverContract;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;
use Tests\TestCase;

class CreateConversationTest extends TestCase
{
    protected $messageCreator;

    protected $urnResolver;

    protected $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageCreator = Mockery::mock(CreatesMessages::class);
        $this->urnResolver = Mockery::mock(UrnResolverContract::class);
        $this->action = new CreateConversation($this->messageCreator, $this->urnResolver);
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
    public function it_resolves_recipient_urns()
    {
        $user = Mockery::mock('User');
        $recipient1 = Mockery::mock('User');
        $recipient2 = Mockery::mock('User');
        $conversation = Mockery::mock(Conversation::class);

        $this->urnResolver->shouldReceive('resolve')
            ->once()
            ->with('urn:user:1')
            ->andReturn($recipient1);

        $this->urnResolver->shouldReceive('resolve')
            ->once()
            ->with('urn:user:2')
            ->andReturn($recipient2);

        $makeDirectMock = Mockery::mock();
        $makeDirectMock->shouldReceive('createConversation')
            ->once()
            ->andReturn($conversation);

        $chatMock = Mockery::mock();
        $chatMock->shouldReceive('makeDirect')
            ->once()
            ->andReturn($makeDirectMock);

        Chat::swap($chatMock);

        $result = $this->action->create($user, [
            'recipients' => ['urn:user:1', 'urn:user:2'],
        ]);

        $this->assertSame($conversation, $result);
    }

    /** @test */
    public function it_creates_direct_conversation_for_two_participants()
    {
        $user = Mockery::mock('User');
        $recipient = Mockery::mock('User');
        $conversation = Mockery::mock(Conversation::class);

        $this->urnResolver->shouldReceive('resolve')
            ->once()
            ->andReturn($recipient);

        $chatMock = Mockery::mock();
        $chatMock->shouldReceive('createConversation')
            ->once()
            ->with([$user, $recipient])
            ->andReturn($conversation);

        Chat::swap($chatMock);

        $result = $this->action->create($user, [
            'recipients' => ['urn:user:1'],
        ]);

        $this->assertSame($conversation, $result);
    }

    /** @test */
    public function it_creates_group_conversation_for_more_than_two_participants()
    {
        $user = Mockery::mock('User');
        $recipient1 = Mockery::mock('User');
        $recipient2 = Mockery::mock('User');
        $conversation = Mockery::mock(Conversation::class);

        $this->urnResolver->shouldReceive('resolve')
            ->twice()
            ->andReturn($recipient1, $recipient2);

        $makeDirectMock = Mockery::mock();
        $makeDirectMock->shouldReceive('createConversation')
            ->once()
            ->andReturn($conversation);

        $chatMock = Mockery::mock();
        $chatMock->shouldReceive('makeDirect')
            ->once()
            ->andReturn($makeDirectMock);

        Chat::swap($chatMock);

        $result = $this->action->create($user, [
            'recipients' => ['urn:user:1', 'urn:user:2'],
        ]);

        $this->assertSame($conversation, $result);
    }

    /** @test */
    public function it_creates_initial_message_when_provided()
    {
        $user = Mockery::mock('User');
        $recipient = Mockery::mock('User');
        $conversation = Mockery::mock(Conversation::class);

        $this->urnResolver->shouldReceive('resolve')
            ->once()
            ->andReturn($recipient);

        $chatMock = Mockery::mock();
        $chatMock->shouldReceive('createConversation')
            ->once()
            ->andReturn($conversation);

        Chat::swap($chatMock);

        $this->messageCreator->shouldReceive('create')
            ->once()
            ->with($user, $conversation, [
                'type' => 'text',
                'message' => 'Hello there!',
                'data' => [],
            ]);

        $result = $this->action->create($user, [
            'recipients' => ['urn:user:1'],
            'message' => 'Hello there!',
        ]);

        $this->assertSame($conversation, $result);
    }

    /** @test */
    public function it_validates_required_recipients()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $user = Mockery::mock('User');

        $this->action->create($user, [
            'recipients' => [],
        ]);
    }
}
