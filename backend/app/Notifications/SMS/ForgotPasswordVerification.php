<?php

namespace App\Notifications\SMS;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;

class ForgotPasswordVerification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Holds the verification instnace
     *
     * @var App\Models\Verification
     */
    protected $token;

    /**
     * Holds the user instnace
     *
     * @var App\Models\User
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param  App\Models\Verification  $token
     * @param  App\Models\User  $user
     * @return void
     */
    public function __construct(Verification $token, User $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['nexmo'];
    }

    /**
     * Get nexmo message representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)->content(
            "Hi {$this->user->username}, you requested to reset your password.
            Your verification code is {$this->token->code}.",
        );
    }
}
