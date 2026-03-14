<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipInvitationLink extends Mailable
{
    use Queueable, SerializesModels;

    protected $sender;

    protected $email;

    protected $url;

    protected $note;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Model $sender, string $email, string $url, $note = null)
    {
        $this->sender = $sender;
        $this->url = $url;
        $this->email = $email;
        $this->note = $note;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        [$nickname] = explode('@', $this->email);

        return $this->markdown('emails.membership-link', [
            'nickname' => $nickname,
            'url' => $this->url,
            'note' => $this->note,
            'sender' => $this->sender->name,
        ]);
    }
}
