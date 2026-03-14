<?php

namespace App\Enums;

enum CompanySetting: string
{
    use \ArchTech\Enums\InvokableCases;

    case AUTO_SHARE_TO_ROLES = 'auto_share_to_roles';
}
