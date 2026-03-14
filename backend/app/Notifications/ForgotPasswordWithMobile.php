<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;

class ForgotPasswordWithMobile extends Notification
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['nexmo'];
    }

    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
            ->content(self::getMessage($this->user));
    }

    private static function getMessage($user)
    {
        $firstname = ucfirst($user->first_name);
        $code = $user->verification->code;

        return "Hi {$firstname}, you attempt to reset your password. Your reset password code is {$code}.";
    }
}
