<?php

namespace App\Services\Order\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait WithOrderStatusStyles
{
    public function orderStatusDialogMessage(string $statusName): string
    {
        return __('order.status.response.message.' . $this->snakeToKebab($statusName));
    }

    public function orderStatusRowColor(string $statusName): ?string
    {
        return Arr::get(config('order.status.color.row'), $statusName);
    }

    public function orderStatusTitle(string $statusName): ?string
    {
        return data_get(__('order.status.title'), $statusName);
    }

    public function orderStatusDescription(string $statusName): ?string
    {
        return data_get(__('order.status.description'), $statusName);
    }

    public function orderStatusDialogColor(string $statusName): ?string
    {
        return Arr::get(config('order.status.color.dialog'), $statusName);
    }

    public function orderStatusIcon(string $statusName): ?string
    {
        return Arr::get(config('order.status.icon'), $statusName);
    }

    protected function snakeToKebab(string $name): string
    {
        return Str::of($name)->lower()
            ->camel()
            ->kebab()
            ->__toString();
    }
}
