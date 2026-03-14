<?php

namespace App\Services\Order;

use App\Enums\Permissions;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\PermissionService;

class OrderViewerService
{
    protected User $user;

    protected ?Company $company;

    public function __construct(protected Order $order) {}

    public static function make(Order $order): static
    {
        return new static($order);
    }

    public function viewer(User $user, ?Company $company): self
    {
        $this->user = $user;
        $this->company = $company;

        return $this;
    }

    public function user(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function company(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function viewerIsPrivateUser(): bool
    {
        return blank($this->company);
    }

    public function isClient(): bool
    {
        return $this->order->client()->is($this->company ?? $this->user);
    }

    public function canViewPurchasingPrice(): bool
    {
        return $this->isClient() ?: PermissionService::allows(Permissions::CAN_VIEW_PURCHASING_PRICE);
    }

    public function canViewSellingPrice(): bool
    {
        return $this->isClient() ?: PermissionService::allows(Permissions::CAN_VIEW_SELLING_PRICE);
    }
}
