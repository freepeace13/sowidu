<?php

namespace App\Models;

use App\Events\Invitation\InvitationAccepted;
use App\Models\States\InvitationStates\Traits\HasStates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated This model is deprecated and will be removed in the future.
 * @see \App\Models\CompanyInvitation - use this model instead.
 */
class Invitation extends Model
{
    use HasStates;

    /**
     * @var string
     */
    const EMPLOYMENT = 'employment';

    /**
     * @var string
     */
    const CONTACT = 'contact';

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    public function accept()
    {
        $this->state->transitionTo(
            States\InvitationStates\AcceptedState::class,
            $this,
        );

        InvitationAccepted::broadcast($this);

        return $this;
    }

    public function revoke()
    {
        $this->revoked = true;
        $this->save();
    }

    /**
     * @return void
     */
    public function setRecipientAttribute(Model $value)
    {
        $this->recipient()
            ->associate($value);
    }

    /**
     * @return void
     */
    public function setSenderAttribute(Model $value)
    {
        $this->sender()
            ->associate($value);
    }

    /**
     * @param  string|null  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereType(Builder $query, $type = null)
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereSender(Builder $query, Model $sender)
    {
        return $query
            ->where('sender_id', $sender->getKey())
            ->where('sender_type', $sender->getMorphClass());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRecipient(Builder $query, Model $recipient)
    {
        return $query
            ->where('recipient_id', $recipient->getKey())
            ->where('recipient_type', $recipient->getMorphClass());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenModels(Builder $query, Model $sender, Model $recipient)
    {
        return $query->where(function ($query) use ($sender, $recipient) {
            $query->where(function ($q) use ($sender, $recipient) {
                $q->whereSender($sender)
                    ->whereRecipient($recipient);
            })
                ->orWhere(function ($q) use ($sender, $recipient) {
                    $q->whereSender($recipient)
                        ->whereRecipient($sender);
                });
        });
    }

    public function scopePending(Builder $query)
    {
        $query->where('revoked', 0)
            ->where('declined', 0)
            ->whereNull('accepted_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function sender()
    {
        return $this->morphTo('sender');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function recipient()
    {
        return $this->morphTo('recipient');
    }
}
