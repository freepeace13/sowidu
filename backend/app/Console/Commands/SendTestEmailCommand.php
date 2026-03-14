<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email} {--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test email.';

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
     * @return int
     */
    public function handle()
    {
        $to = $this->argument('email');
        $queue = $this->option('queue');

        $mailable = function () use ($to) {
            Mail::raw('This is test email, please ignore.', function ($message) use ($to) {
                $message
                    ->from('register@sowidu.de')
                    ->to($to)
                    ->subject('Test email from Sowidu!');
            });
        };

        if (is_null($queue)) {
            $mailable();
        } else {
            dispatch($mailable)->onQueue($queue);
        }

        return self::SUCCESS;
    }
}
