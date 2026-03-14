<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\External;

/**
 * Outgoing port for model configuration.
 *
 * Provides the class names for Eloquent relationships
 * so the module doesn't need to import main app models directly.
 */
interface ModelConfigContract
{
    /**
     * Get the User model class name.
     */
    public function getUserModel(): string;

    /**
     * Get the Company model class name.
     */
    public function getCompanyModel(): string;

    /**
     * Get the Order model class name.
     */
    public function getOrderModel(): string;

    /**
     * Get the ActivityLogReport model class name.
     */
    public function getActivityLogReportModel(): string;

    /**
     * Get the InvoiceItem model class name.
     */
    public function getInvoiceItemModel(): string;
}
