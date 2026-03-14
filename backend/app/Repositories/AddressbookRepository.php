<?php

namespace App\Repositories;

use App\Models\Addressbook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @deprecated use AddressbookService instead
 * @see \App\Services\AddressbookService
 */
class AddressbookRepository
{
    protected $userId;

    protected $teamId;

    protected $filter;

    protected $relations;

    protected $organizationId;

    public function findAddressbookById($id)
    {
        return $this->baseQuery()->withTrashed()->find($id);
    }

    public function findAssociatedAddressbookById($id)
    {
        return $this->associatedBaseQuery()
            ->withTrashed()
            ->find($id);
    }

    public function getAssociatedAddressbookByIds(array $ids)
    {
        return $this->associatedBaseQuery()
            ->whereIn('id', $ids)
            ->withTrashed()
            ->get();
    }

    public function getAssociatedAddressbooks(array $inputs = [], $filter = null): Collection
    {
        return $this->associatedBaseQuery()
            ->filter($inputs, $filter ?? $this->filter)

            ->get();
    }

    public function getTrashedAddressbooks()
    {
        return $this->associatedBaseQuery()->onlyTrashed()->get();
    }

    public function withOrganization(int $organizationId): self
    {
        $this->organizationId = $organizationId;

        return $this;
    }

    public function withRelations(array $relations): self
    {
        $this->relations = $relations;

        return $this;
    }

    public function setFilter($filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function setCurrentUser($user): self
    {
        if ($user instanceof Model) {
            $user = $user->getKey();
        }

        $this->userId = $user;

        return $this;
    }

    public function setCurrentTeam($team): self
    {
        if ($team instanceof Model) {
            $team = $team->getKey();
        }

        $this->teamId = $team;

        return $this;
    }

    public function associatedBaseQuery(): Builder
    {
        return $this->baseQuery()
            ->whereTeamId($this->teamId)
            ->when(is_null($this->teamId), function ($query) {
                $query->whereUserId($this->userId);
            })
            ->when(
                filled($this->relations),
                fn ($query) => $query->with($this->relations),
            )
            ->when(
                filled($this->organizationId),
                fn ($query) => $query->whereHas('organizations', fn ($query) => $query->where('addressbook_organization_id', $this->organizationId)),
            );
    }

    public function baseQuery(): Builder
    {
        return Addressbook::query();
    }
}
