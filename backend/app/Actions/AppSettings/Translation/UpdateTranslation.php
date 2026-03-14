<?php

namespace App\Actions\AppSettings\Translation;

use App\Actions\Traits\AsAction;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateTranslation
{
    use AsAction;

    public function handle(User $user, array $inputs)
    {
        $validated = $this->validate($inputs);

        TranslationService::make()->update($validated['key'], $validated['text']);
    }

    protected function validate(array $inputs)
    {
        $keys = TranslationService::make()->all()
            ->pluck('key')
            ->toArray();

        return Validator::make($inputs, [
            'key' => [
                'required',
                Rule::in($keys),
            ],
            'text' => [
                'nullable',
                'string',
            ],
        ])->validate();
    }
}
