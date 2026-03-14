<?php

namespace App\Providers;

use App\Contracts\Actions\AddsConversationParticipants;
use App\Contracts\Actions\CreatesConversations;
use App\Contracts\Actions\CreatesMessages;
use App\Contracts\Actions\CreatesTeamInvitations;
use App\Contracts\Actions\CreatesTeams;
use App\Contracts\Actions\DeletesConversations;
use App\Contracts\Actions\DeletesMessages;
use App\Contracts\Actions\RemovesConversationParticipants;
use App\Contracts\Actions\RunsTeamInvitationLinkActions;
use App\Contracts\Actions\SendsTeamInvitations;
use App\Contracts\Actions\SwitchesAccount;
use App\Contracts\Actions\UpdatesConversations;
use App\Contracts\Actions\UpdatesTeamAvatar;
use App\Contracts\Actions\UpdatesTeamMembersProfile;
use App\Contracts\Actions\UpdatesTeamProfile;
use App\Contracts\Actions\UpdatesUserAvatar;
use App\Contracts\Actions\UpdatesUserProfile;
use App\Contracts\Place\PlaceService as PlaceServiceContract;
use App\Http\Api\Actions\Auth\SwitchTeam;
use App\Http\Api\Actions\Chats\AddParticipant;
use App\Http\Api\Actions\Chats\CreateConversation;
use App\Http\Api\Actions\Chats\CreateMessage;
use App\Http\Api\Actions\Chats\DeleteConversation;
use App\Http\Api\Actions\Chats\DeleteMessage;
use App\Http\Api\Actions\Chats\RemoveParticipant;
use App\Http\Api\Actions\Chats\UpdateConversation;
use App\Http\Api\Actions\Teams\CreateTeam;
use App\Http\Api\Actions\Teams\CreateTeamInvitation;
use App\Http\Api\Actions\Teams\SendTeamInvitation;
use App\Http\Api\Actions\Teams\TeamInvitationLinkAction;
use App\Http\Api\Actions\Teams\UpdateTeamAvatar;
use App\Http\Api\Actions\Teams\UpdateTeamMembersProfile;
use App\Http\Api\Actions\Teams\UpdateTeamProfile;
use App\Http\Api\Actions\User\UpdateUserAvatar;
use App\Http\Api\Actions\User\UpdateUserProfile;
use App\Services\PlaceService;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(SwitchesAccount::class, SwitchTeam::class);
        $this->app->bind(CreatesTeamInvitations::class, CreateTeamInvitation::class);
        $this->app->bind(CreatesTeams::class, CreateTeam::class);
        $this->app->bind(RunsTeamInvitationLinkActions::class, TeamInvitationLinkAction::class);
        $this->app->bind(SendsTeamInvitations::class, SendTeamInvitation::class);
        $this->app->bind(UpdatesUserAvatar::class, UpdateUserAvatar::class);
        $this->app->bind(UpdatesUserProfile::class, UpdateUserProfile::class);
        $this->app->bind(UpdatesTeamAvatar::class, UpdateTeamAvatar::class);
        $this->app->bind(UpdatesTeamProfile::class, UpdateTeamProfile::class);
        $this->app->bind(UpdatesTeamMembersProfile::class, UpdateTeamMembersProfile::class);

        $this->app->bind(CreatesConversations::class, CreateConversation::class);
        $this->app->bind(CreatesMessages::class, CreateMessage::class);
        $this->app->bind(DeletesConversations::class, DeleteConversation::class);
        $this->app->bind(DeletesMessages::class, DeleteMessage::class);
        $this->app->bind(UpdatesConversations::class, UpdateConversation::class);
        $this->app->bind(AddsConversationParticipants::class, AddParticipant::class);
        $this->app->bind(RemovesConversationParticipants::class, RemoveParticipant::class);

        $this->app->bind(PlaceServiceContract::class, PlaceService::class);
    }

    public function boot()
    {
        //
    }
}
