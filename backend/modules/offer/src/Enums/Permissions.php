<?php

namespace Modules\Offer\Enums;

/**
 * Offer module permissions.
 */
enum Permissions: string
{
    case CAN_MANAGE_OFFERS = 'can manage offers';
    case CAN_MANAGE_OFFERS_ITEMS = 'can manage offers items';
    case CAN_MODIFY_OFFERS_STATUS = 'can modify offers status';
}
