<?php

namespace Packages\Contacts\Models;

use Illuminate\Database\Eloquent\Model;

class Contactship extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function __construct(array $attributes = [])
    {
        $this->table = config('contactships.table_name');

        parent::__construct($attributes);
    }

    public function fillRecipient($recipient)
    {
        return $this->fill([
            'recipient_id' => $recipient->getKey(),
            'recipient_type' => $recipient->getMorphClass(),
        ]);
    }

    public function fillSender($sender)
    {
        return $this->fill([
            'sender_id' => $sender->getKey(),
            'sender_type' => $sender->getMorphClass(),
        ]);
    }

    public function scopeWhereRecipient($query, $model)
    {
        return $query
            ->where('recipient_id', $model->getKey())
            ->where('recipient_type', $model->getMorphClass());
    }

    public function scopeWhereSender($query, $model)
    {
        return $query
            ->where('sender_id', $model->getKey())
            ->where('sender_type', $model->getMorphClass());
    }

    public function scopeBetweenModels($query, $sender, $recipient)
    {
        $query->where(function ($queryIn) use ($sender, $recipient) {
            $queryIn->where(function ($q) use ($sender, $recipient) {
                $q->whereSender($sender)->whereRecipient($recipient);
            })->orWhere(function ($q) use ($sender, $recipient) {
                $q->whereSender($recipient)->whereRecipient($sender);
            });
        });
    }

    public function recipient()
    {
        return $this->morphTo('recipient');
    }

    public function sender()
    {
        return $this->morphTo('sender');
    }
}
