<?php

namespace Packages\Contacts\Actions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Packages\Contacts\Events\ContactAccepted;
use Packages\Contacts\Events\ContactRequestSent;
use Packages\Contacts\Models\Contactship;
use Packages\Contacts\Status;

class AddContactAction extends ContactAction
{
    public function execute(Model $recipient, $accepted = false)
    {
        if (!$this->canBeContact($recipient)) {
            return false;
        }

        $sender = $this->repository->getModel();

        $contactship = (new Contactship)
            ->fillSender($sender)
            ->fillRecipient($recipient)
            ->fill([
                'status' => $accepted
                    ? Status::ACCEPTED
                    : $this->determineStatus($recipient),
            ]);

        $contactship->save();

        $eventClass = ($contactship->status == Status::ACCEPTED)
            ? ContactAccepted::class
            : ContactRequestSent::class;

        event(new $eventClass($contactship));

        return $contactship;
    }

    protected function canBeContact(Model $recipient)
    {
        $sender = $this->repository->getModel();

        if ($this->repository->hasBlocked($recipient)) {
            (new UnblockContactAction($sender))->execute($recipient);

            return true;
        }

        if ($contactship = $this->repository->getContactship($recipient)) {
            if ($contactship->status != Status::DENIED) {
                return false;
            }
        }

        return true;
    }

    private function determineStatus(Model $recipient)
    {
        return is_a($recipient, User::class)
            ? Status::PENDING
            : Status::ACCEPTED;
    }
}
