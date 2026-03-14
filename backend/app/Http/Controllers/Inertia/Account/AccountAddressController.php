<?php

namespace App\Http\Controllers\Inertia\Account;

use App\Actions\Account\Address\AddAccountAddress;
use App\Http\Controllers\Inertia\InertiaController;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;

class AccountAddressController extends InertiaController
{
    use InteractsWithImpersonator;

    public function store(Request $request, AddAccountAddress $adder)
    {
        $place = $adder
            ->add(
                $request->user(),
                $request->mergeIfMissing([
                    'label' => $this->account()?->name ?? $this->account()->full_name,
                ])->all(),
                $this->getCurrentTeam(),
            );

        flash_data($place->id);

        return back(303);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
