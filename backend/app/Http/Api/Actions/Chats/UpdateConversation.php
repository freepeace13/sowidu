<?php

namespace App\Http\Api\Actions\Chats;

use App\Contracts\Actions\UpdatesConversations;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Musonza\Chat\Models\Conversation;
use Packages\RestApi\RestApiAction;

class UpdateConversation extends RestApiAction implements UpdatesConversations
{
    public function update($user, Conversation $conversation, array $data, $errorBag = null)
    {
        Gate::forUser($user)->authorize('update', $conversation);

        $validated = $this->validate($data);

        $conversation->update([
            'data' => array_replace($conversation->data, $validated),
        ]);

        return $conversation->fresh();
    }

    public function getValidationRules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:3'],
            'avatar' => [
                'nullable',
                File::image()
                    ->min(1024)
                    ->max(12 * 1024)
                    ->dimensions(Rule::dimensions()->maxWidth(1000)->maxHeight(500)),
            ],
        ];
    }
}
