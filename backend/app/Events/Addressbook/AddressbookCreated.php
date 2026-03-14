<?php

namespace App\Events\Addressbook;

use App\Models\Addressbook;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddressbookCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public $user,
        public Addressbook $addressbook,
        public ?int $teamId = null,
    ) {}
}
