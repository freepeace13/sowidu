<?php

namespace Packages\Contacts\Actions;

use Illuminate\Database\Eloquent\Model;
use Packages\Contacts\Events\ContactRequestDenied;
use Packages\Contacts\Models\Contactship;
use Packages\Contacts\Status;

class DenyRequestAction extends ContactAction
{
    public function execute(Model $sender)
    {
        $recipient = $this->repository->getModel();

        $contactship = Contactship::betweenModels($recipient, $sender)
            ->whereRecipient($recipient)
            ->first();

        if ($contactship) {
            $result = $contactship->fill(['status' => Status::DENIED])->save();

            event(new ContactRequestDenied($contactship));

            return $result;
        }

        return false;
    }
}
