<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for external model class configuration.
 *
 * Provides model class names for Eloquent relationships
 * without creating hard dependencies on main app models.
 */
interface ModelConfigContract
{
    /**
     * Get the User model class.
     */
    public function getUserModel(): string;

    /**
     * Get the Company model class.
     */
    public function getCompanyModel(): string;

    /**
     * Get the Order model class.
     */
    public function getOrderModel(): string;

    /**
     * Get the Addressbook model class.
     */
    public function getAddressbookModel(): string;

    /**
     * Get the CatalogItem model class.
     */
    public function getCatalogItemModel(): string;

    /**
     * Get the CatalogItemUnit model class.
     */
    public function getCatalogItemUnitModel(): string;

    /**
     * Get the Invoice model class.
     */
    public function getInvoiceModel(): string;

    /**
     * Get the Place model class.
     */
    public function getPlaceModel(): string;
}
