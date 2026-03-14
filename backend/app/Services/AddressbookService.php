<?php

namespace App\Services;

use App\Models\Addressbook;
use App\Models\Company as Organization;
use App\Models\User as Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AddressbookService
{
    protected $user;

    protected $team;

    protected $query;

    public function __construct($user, $team = null)
    {
        $this->user = $user;
        $this->team = $team;

        $this->query = $this->newQuery();
    }

    public function isOwned(Addressbook $addressbook)
    {
        if (!$this->team) {
            return $addressbook->isOwnedByUser($this->user);
        }

        return $addressbook->isOwnedByTeam($this->team?->id ?? $this->team);
    }

    public function getSourceIds()
    {
        return $this->pluck('model_id')
            ->filter();
    }

    public function getPeopleIds(): Collection
    {
        return $this->people()
            ->select('model_id')
            ->getSourceIds();
    }

    public function getOrganizationIds(): Collection
    {
        return $this->organizations()
            ->select('model_id')
            ->getSourceIds();
    }

    public function doesntHaveMembers($ids)
    {
        return $this->whereDoesntHave('organizationMembers', function ($query) use ($ids) {
            $query->whereIn('id', Arr::wrap($ids));
        });
    }

    public function doesHaveMembers($ids)
    {
        return $this->whereHas('organizationMembers', function ($query) use ($ids) {
            $query->whereIn('id', Arr::wrap($ids));
        });
    }

    public function findBySourceId($id)
    {
        return $this->whereModelId($id)
            ->first();
    }

    public function findPersonById($id)
    {
        return $this->people()
            ->findBySourceId($id);
    }

    public function findOrganizationById($id)
    {
        return $this->organizations()
            ->findBySourceId($id);
    }

    public function people(): self
    {
        return $this->where(function (Builder $query) {
            $query->where('model_type', (new Person)->getMorphClass())
                ->orWhere(
                    fn (Builder $query) => $query
                        ->whereNull('model_id')
                        ->where('foreign_type', Addressbook::FOREIGN_PERSON),
                );
        });
    }

    public function organizations()
    {
        return $this->where(function (Builder $query) {
            $query->where('model_type', (new Organization)->getMorphClass())
                ->orWhere(
                    fn (Builder $query) => $query
                        ->whereNull('model_id')
                        ->where('foreign_type', Addressbook::FOREIGN_ORGANIZATION),
                );
        });
    }

    public function onlyType($model)
    {
        if ($model === 'users') {
            return $this->people();
        }

        return $this->organizations();
    }

    public function matchesText($text)
    {
        return $this->when(
            filled($text),
            function ($query) use ($text) {
                $query->search($text);
            },
        );
    }

    public function withFilter($filter, array $input = [])
    {
        return $this->filter($input, $filter);
    }

    public function newInstance()
    {
        return static::make($this->user, $this->team);
    }

    public static function make($user, $team = null)
    {
        return new static($user, $team);
    }

    public function setQuery(Builder $query)
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function newQuery()
    {
        return Addressbook::query()
            ->whereTeamId($this->team?->id)
            ->when(is_null($this->team?->id), function ($query) {
                $query->whereUserId($this->user->id);
            });
    }

    /**
     * @return \App\Models\Addressbook|static
     */
    public function __call($method, $parameters)
    {
        $result = $this->query->{$method}(...$parameters);

        if ($result instanceof Builder) {
            return $this;
        }

        $this->setQuery($this->newQuery());

        return $result;
    }

    public function filters(array $filters = [])
    {
        $this->query
            ->when(
                $q = $filters['q'] ?? null,
                fn ($query) => $query->search($q),
            )
            ->when(
                $id = $filters['id'] ?? null,
                fn ($query) => $query->where('id', $id)->orWhere('team_id', $id),
            );

        return $this;
    }
}
