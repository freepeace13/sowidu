<?php

namespace App\Repositories\ActivityLog\Models;

use App\Enums\OrderEvent;
use App\Models\Order;
use App\Models\User;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Models\Activity;

class OrderLog
{
    protected ActivityLogger $logger;

    protected string $log;

    protected array $properties = [];

    public function __construct(protected Order $order)
    {
        $this->log = "order.{$order->id}";

        $this->properties = [
            'company_id' => $order->contractor->id,
        ];

        $this->logger = activity($this->log)
            ->on($this->order)
            ->withProperties($this->properties);
    }

    public function setCauser(User $user): self
    {
        $this->logger->causedBy($user);

        return $this;
    }

    public function withProperties(array $properties = []): self
    {
        $this->properties = array_merge($this->properties, $properties);

        $this->logger->withProperties($this->properties);

        return $this;
    }

    public function created(): Activity
    {
        return $this->logger
            ->event(OrderEvent::CREATED())
            ->log('order.timelines.created');
    }

    public function updateStatus(OrderEvent $event, ?string $description = null): Activity
    {
        // Guess description from the `$event` name if not given
        $description = $description ?? "order.timelines.{$event->value}";

        return $this->logger
            ->event($event->value)
            ->log($description);
    }

    public function delete()
    {
        Activity::inLog($this->log)->delete(); // Remove all logs on this `Order`
    }
}
