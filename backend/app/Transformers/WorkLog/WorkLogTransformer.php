<?php

namespace App\Transformers\WorkLog;

use App\Enums\PaymentForm;
use App\Enums\WorkLogEvent;
use App\Models\Order;
use App\Models\User;
use App\Transformers\ActivityLogReportTransformer;
use App\Transformers\PlaceTransformer;
use App\Transformers\Transformer;
use App\Transformers\UserTransformer;
use Illuminate\Support\Str;

/**
 * @property \App\Models\WorkLog $resource
 */
class WorkLogTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'started_at' => $this->resource->started_at,
            'ended_at' => $this->resource->ended_at,
            'duration_in_seconds' => $this->resource->duration_in_seconds ?? 0,
            'is_currently_working' => $this->resource->isCurrentlyWorking(),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'duration' => $this->resource->duration_for_human,
            'is_shared' => $this->resource->is_shared,
            'event' => $this->resource->event,
            'payment_form' => $this->resource->payment_form
                ? collect(PaymentForm::options())->firstWhere('value', $this->resource->payment_form->value)
                : null,
            'document_number' => $this->resource->document_number,
            'document_date' => $this->resource->document_date,
        ];
    }

    public function withEventMeta()
    {
        $event = WorkLogEvent::tryFrom($this->resource->event);

        return $this->state(fn ($attr) => [
            'event_meta' => [
                'color' => $event->color(),
                'name' => Str::of($event?->name ?? '')->replace('_', ' ')
                    ->title(),
            ],
        ]);
    }

    public function withCauser(User $causer)
    {
        return $this->state(fn ($attr) => [
            'causer' => (new UserTransformer($causer))->resolve(),
        ]);
    }

    public function withOrder(?Order $order)
    {
        $orderAttr = null;
        if ($order) {
            $orderAttr = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'delivery_address' => (new PlaceTransformer($order->deliveryAddress))
                    ->withId()
                    ->resolve(),
            ];
        }

        return $this->state(fn ($attr) => [
            'order' => $orderAttr,
        ]);
    }

    public function withReports($reports)
    {
        return $this->state(fn ($attr) => [
            'reports' => $reports->map(
                fn ($report) => (new ActivityLogReportTransformer($report))->resolve(),
            ),
        ]);
    }

    public function withCanStopTime(User $auth)
    {
        return $this->state(fn () => [
            'can_stop_time' => $this->resource->causer->is($auth),
        ]);
    }

    public function withEmployeeRole()
    {
        $company = $this->resource->company;
        $role = $company->getEmployee($this->resource->causer)
            ->role;

        return $this->state(fn ($attributes) => [
            'causer' => array_merge($attributes['causer'], [
                'role' => $role,
            ]),
        ]);
    }

    public function withWorkingStatus()
    {
        return $this->state(fn () => [
            'is_currently_working' => $this->resource->isCurrentlyWorking(),
        ]);
    }

    public function withActions(bool $canEdit, bool $canDelete)
    {
        return $this->state(fn () => [
            'can_edit' => $canEdit,
            'can_delete' => $canDelete,
        ]);
    }

    public function withCanChangeOrder(bool $canChangeOrder): self
    {
        return $this->state(fn () => [
            'can_change_order' => $canChangeOrder,
        ]);
    }

    public function withIsInvoiced(bool $isInvoiced): self
    {
        return $this->state(fn () => [
            'is_paid' => $this->resource->is_paid,
            'is_invoiced' => $isInvoiced,
        ]);
    }
}
