<?php

namespace Packages\Contacts;

use Illuminate\Database\Eloquent\Model;
use Packages\Contacts\Models\Contactship;

class ContactshipRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function hasContactRequestFrom(Model $sender)
    {
        return Contactship::whereSender($sender)
            ->whereRecipient($this->getModel())
            ->whereStatus(Status::PENDING)
            ->exists();
    }

    public function hasSentContactRequestTo(Model $recipient)
    {
        return Contactship::whereRecipient($recipient)
            ->whereSender($this->getModel())
            ->whereStatus(Status::PENDING)
            ->exists();
    }

    public function hasBlocked(Model $recipient)
    {
        return Contactship::whereSender($this->getModel())
            ->whereRecipient($recipient)
            ->whereStatus(Status::BLOCKED)
            ->exists();
    }

    public function isBlockedBy(Model $recipient)
    {
        return (new static($recipient))->hasBlocked($this->getModel());
    }

    public function isContactWith(Model $recipient)
    {
        return $this->findContactship($recipient)
            ->whereStatus(Status::ACCEPTED)
            ->exists();
    }

    public function getContactship(Model $recipient)
    {
        return $this->findContactship($recipient)->first();
    }

    public function getContactRequests()
    {
        return Contactship::whereRecipient($this->getModel())
            ->whereStatus(Status::PENDING)
            ->get();
    }

    private function findContactship(Model $recipient)
    {
        return Contactship::betweenModels($this->getModel(), $recipient);
    }

    public function getModel()
    {
        return $this->model;
    }
}
