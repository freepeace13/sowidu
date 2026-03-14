<?php

namespace Modules\Offer\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Models\Offer;

class DeleteOffer
{
    use AsAction;

    public function handle(Model $user, Model $company, Offer $offer): ?bool
    {
        Gate::forUser($user)->allows('delete', $offer);

        return $offer->delete();
    }
}
