<?php

namespace Packages\Contacts\Actions;

use Illuminate\Database\Eloquent\Model;
use Packages\Contacts\Events\ContactBlocked;
use Packages\Contacts\Models\Contactship;
use Packages\Contacts\Status;

class BlockContactAction extends ContactAction
{
    public function execute(Model $recipient)
    {
        if ($this->repository->isBlockedBy($recipient)) {
            Contactship::betweenModels($sender, $recipient)->delete();
        }

        $sender = $this->repository->getModel();

        $contactship = (new Contactship)
            ->fillSender($sender)
            ->fillRecipient($recipient)
            ->fill([
                'status' => Status::BLOCKED,
            ]);

        $result = $contactship->save();

        if ($result) {
            event(new ContactBlocked($contactship->sender, $contactship->recipient));
        }

        return $result;
    }
}
