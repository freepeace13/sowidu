<?php

namespace App\Notifications\SMS;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;

class AccountActivation extends Notification implements ShouldQueue
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
        $redirectUrl = $this->resolveRedirectUrl();

        return (new NexmoMessage)->content(
            "Hi {$this->user->username}, Welcome to Sowidu.
            Please activate your account using this link: {$redirectUrl}",
        );
    }

    /**
     * Resolve the complete redirect url
     *
     * @return string
     */
    private function resolveRedirectUrl()
    {
        return $this->token->absolute_redirect_url . '&callback=/account/confirmation';
    }
}
