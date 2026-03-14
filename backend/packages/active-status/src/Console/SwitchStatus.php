<?php

namespace Packages\ActiveStatus\Console;

use Illuminate\Console\Command;

class SwitchStatus extends Command
{
    protected $signature = 'active-status:switch {models?*}';

    protected $description = 'Run status change';

    protected $beforeAway;

    protected $beforeOffline;

    protected $executedAt;

    public function handle(): int
    {
        $models = $this->argument('models');
        $models = blank($models) ? config('activestatus.scheduler.arguments', []) : $models;

        $this->executedAt = now();
        $this->beforeAway = config('activestatus.before_away');
        $this->beforeOffline = config('activestatus.before_offline');

        foreach ($models as $model) {
            [$offlineUsers, $onlineUsers, $awayUsers] = [
                $this->getQualifiedOfflineUsers($model),
                $this->getQualifiedOnlineUsers($model),
                $this->getQualifiedAwayUsers($model),
            ];

            $offlineUsers->each->switchStatus('offline');
            $onlineUsers->each->switchStatus('online');
            $awayUsers->each->switchStatus('away');
        }

        return 0;
    }

    protected function getQualifiedOfflineUsers($model)
    {
        $now = $this->executedAt->copy();

        $seconds = $this->beforeAway + $this->beforeOffline;

        return $model::query()
            ->whereNotNull('last_seen_at')
            ->where(function ($query) {
                $query->where('active_status', '!=', 'offline')
                    ->orWhereNull('active_status');
            })
            ->where('last_seen_at', '<=', $now->subSeconds($seconds))
            ->get();
    }

    protected function getQualifiedAwayUsers($model)
    {
        $now = $this->executedAt->copy();

        $to = $now->subSeconds($this->beforeAway);
        $from = $to->copy()->subSeconds($this->beforeOffline);

        return $model::query()
            ->whereNotNull('last_seen_at')
            ->where(function ($query) {
                $query->where('active_status', '!=', 'away')
                    ->orWhereNull('active_status');
            })
            ->whereBetween('last_seen_at', [$from, $to])
            ->get();
    }

    protected function getQualifiedOnlineUsers($model)
    {
        $now = $this->executedAt->copy();

        return $model::query()
            ->whereNotNull('last_seen_at')
            ->where(function ($query) {
                $query->where('active_status', '!=', 'online')
                    ->orWhereNull('active_status');
            })
            ->where('last_seen_at', '>', $now->subSeconds($this->beforeAway))
            ->get();
    }
}
