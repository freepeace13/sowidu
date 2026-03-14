<?php

namespace App\Repositories\Chat;

use App\Repositories\Chat\Interfaces\BaseChatInterface;
use App\Repositories\Chat\Interfaces\ChatRepositoryInterface;
use App\Repositories\Chat\Providers\ConversationProvider;
use App\Repositories\Chat\Providers\MessageProvider;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;

/**
 * Chat Repository
 *
 * @method ConversationProvider conversations()
 * @method MessageProvider messages()
 *
 * @property-read ConversationProvider $conversations
 * @property-read MessageProvider $messages
 */
class ChatRepository implements BaseChatInterface, ChatRepositoryInterface
{
    use InteractsWithImpersonator;

    protected $message;

    protected $conversation;

    protected $request;

    public function __construct(
        Request $request,
        MessageProvider $message,
        ConversationProvider $conversation,
    ) {
        $this->request = $request;
        $this->message = $message;
        $this->conversation = $conversation;
    }

    /**
     * Set `Request`
     *
     * @param  Request  $request
     * @return parent
     */
    public function setRequest($request)
    {
        $this->request = $request;
        $this->conversation->setRequest($this->request);
        $this->message->setRequest($this->request);

        return $this;
    }

    /**
     * Get MessageProvider
     *
     * @return MessageProvider
     */
    public function messages()
    {
        return $this->conversation->messages();
    }

    /**
     * Get ConversationProvider
     *
     * @return ConversationProvider
     */
    public function conversations()
    {
        return $this->conversation;
    }

    /**
     * Sets `Conversation` model
     *
     * @param  int  $conversationId
     * @return ConversationProvider
     */
    public function conversation($conversationId)
    {
        return $this->conversation->set($conversationId);
    }

    /**
     * Set `Message` model
     *
     * @param  int  $messageId
     * @return MessageProvider
     */
    public function message($messageId = null)
    {
        return $this->message->setMessage($messageId);
    }

    /**
     * Set Message
     *
     * @param  int  $messageId
     * @return void
     */
    public function setMessage($messageId)
    {
        $this->message->setMessage($messageId);

        return $this;
    }

    /**
     * Check whether this message type is attachment
     *
     * @param  string  $type
     * @return bool
     */
    public static function messageIsAttachment($type)
    {
        return $type == self::MESSAGE_TYPE_ATTACHMENT;
    }
}
