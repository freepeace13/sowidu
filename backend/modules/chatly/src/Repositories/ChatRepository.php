<?php

namespace Modules\Chatly\Repositories;

use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;
use Modules\Chatly\Repositories\Interfaces\BaseChatInterface;
use Modules\Chatly\Repositories\Interfaces\ChatRepositoryInterface;
use Modules\Chatly\Repositories\Providers\ConversationProvider;
use Modules\Chatly\Repositories\Providers\MessageProvider;

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
