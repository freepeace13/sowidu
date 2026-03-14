<?php

namespace App\Listeners\Order\Files;

use App\Actions\Media\AutoShare\OrganizationFileAutoShareToRoles;
use App\Events\Order\OrderFileShareToOtherParty;
use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShareFileToOtherParty implements ShouldQueue
{
    public function handle(OrderFileShareToOtherParty $event)
    {
        $mediaFile = $event->mediaFile;
        $oppositeParty = $event->oppositeParty;

        // If `shareTo` is a `User`
        if (morph_is($oppositeParty, User::class)) {
            $mediaFile->shareTo($oppositeParty);
        }

        // If `shareTo` is a `Company` - share to employees
        if (morph_is($oppositeParty, Company::class)) {
            /**
             * @var Company $company
             */
            $company = $oppositeParty;

            (new OrganizationFileAutoShareToRoles)
                ->execute(
                    $event->account,
                    $company,
                    $mediaFile,
                );
        }
    }
}
