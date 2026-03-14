<?php

declare(strict_types=1);

namespace App\Services\WorkLogs;

use App\Support\Facades\Impersonate;
use Modules\WorkLogs\Contracts\External\ImpersonatorContract;

class ImpersonatorAdapter implements ImpersonatorContract
{
    public function isImpersonating(): bool
    {
        return Impersonate::isImpersonating();
    }

    public function getTeamId(): ?int
    {
        return Impersonate::tenant()?->id;
    }

    public function getUser(): mixed
    {
        return Impersonate::user();
    }

    public function getCompany(): mixed
    {
        return Impersonate::tenant();
    }
}
