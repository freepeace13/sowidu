<?php

namespace App\Factories\Todo;

use App\Actions\Todo\Board\CreatesBoard;
use App\Models\Company;
use App\Models\User;

class PredefinedBoardFactory
{
    public static function make(User $user, ?Company $company = null)
    {
        array_map(
            fn ($predefinedBoard) => (new CreatesBoard)->create($user, array_merge_recursive(
                $predefinedBoard,
                [
                    'settings' => [
                        'is_predefined' => true,
                    ],
                ],
                [
                    'team_id' => $company?->id,
                ],
            )),
            config('todo.board.predefined'),
        );
    }
}
