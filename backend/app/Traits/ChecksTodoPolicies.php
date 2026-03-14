<?php

namespace App\Traits;

use App\Models\Board;

trait ChecksTodoPolicies
{
    protected function hasValidSubscription(Board $board, $user)
    {
        if (!$board->team_id && $board->hasUser($user)) {
            return true;
        }

        if ($user->belongsToTeam($board->team) && $board->hasUser($user)) {
            return true;
        }

        deny('You are not a members of this board.');
    }

    protected function isBoardOwner(Board $board, $user): bool
    {
        return $board->hasAnyRole($user, ['owner', 'co-owner']);
    }

    protected function membersCanManageLabel(Board $board)
    {
        deny_unless(
            $membersCanManageLabel = $board->settings()->permissions()->allow('members', 'can_manage_label'),
            'Only board admins can manage label.',
        );

        return $membersCanManageLabel;
    }

    protected function membersCanManageGroup(Board $board)
    {
        deny_unless(
            $membersCanManageGroup = $board->settings()->permissions()->allow('members', 'can_manage_group'),
            'Only board admins can create, update or delete a group.',
        );

        return $membersCanManageGroup;
    }

    protected function membersCanManageTask(Board $board)
    {
        deny_unless(
            $membersCanManageTask = $board->settings()->permissions()->allow('members', 'can_manage_task'),
            'Only board admins and task member can update this task.',
        );

        return $membersCanManageTask;
    }

    protected function membersCanManageBoardSubscribers(Board $board)
    {
        deny_unless(
            $membersCanManageSubscriber = $board->settings()->permissions()->allow('members', 'can_manage_subscriber'),
            'Only board admins can add or remove subscriber.',
        );

        return $membersCanManageSubscriber;
    }
}
