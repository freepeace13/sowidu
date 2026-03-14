<?php

namespace App\Repositories\Chat\Providers;

use App\Repositories\Chat\Interfaces\BaseChatInterface;
use App\Repositories\Chat\Interfaces\ProviderInterface;
use App\Repositories\Chat\Services\AttachmentBuilder;
use App\Repositories\Chat\Services\ChatBroadcaster;
use App\Traits\InteractsWithImpersonator;
use App\Transformers\MessageTransformer;
use Illuminate\Http\Request;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;

class MessageProvider implements BaseChatInterface, ProviderInterface
{
    use InteractsWithImpersonator;

    protected $attachmentBuilder;

    protected $broadcaster;

    protected $message;

    protected $conversation;

    protected $request;

    public function __construct(Request $request, Conversation $conversation)
    {
        $this->attachmentBuilder = new AttachmentBuilder;
        $this->broadcaster = new ChatBroadcaster;
        $this->conversation = $conversation;
        $this->request = $request;
    }

    /**
     * Set `Request`
     *
     * @param  Request  $request
     * @return self
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set Conversation
     *
     * @return self
     */
    public function conversation(Conversation $conversation)
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * Sets `Message` model
     *
     * @param  int  $messageId
     * @return self
     */
    public function setMessage($messageId)
    {
        $this->message = Chat::messages()->getById($messageId);
        $this->conversation = $this->message->conversation;
        $this->broadcaster
            ->conversation($this->conversation)
            ->message($this->message);

        return $this;
    }

    /**
     * Fetch chat messages from the conversation
     *
     * @param  int  $conversationId
     * @param  Request  $request
     * @return array
     */
    public function get()
    {
        $messages = Chat::conversation($this->conversation)
            ->setParticipant($this->user())
            ->setPaginationParams([
                'page' => $this->request->get('page', 1),
                'perPage' => 10,
                'sorting' => 'desc',
            ])
            ->getMessages();

        // Sort messages
        $messageCollection = $messages->loadMissing(['participation'])
            ->sort()->map(function ($message) {
                return (new MessageTransformer($message))->resolve();
            });

        $items = [];
        foreach ($messageCollection as $message) {
            array_push($items, $message);
        }

        return [
            'items' => $items,
            'total' => $messages->total(),
            'last_page' => $messages->lastPage(),
            'current_page' => $messages->currentPage(),
            'per_page' => $messages->perPage(),
        ];
    }

    /**
     * Delete message
     *
     * @param  int  $messageId
     * @return void
     */
    public function destroy()
    {
        foreach ($this->message->conversation->getParticipants() as $participant) {
            Chat::message($this->message)
                ->setParticipant($participant)
                ->delete();
        }

        $this->broadcaster->deleted()->broadcast();
    }

    /**
     * Update message
     *
     * @return MessageTransformer
     */
    public function update()
    {
        $this->message->update(['body' => $this->request->message['body']]);
        $this->broadcaster->updated()->broadcast();

        return (new MessageTransformer($this->message))->resolve();
    }

    /**
     * Create message
     *
     * @return MessageTransformer
     */
    public function create()
    {
        if ($this->request->has('file')) {
            return $this->withFile();
        }

        if ($this->request->has('media_id')) {
            return $this->withMedia();
        }

        if ($this->request->has('message')) {
            return (new MessageTransformer($this->storeTextMessage()))
                ->resolve();
        }
    }

    /**
     * Store message as type `text`
     *
     * @return \Musonza\Chat\Models\Message
     */
    public function storeTextMessage()
    {
        $message = Chat::message($this->request->body ?: $this->request->message['body'])
            ->from($this->user())
            ->type(self::MESSAGE_TYPE_TEXT)
            ->to($this->conversation)
            ->send();

        $this->broadcaster->message($message)->new()->broadcast();

        return $message;
    }

    /**
     * Store message with attachmnent
     *
     * @param  array  $additionalData
     * @return MessageTransformer
     */
    public function storeMessageWithAttachment($additionalData)
    {
        $message = Chat::message($this->request->body ?? 'Sent an attachment')
            ->from($this->user())
            ->data($additionalData)
            ->type(self::MESSAGE_TYPE_ATTACHMENT)
            ->to($this->conversation)
            ->send();

        $this->broadcaster->message($message)->new()->broadcast();

        return (new MessageTransformer($message))->resolve();
    }

    /**
     * Handle `Media` attachment
     *
     * @return MessageTransformer
     */
    public function withMedia()
    {
        return $this->storeMessageWithAttachment(
            $this->attachmentBuilder->conversation($this->conversation->id)
                ->build($this->request->media_id),
        );
    }

    /**
     * Handle `File` attachment
     *
     * @return MessageTransformer|Response
     */
    public function withFile()
    {
        $file = $this->request->file;

        $progress = $this->request->receiveChunkFiles();

        if ($progress->isFinished()) {
            $file = $progress->getFile();

            return $this->storeMessageWithAttachment(
                $this->attachmentBuilder->conversation($this->conversation->id)
                    ->build($file),
            );
        }

        return response()->json([
            'progress' => $progress->handler()->getPercentageDone(),
        ]);
    }
}
