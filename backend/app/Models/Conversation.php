<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    public function recipientMember()
    {
        return $this->belongsTo(Employee::class, 'recipient_mid');
    }

    public function recipientUser()
    {
        return $this->belongsTo(User::class, 'recipient_uid');
    }

    public function senderMember()
    {
        return $this->belongsTo(Employee::class, 'sender_mid');
    }

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_uid');
    }
}
