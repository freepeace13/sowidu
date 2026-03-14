<?php

namespace Modules\Todos\Actions\Board\Subscriber;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Board;

class AddsSubscriber
{
    public function add($user, Board $board, array $params)
    {
        Gate::forUser($user)->authorize('addSubscriber', $board);

        $validated = $this->validate($board, $params);

        $board->addSubscriber(
            User::firstWhere('email', $validated['email']),
            $validated['role'],
        );
    }

    protected function validate($board, $params)
    {
        return Validator::make($params, [
            'email' => ['required', 'exists:users,email'],
            'role' => 'in:co-owner,guest',
        ])->after($this->ensureUserCanSubscribeToBoard($board))
            ->validateWithBag('addBoardSubscriber');
    }

    protected function ensureUserCanSubscribeToBoard($board)
    {
        return function ($validator) use ($board) {
            $email = Arr::get($validator->getData(), 'email');

            $validator->errors()->addIf(
                $board->hasUserWithEmail($email),
                'email',
                'The user has already subscribed to the board.',
            );

            $validator->errors()->addIf(
                $board->team && !$board->team->hasUserWithEmail($email),
                'email',
                'The user cannot subscribed to the board.',
            );
        };
    }
}
