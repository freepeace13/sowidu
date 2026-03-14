<?php

namespace App\Events\Auth;

use App\Contracts\Auth\AuthorizableGroup;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountActivated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Contracts\Auth\AuthorizableGroup
     */
    public $account;

    /**
     * Create a new event instance.
     *
     * @param \App\Contracts\Auth\AuthorizableGroup
     * @return void
     */
    public function __construct(AuthorizableGroup $account)
    {
        $this->account = $account;
    }
}
