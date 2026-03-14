<?php

declare(strict_types=1);

namespace App\Services\WorkLogs;

use App\Models\ActivityLogReport;
use App\Models\Company;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\User;
use Modules\WorkLogs\Contracts\External\ModelConfigContract;

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

    public function getActivityLogReportModel(): string
    {
        return ActivityLogReport::class;
    }

    public function getInvoiceItemModel(): string
    {
        return InvoiceItem::class;
    }
}
