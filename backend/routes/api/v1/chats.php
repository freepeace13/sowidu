<?php

use App\Http\Api\Controllers\V1\Chats\AddParticipantController;
use App\Http\Api\Controllers\V1\Chats\DeleteConversationController;
use App\Http\Api\Controllers\V1\Chats\DeleteMessageController;
use App\Http\Api\Controllers\V1\Chats\GetConversationsController;
use App\Http\Api\Controllers\V1\Chats\GetMessagesController;
use App\Http\Api\Controllers\V1\Chats\GetParticipantsController;
use App\Http\Api\Controllers\V1\Chats\RemoveParticipantController;
use App\Http\Api\Controllers\V1\Chats\SendMessageController;
use App\Http\Api\Controllers\V1\Chats\ShowConversationController;
use App\Http\Api\Controllers\V1\Chats\StoreConversationController;
use App\Http\Api\Controllers\V1\Chats\UpdateConversationController;
use Illuminate\Support\Facades\Route;

Route::get('/', GetConversationsController::class);
Route::post('/', StoreConversationController::class);
Route::get('/{conversation}', ShowConversationController::class);
Route::patch('/{conversation}', UpdateConversationController::class);
Route::delete('/{conversation}', DeleteConversationController::class);

Route::get('/{conversation}/participants', GetParticipantsController::class);
Route::post('/{conversation}/participants', AddParticipantController::class);
Route::delete('/{conversation}/participants/{participation}', RemoveParticipantController::class);

Route::get('/{conversation}/messages', GetMessagesController::class);
Route::post('/{conversation}/messages', SendMessageController::class);
Route::delete('/{conversation}/messages/{message}', DeleteMessageController::class);
