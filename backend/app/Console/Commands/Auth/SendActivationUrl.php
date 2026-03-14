<?php

namespace App\Console\Commands\Auth;

use App\Models\User;
use App\Notifications\Auth\SendActivationLink;
use App\Traits\CreatesNotifiableRecipient;
use App\Values\Verifiable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class SendActivationUrl extends Command
{
    use CreatesNotifiableRecipient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:send-activation-url {user} {--via=mail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification for account activation to the given user.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $via = $this->option('via');

        $recipient = $this->argument('user');

        if (!in_array($via, ['email', 'mobile'])) {
            $via = is_email($recipient) ? 'email' : 'mobile';
        }

        if (is_null($user = User::find($recipient))) {
            $user = User::where($via, $recipient)->first();
        }

        if (!is_null($user) && !is_null($user->{$via})) {
            $notification = new SendActivationLink(
                $verifiable = new Verifiable(
                    $this->createRecipient($user->{$via}),
                    URL::signedRoute('auth.activate', ['user' => $user->id]),
                ),
            );

            Notification::route(
                $verifiable->recipient->getDriver(), $verifiable->recipient->getValue(),
            )->notify($notification);

            $this->info('Activation link sent to ' . $user->{$via} . ' via ' . $via);
        } else {
            $this->error('User\'s ' . $via . ' value of ' . $this->argument('user') . ' not found.');
        }

        return 0;
    }
}
