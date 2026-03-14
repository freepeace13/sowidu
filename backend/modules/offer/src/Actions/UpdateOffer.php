<?php

namespace Modules\Offer\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Models\Offer;

class UpdateOffer
{
    use AsAction;

    public function handle(
        Model $user,
        Model $company,
        Offer $offer,
        array $inputs,
    ): Offer {
        Gate::forUser($user)->authorize('update', $offer);

        // Validate inputs
        $validated = $this->validate($inputs);

        data_set(
            $validated,
            'construction_site_id',
            data_get($validated, 'construction_site.id'),
        );

        $validated = data_except($validated, 'construction_site');

        return tap($offer)->update($validated);
    }

    protected function validate(array $inputs): array
    {
        return Validator::make(
            $inputs,
            [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'description' => [
                    'nullable',
                    'string',
                ],
                'offer_date' => [
                    'required',
                    'date_format:Y-m-d',
                ],
                'execution_period_start' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                ],
                'execution_period_end' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                ],
                'construction_site' => 'nullable|array',
                'construction_site.id' => [
                    'nullable',
                    'integer',
                    'exists:places,id',
                ],
            ],
        )->validate();
    }
}
