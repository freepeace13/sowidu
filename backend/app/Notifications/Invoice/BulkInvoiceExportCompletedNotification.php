<?php

namespace App\Notifications\Invoice;

use App\Notifications\Common\BasicNotification;

class BulkInvoiceExportCompletedNotification extends BasicNotification
{
    public function __construct(protected string $url) {}

    public function message()
    {
        return __('invoices.notifications.employee.bulk-export-completed', [
            'link' => $this->url,
        ]);
    }

    protected function redirectTo(): string
    {
        return $this->url;
    }

    protected function timeout(): int
    {
        return 10000;
    }
}
