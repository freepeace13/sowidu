<?php

namespace App\Services\DeliveryTicket;

use App\Models\Addressbook;
use App\Models\CatalogItem;
use App\Models\CatalogItemUnit;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Place;
use App\Models\User;
use Modules\DeliveryTicket\Contracts\External\ModelConfigContract;

class ModelConfigAdapter implements ModelConfigContract
{
    public function getUserModel(): string
    {
        return User::class;
    }

    public function getCompanyModel(): string
    {
        return Company::class;
    }

    public function getOrderModel(): string
    {
        return Order::class;
    }

    public function getAddressbookModel(): string
    {
        return Addressbook::class;
    }

    public function getCatalogItemModel(): string
    {
        return CatalogItem::class;
    }

    public function getCatalogItemUnitModel(): string
    {
        return CatalogItemUnit::class;
    }

    public function getInvoiceModel(): string
    {
        return Invoice::class;
    }

    public function getPlaceModel(): string
    {
        return Place::class;
    }
}
