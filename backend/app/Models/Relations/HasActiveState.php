<?php

namespace App\Models\Relations;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasActiveState
{
    /**
     * Set the object status to online
     *
     * @return void
     */
    public function setOnline()
    {
        return $this->setStatus('online');
    }

    /**
     * Set the object status to offline
     *
     * @return self
     */
    public function setOffline()
    {
        return $this->setStatus('offline');
    }

    /**
     * Set the object status to idle
     *
     * @return self
     */
    public function setIdle()
    {
        return $this->setStatus('away');
    }

    /**
     * Set the object attribute status according to param
     *
     * @param  string  $status
     * @return self
     */
    private function setStatus($status)
    {
        $this->active_status = $status;

        return $this;
    }

    /**
     * Scope a query that online include contactables that authenticatable is online
     *
     * @author goper
     */
    public function scopeOnline(Builder $query): Builder
    {
        if ($query->getModel() instanceof Employee) {
            return $query->whereHas('user', function (Builder $query) {
                $query->online();
            });
        }

        return $query->where('active_status', 'online');
    }

    /**
     * Scope a query that online include contactables that authenticatable is online
     *
     * @author goper
     */
    public function scopeOffline(Builder $query): Builder
    {
        if ($query->getModel() instanceof Employee) {
            return $query->whereHas('user', function (Builder $query) {
                $query->offline();
            });
        }

        return $query->where('active_status', 'offline');
    }

    /**
     * Determine if the contactable is currently online
     *
     * @return bool
     */
    public function isOnline()
    {
        return Str::is($this->active_status, 'online');
    }
}
