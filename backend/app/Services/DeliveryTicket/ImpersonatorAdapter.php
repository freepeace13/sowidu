<?php

namespace App\Services\DeliveryTicket;

use App\Traits\InteractsWithImpersonator;
use Modules\DeliveryTicket\Contracts\External\ImpersonatorContract;

class ImpersonatorAdapter implements ImpersonatorContract
{
    use InteractsWithImpersonator;

    public function isImpersonating(): bool
    {
        return $this->checkImpersonating();
    }

    public function getImpersonatedUser(): mixed
    {
        return $this->impersonatedUser();
    }

    public function getOriginalUser(): mixed
    {
        return $this->impersonator();
    }

    private function checkImpersonating(): bool
    {
        return session()->has('impersonator_id');
    }

    private function impersonatedUser(): mixed
    {
        return auth()->user();
    }

    private function impersonator(): mixed
    {
        $id = session('impersonator_id');

        return $id ? \App\Models\User::find($id) : null;
    }
}
