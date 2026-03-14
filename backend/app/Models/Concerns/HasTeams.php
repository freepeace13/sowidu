<?php

namespace App\Models\Concerns;

use App\Models\Company as Team;
use App\Models\Employee as Membership;
use App\Models\Permission;
use Packages\Tenancy\TeamStoreFactory;

trait HasTeams
{
    public function isCurrentTeam($team)
    {
        return $this->currentTeam()
            ?->id === $team->id;
    }

    public function currentTeam()
    {
        $teamId = TeamStoreFactory::create()->getTeamId($this);

        return $this->teams()
            ->find($teamId);
    }

    public function switchTeam($team)
    {
        if (!is_null($team) && !$this->belongsToTeam($team)) {
            return false;
        }

        TeamStoreFactory::create()->setTeamId($this, $team?->id);

        return true;
    }

    protected function resolveTeam($team): ?Team
    {
        if (is_int($team) || is_string($team)) {
            return Team::find($team);
        }

        return $team;
    }

    public function belongsToTeam($team)
    {
        $team = $this->resolveTeam($team);

        if (is_null($team)) {
            return false;
        }

        return $this->ownsTeam($team) || $this->teams->contains(function ($t) use ($team) {
            return $t->id === $team->id;
        });
    }

    public function ownsTeam($team)
    {
        if (is_null($team)) {
            return false;
        }

        return $this->id === $team->{$this->getForeignKey()};
    }

    public function ownsCompany($company): bool
    {
        return $this->ownsTeam($company);
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'employees');
    }

    public function membership()
    {
        return $this->hasMany(Membership::class);
    }

    public function teamMembership($team)
    {
        $team = $this->resolveTeam($team);

        if (!$this->belongsToTeam($team)) {
            return null;
        }

        return $this->membership()
            ->where('company_id', $team->id)
            ->first();
    }

    public function teamRoles($team)
    {
        if (is_null($team) || !$this->belongsToTeam($team)) {
            return null;
        }

        if ($this->ownsTeam($team)) {
            return ['*'];
        }

        $membership = $this->membership()
            ->where('company_id', $team->id)
            ->first();

        return $team->roles
            ->filter(function ($role) use ($membership) {
                return $membership->hasRole($role->id);
            })
            ->pluck('name')
            ->all();
    }

    public function hasTeamRole($team, string $role)
    {
        if ($this->ownsTeam($team)) {
            return true;
        }

        return $this->belongsToTeam($team) && in_array($role, $this->teamRoles($team));
    }

    public function teamPermissions($team)
    {
        if (is_null($team) || !$this->belongsToTeam($team)) {
            return null;
        }

        if ($this->ownsTeam($team)) {
            return Permission::all()->pluck('name')
                ->all();
        }

        $membership = $this->membership()
            ->where('company_id', $team->id)
            ->first();

        return Permission::all()
            ->filter(function ($item) use ($membership) {
                return $membership->checkPermissionTo($item);
            })
            ->pluck('name')
            ->all();
    }

    public function hasTeamPermission($team, string $permission)
    {
        $team = $this->resolveTeam($team);

        if ($this->ownsTeam($team)) {
            return true;
        }

        if (!$this->belongsToTeam($team)) {
            return false;
        }

        return in_array($permission, $this->teamPermissions($team));
    }
}
