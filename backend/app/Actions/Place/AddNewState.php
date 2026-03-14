<?php

namespace App\Actions\Place;

use App\Actions\Traits\AsAction;
use App\Models\State;
use Illuminate\Support\Facades\Validator;

class AddNewState
{
    use AsAction;

    public function handle(array $inputs)
    {
        $validated = Validator::make($inputs, [
            'state' => [
                'required',
                'string',
            ],
            'country' => 'sometimes|required',
        ])->validate();

        if (
            State::where([
                'name' => $validated['state'],
                'country_code' => $validated['country'],
            ])->exists()
        ) {
            return;
        }

        return State::create([
            'name' => $validated['state'],
            'country_code' => $validated['country'],
        ]);
    }
}
