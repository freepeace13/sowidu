<?php

namespace App\Actions\Place;

use App\Actions\Traits\AsAction;
use App\Models\City;
use Illuminate\Support\Facades\Validator;

class AddNewCity
{
    use AsAction;

    public function handle(array $inputs)
    {
        $validated = Validator::make($inputs, [
            'city' => [
                'required',
            ],
            'country' => 'sometimes|required',
        ])->validate();

        if (
            City::where([
                'name' => $validated['city'],
                'country_code' => $validated['country'],
            ])->exists()
        ) {
            return;
        }

        return City::create([
            'name' => $validated['city'],
            'country_code' => $validated['country'],
        ]);
    }
}
