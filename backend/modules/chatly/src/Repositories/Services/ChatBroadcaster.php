<?php

namespace Modules\Chatly\Repositories\Services;

use App\Transformers\MessageTransformer;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Message;
use ReflectionClass;

class ChatBroadcaster
{
    protected $conversation;

    protected $message;

    protected $event;

    protected $eventsDir;

    const EVENTS_DIR = '\\App\\Events\\Chat\\';

    /**
     * Set `Conversation`
     *
     * @return self
     */
    public function conversation(Conversation $conversation)
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * Set `Message`
     *
     * @return self
     */
    public function message(Message $message)
    {
        $this->message = (new MessageTransformer($message))->resolve();
        $this->conversation = $message->conversation;

        return $this;
    }

    /**
     * Broad cast events
     *
     * @return void
     */
    public function broadcast()
    {
        foreach ($this->conversation->getParticipants() as $participant) {
            broadcast((new ReflectionClass(self::EVENTS_DIR . "{$this->event}"))
                ->newInstance($participant, $this->message))
                ->toOthers();
        }
    }

    /**
     * Broadcast deleted message
     *
     * @property App\Events\Chat\MessageDeleted $event
     *
     * @return $this
     */
    public function deleted()
    {
        $this->event = 'MessageDeleted';

        return $this;
    }

    /**
     * Broadcast new message
     *
     * @property App\Events\Chat\MessageSent $event
     *
     * @return $this
     */
    public function new()
    {
        $this->event = 'MessageSent';

        return $this;
    }

    /**
     * Broadcast updated message
     *
     * @property App\Events\Chat\MessageUpdated $event
     *
     * @return $this
     */
    public function updated()
    {
        $this->event = 'MessageUpdated';

        return $this;
    }
}
