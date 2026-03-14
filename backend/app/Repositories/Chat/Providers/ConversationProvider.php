<?php

namespace App\Repositories\Chat\Providers;

use App\Models\ChatParticipation;
use App\Repositories\Chat\Interfaces\ProviderInterface;
use App\Traits\InteractsWithImpersonator;
use App\Transformers\ConversationTransformer;
use Illuminate\Http\Request;
use IlluminateAgnostic\Arr\Support\Arr;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;

class ConversationProvider implements ProviderInterface
{
    use InteractsWithImpersonator;

    protected $message;

    /**
     * @var Conversation
     */
    protected $conversation;

    protected $request;

    public function __construct(Request $request, MessageProvider $message)
    {
        $this->message = $message;
        $this->request = $request;
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

        return $this;
    }

    /**
     * Set conversation
     *
     * @param  int  $conversationId
     * @return self
     */
    public function set($conversationId)
    {
        $this->conversation = Chat::conversations()->getById($conversationId);
        $this->message->conversation($this->conversation);

        return $this;
    }

    /**
     * Get conversation messages
     *
     * @return array
     */
    public function messages()
    {
        return $this->message->conversation($this->conversation)->get();
    }

    /**
     * Get `Message` instance for this `conversation`
     *
     * @return MessageProvider
     */
    public function message()
    {
        return $this->message->setRequest($this->request)->conversation($this->conversation);
    }

    /**
     * Mark message as `Read`
     *
     * @return self
     */
    public function readAll()
    {
        $this->conversation->readAll($this->user());

        return $this;
    }

    /**
     * Find conversation using `id`
     *
     * @param  id  $conversationId
     * @return Conversation
     */
    public function find($conversationId)
    {
        return Chat::conversations()->getById($conversationId);
    }

    /**
     * Get conversations
     *
     * @return array ConversationTransformer
     */
    public function get()
    {
        $user = $this->user();
        // $request = $this->request;

        // $params = ['sorting' => 'asc'];

        // $params = array_merge($params, $request->whenFilled('limit', function () use ($request) {
        //     return ['perPage' => $request->limit];
        // }, fn () => []), $request->whenFilled('page', function () use ($request) {
        //     return ['page' => $request->page];
        // }, fn () => []));

        $conversations = ChatParticipation::query()
            ->ofUser($user)
            ->withLastMessage($user)
            ->orderByLastMessage()
            ->ignoreMuted()
            ->get();

        return $conversations
            ->map(function ($participant) use ($user) {
                $participant->loadMissing(['messageable.participation']);

                return (new ConversationTransformer($participant->conversation))
                    ->withLastMessage()
                    ->withUnreadCount($participant->conversation, $user)
                    ->withParticipants(function ($participant) use ($user) {
                        return $participant->messageable->isNot($user);
                    })
                    ->resolve();
            })->toArray();
    }

    /**
     * Create conversation
     *
     * @param  \App\Http\Requests\Chat\Conversation\StoreRequest  $request
     * @return Conversation
     */
    public function create()
    {
        $participants = $this->request->participants();
        $isDirectMessage = count($participants) == 1;

        $conversation = $this->findConversationUsingParticipants($participants);
        if (!$conversation) {
            // Conversation not found - Create new one!
            $conversation = Chat::createConversation(
                [$this->user(), ...$participants],
                $this->request->input('data', []),
            );

            if ($isDirectMessage) {
                $conversation->makeDirect();
            }
        }

        $this->conversation = $conversation;
        if ($this->request->has('media_id')) {
            $this->message->conversation($this->conversation)->withMedia();
        }

        return $conversation;
    }

    /**
     * Find user conversation
     *
     * @todo finding conversation on group will be slow eventually `https://github.com/musonza/chat/issues/235`
     *
     * @param  array  $participants
     * @return Chat
     */
    protected function findConversationUsingParticipants($participants)
    {
        $matchConversation = null;

        $isDirectMessage = count($participants) == 1;
        if ($isDirectMessage) {
            return Chat::conversations()
                ->between($this->user()->loadMissing(['participation.conversation']), reset($participants));
        }

        $page = 1;
        do {
            $conversations = Chat::conversations()->setParticipant($this->user())
                ->isDirect(false)->limit(25)->page($page)->get();

            $page++;
            foreach ($conversations->items() as $userConversation) {
                $query = Participation::where('conversation_id', $userConversation->id)
                    ->where('messageable_id', '!=', $this->user()->id);

                // Conversation mathes the participants count
                if ($query->count() == count($participants)) {
                    $participators = $query->get();
                    $match = true;
                    // Check this `userConversation` if all participants are matched
                    foreach ($participators as $participator) {
                        $messageable = $participator->messageable;
                        $messageableId = $participator->messageable_id;

                        if (empty(Arr::where($participants, function ($p) use ($messageable, $messageableId) {
                            return $p->id == $messageableId && $p->getMorphClass() == $messageable->getMorphClass();
                        }))) {
                            $match = false;
                            break;
                        }
                    }

                    if ($match) {
                        $matchConversation = $userConversation;
                    }
                }
            }
        } while ($conversations->hasMorePages() && !$matchConversation);

        return $matchConversation;
    }

    public function destroy()
    {
        // code...
    }
}
