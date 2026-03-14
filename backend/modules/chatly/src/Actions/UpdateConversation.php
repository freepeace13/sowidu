<?php

namespace Modules\Chatly\Actions;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Contracts\UpdatesConversations;
use Musonza\Chat\Models\Conversation;
use Packages\RestApi\RestApiAction;

class UpdateConversation extends RestApiAction implements UpdatesConversations
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function update($user, Conversation $conversation, array $data, $errorBag = null)
    {
        $this->authorization->authorize($user, 'update', $conversation);

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
