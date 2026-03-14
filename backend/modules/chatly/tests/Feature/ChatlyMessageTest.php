<?php

namespace Modules\Chatly\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Modules\Chatly\Http\Controllers\Inertia\ConversationController;
use Modules\Chatly\Http\Controllers\Inertia\MessageController;
use Modules\Chatly\Http\Request\Message\PatchRequest;
use Modules\Chatly\Http\Request\Message\StoreRequest;
use Modules\Chatly\Repositories\ChatRepository;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Tests\TestCase;

class ChatlyMessageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $otherUser;

    protected $conversation;

    protected $messageId;

    protected function setUp(): void
    {
        parent::setUp();

        // Use factory instead of firstOrCreate to avoid UUID issues
        $this->user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->otherUser = User::factory()->create([
            'first_name' => 'Other',
            'last_name' => 'User',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->conversation = Chat::createConversation([$this->user, $this->otherUser]);
        $this->actingAs($this->user);
    }

    public function test_index_message()
    {
        $message1 = Chat::message('Hello')
            ->from($this->user)
            ->to($this->conversation)
            ->send();

        $message2 = Chat::message('Hi there')
            ->from($this->otherUser)
            ->to($this->conversation)
            ->send();

        $controller = new MessageController(app(ChatRepository::class));
        $response = $controller->index($this->conversation->id, new Request);

        $this->assertJson($response->content());

        $responseData = json_decode($response->content());

        $this->assertIsObject($responseData);
        $this->assertObjectHasProperty('items', $responseData);
        $this->assertIsArray($responseData->items);
        $this->assertCount(2, $responseData->items);

        $bodies = array_map(function ($msg) {
            return $msg->body;
        }, $responseData->items);

        $this->assertContains('Hello', $bodies);
        $this->assertContains('Hi there', $bodies);
    }

    public function test_store_message()
    {
        $request = new StoreRequest;
        $request->replace([
            'message' => ['body' => 'Test message content'],
        ]);

        $controller = new MessageController(app(ChatRepository::class));
        $response = $controller->store($request, $this->conversation->id);

        $this->assertIsArray($response);
        $this->assertEquals('Test message content', $response['body']);

        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $this->conversation->id,
            'body' => 'Test message content',
        ]);
    }

    public function test_update_message()
    {
        $storeRequest = new StoreRequest;
        $storeRequest->replace([
            'message' => ['body' => 'Original message content'],
        ]);

        $controller = new MessageController(app(ChatRepository::class));
        $message = $controller->store($storeRequest, $this->conversation->id);

        $this->assertEquals('Original message content', $message['body']);

        $updateRequest = new PatchRequest;
        $updateRequest->replace([
            'message' => ['body' => 'Updated message content'],
        ]);

        $updatedMessage = $controller->patch(
            $updateRequest,
            $this->conversation->id,
            $message['id'],
        );

        $this->assertEquals('Updated message content', $updatedMessage['body']);

        $this->assertDatabaseHas('chat_messages', [
            'id' => $message['id'],
            'body' => 'Updated message content',
        ]);
    }

    public function test_delete_message()
    {
        $message = Chat::message('Message to be deleted')
            ->from($this->user)
            ->to($this->conversation)
            ->send();

        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'body' => 'Message to be deleted',
        ]);

        $controller = new MessageController(app(ChatRepository::class));
        $response = $controller->destroy($this->conversation->id, $message->id);

        $this->assertEquals(200, $response->getStatusCode());

        $messages = Chat::conversation($this->conversation)
            ->setParticipant($this->user)
            ->getMessages();

        $this->assertFalse($messages->contains('id', $message->id));
    }

    public function test_delete_conversation()
    {
        Chat::message('Message 1')
            ->from($this->user)
            ->to($this->conversation)
            ->send();

        Chat::message('Message 2')
            ->from($this->otherUser)
            ->to($this->conversation)
            ->send();

        $controller = new ConversationController(app(ChatRepository::class));
        $response = $controller->destroy($this->conversation->id);

        $this->assertEquals(302, $response->getStatusCode());
    }
}
