<?php

namespace Packages\Contacts\Actions;

use Illuminate\Database\Eloquent\Model;
use Packages\Contacts\Events\ContactRequestCancelled;
use Packages\Contacts\Models\Contactship;

class CancelRequestAction extends ContactAction
{
    public function execute(Model $recipient)
    {
        $sender = $this->repository->getModel();

        $result = Contactship::betweenModels($sender, $recipient)->delete();

        if ($result) {
            event(new ContactRequestCancelled($sender, $recipient));
        }

        return $result;
    }
}
