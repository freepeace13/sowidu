<?php

namespace Modules\Chatly\Http\Controllers\Inertia;

use Illuminate\Http\Request;
use Modules\Chatly\Http\Controllers\BaseController;
use Modules\Chatly\Http\Request\Message\PatchRequest;
use Modules\Chatly\Http\Request\Message\StoreRequest;
use Modules\Chatly\Repositories\ChatRepository;

class MessageController extends BaseController
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
