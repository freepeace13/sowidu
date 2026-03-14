<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountActivation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Holds the verification instance
     *
     * @var App\Models\Verification
     */
    protected $token;

    /**
     * Holds the user instance
     *
     * @var App\Models\User
     */
    protected $user;

    /**
     * Create a new message instance.
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('Emails.Auth.AccountActivation')->with([
            'username' => $this->user->username,
            'redirect_url' => $this->resolveRedirectUrl(),
        ]);
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
