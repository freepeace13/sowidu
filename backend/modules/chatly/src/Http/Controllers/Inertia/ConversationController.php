<?php

namespace Modules\Chatly\Http\Controllers\Inertia;

use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Chatly\Http\Controllers\BaseController;
use Modules\Chatly\Http\Request\Conversation\StoreRequest;
use Modules\Chatly\Repositories\ChatRepository;
use Modules\Chatly\Transformers\ConversationTransformer;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class ConversationController extends BaseController
{
    /**
     * @var ChatRepository
     */
    protected $chatRepository;

    public function __construct(ChatRepository $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->wantsJson()) {
            return response()->json($this->chatRepository->conversations()
                ->setRequest($request)->get());
        }

        return Inertia::render('@chatly/Index', [
            'title' => 'Chat',
            'conversations' => $this->chatRepository->conversations()->get(),
        ]);
    }

    /**
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return Inertia
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $conversations = $this->chatRepository->conversations();

        return Inertia::render('@chatly/Show', [
            'title' => 'Chat',
            'conversations' => $conversations->get(),
            'conversation' => (new ConversationTransformer($conversations->find($id)))
                ->withParticipants(function ($participant) use ($user) {
                    return $participant->messageable && $participant->messageable->isNot($user);
                })
                ->resolve(),
            'files' => Inertia::lazy(
                fn () => $user->getMedia()
                    ->getRootFiles(['type' => $request->query('type')])
                    ->map(fn (MediaFile $media) => (new MediaTransformer($media))
                        ->withStarred($user)
                        ->withPolicies($user)
                        ->resolve()),
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreRequest  $request
     * @return \Illuminate\Http\Redirect
     */
    public function store(StoreRequest $request)
    {
        $conversation = $this->chatRepository
            ->setRequest($request)
            ->conversations()
            ->create();

        if ($request->wantsJson() && $request->has('media')) {
            return response()->json(compact('conversation'));
        }

        return redirect()->route('chatly.show', $conversation->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $conversation = $this->chatRepository->conversations()->find($id);

        Chat::conversation($conversation)
            ->setParticipant($this->user())
            ->clear();

        Chat::conversation($conversation)
            ->getParticipation($this->user())
            ->update(['settings' => ['mute_conversation' => true]]);

        return redirect()->route('chatly.index');
    }
}
