<?php

namespace Packages\Contacts\Actions;

use Illuminate\Database\Eloquent\Model;
use Packages\Contacts\Events\ContactUnblocked;
use Packages\Contacts\Models\Contactship;

class UnblockContactAction extends ContactAction
{
    public function execute(Model $recipient)
    {
        $sender = $this->repository->getModel();

        $result = Contactship::betweenModels($sender, $recipient)
            ->whereSender($sender)
            ->delete();

        if ($result) {
            event(new ContactUnblocked($sender, $recipient));
        }

        return $result;
    }
}
