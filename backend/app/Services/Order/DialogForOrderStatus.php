<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Services\Order\Traits\WithOrderStatusStyles;

class DialogForOrderStatus
{
    use WithOrderStatusStyles;

    public function __construct(protected OrderStatus $orderStatus) {}

    public static function make(OrderStatus $orderStatus)
    {
        return new static($orderStatus);
    }

    public function build(): array
    {
        $toOrderStatus = $this->orderStatus;
        $orderStatusName = $toOrderStatus->name;

        $dialog = [
            'color' => $this->orderStatusDialogColor($orderStatusName),
            'icon' => $this->orderStatusIcon($orderStatusName),
            'message' => $this->orderStatusDialogMessage($orderStatusName),
        ];

        $specifics = [];

        if ($toOrderStatus === OrderStatus::COMMISSIONED) {
            $specifics = [
                'accept_button_label' => __('buttons.confirm'),
                'cancelable' => true,
            ];
        }

        if ($toOrderStatus === OrderStatus::WAITING_FOR_CLIENT_CONFIRMATION) {
            $specifics = [
                'accept_button_label' => null,
                'cancelable' => true,
                'force_confirm' => true,
            ];
        }

        if ($toOrderStatus === OrderStatus::WAITING_FOR_CONTRACTOR_CONFIRMATION) {
            $specifics = [
                'accept_button_label' => null,
                'cancelable' => true,
            ];
        }

        if ($toOrderStatus === OrderStatus::CONTRACTOR_WAITING_FOR_CLIENT_CONFIRMATION) {
            $specifics = [
                'accept_button_label' => null,
                'cancelable' => true,
                'force_confirm' => true,
            ];
        }

        if ($toOrderStatus === OrderStatus::STARTED) {
            $specifics = [
                'accept_button_label' => __('order.buttons.start'),
                'cancelable' => true,
            ];
        }

        if ($toOrderStatus === OrderStatus::WAITING_FOR_CONTRACTOR_TO_START) {
            $specifics = [
                'accept_button_label' => null,
                'cancelable' => false,
            ];
        }

        if ($toOrderStatus === OrderStatus::ONGOING) {
            $specifics = [
                'accept_button_label' => null,
                'cancelable' => false,
            ];
        }

        if ($toOrderStatus === OrderStatus::READY_FOR_REVIEW) {
            $specifics = [
                'accept_button_label' => __('order.buttons.ready-for-review'),
                'cancelable' => false,
            ];
        }

        if ($toOrderStatus === OrderStatus::WAITING_FOR_CLIENT_REVIEW) {
            $specifics = [
                'accept_button_label' => null,
                'cancelable' => false,
                'force_confirm' => true,
                'force_confirm_button_label' => trans('order.buttons.ful-fill'),
            ];
        }

        if ($toOrderStatus === OrderStatus::WORK_ON_REVISIONS) {
            $specifics = [
                'accept_button_label' => __('order.buttons.work-on-revisions'),
                'cancelable' => false,
            ];
        }

        if ($toOrderStatus === OrderStatus::FINISHED) {
            $specifics = [
                'accept_button_label' => __('order.buttons.finished'),
                'cancelable' => false,
                'rejectable' => true,
            ];
        }

        if ($toOrderStatus === OrderStatus::CANCELLED || $toOrderStatus === OrderStatus::REJECT) {
            $specifics = [
                'accept_button_label' => false,
                'cancelable' => false,
            ];
        }

        if ($toOrderStatus === OrderStatus::FULFILLED) {
            $specifics = [];
        }

        return array_merge($dialog, $specifics);
    }
}
