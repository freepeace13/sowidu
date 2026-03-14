<?php

namespace App\Http\Controllers\Inertia\Chat;

use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Requests\Chat\Message\PatchRequest;
use App\Http\Requests\Chat\Message\StoreRequest;
use App\Repositories\Chat\ChatRepository;
use Illuminate\Http\Request;

class MessageController extends InertiaController
{
    /**
     * @var ChatRepository
     */
    protected $chatRepository;

    public function __construct(ChatRepository $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    public function index($conversationId, Request $request)
    {
        return response()->json(
            $this->chatRepository
                ->conversation($conversationId)
                ->readAll()
                ->messages(),
        );
    }

    public function store(StoreRequest $request, $conversationId)
    {
        return $this->chatRepository
            ->setRequest($request)
            ->conversation($conversationId)
            ->message()
            ->create();
    }

    public function destroy($conversationId, $messageId)
    {
        $this->chatRepository
            ->message($messageId)
            ->destroy();

        return response()->json();
    }

    public function patch(PatchRequest $request, $conversationId, $messageId)
    {
        return $this->chatRepository
            ->message($messageId)
            ->setRequest($request)
            ->update();
    }

    public function read($conversationId)
    {
        $this->chatRepository
            ->conversation($conversationId)
            ->readAll();

        return response()->json();
    }
}
