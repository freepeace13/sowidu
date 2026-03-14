<?php

namespace App\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class PushingResourcesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Illuminate\Contracts\Auth\Access\Authorizable
     */
    protected $authorizable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Authorizable $authorizable)
    {
        $this->authorizable = $authorizable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    abstract public function handle();
}
