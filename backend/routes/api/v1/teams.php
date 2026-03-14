<?php

use App\Http\Api\Controllers\V1\TeamController;
use App\Http\Api\Controllers\V1\Teams\CancelPendingInvitationController;
use App\Http\Api\Controllers\V1\Teams\CreateTeamController;
use App\Http\Api\Controllers\V1\Teams\GetTeamInvitationsController;
use App\Http\Api\Controllers\V1\Teams\GetTeamMemberProfileController;
use App\Http\Api\Controllers\V1\Teams\GetTeamMembersController;
use App\Http\Api\Controllers\V1\Teams\ManageTeamRolesController;
use App\Http\Api\Controllers\V1\Teams\SearchNewMemberController;
use App\Http\Api\Controllers\V1\Teams\SendTeamInvitationController;
use App\Http\Api\Controllers\V1\Teams\UpdateTeamAvatarController;
use App\Http\Api\Controllers\V1\Teams\UpdateTeamMembersProfileController;
use App\Http\Api\Controllers\V1\Teams\UpdateTeamProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/', [TeamController::class, 'index']);
    Route::post('/', CreateTeamController::class);

    Route::patch('/{team}/profile', UpdateTeamProfileController::class);
    Route::patch('/{team}/change-avatar', UpdateTeamAvatarController::class);

    Route::get('/{team}/members', GetTeamMembersController::class);
    Route::post('/{team}/members', SendTeamInvitationController::class);
    Route::get('/{team}/new-members/search', SearchNewMemberController::class);
    Route::patch('/{team}/members/{member}', UpdateTeamMembersProfileController::class);
    Route::get('/{team}/members/{member}', GetTeamMemberProfileController::class);

    Route::get('/{team}/roles', [ManageTeamRolesController::class, 'index']);
    Route::post('/{team}/roles', [ManageTeamRolesController::class, 'store']);
    Route::get('/{team}/roles/{role}', [ManageTeamRolesController::class, 'show']);
    Route::patch('/{team}/roles/{role}/permissions', [ManageTeamRolesController::class, 'updatePermissions']);
    Route::patch('/{team}/roles/{role}', [ManageTeamRolesController::class, 'update']);

    Route::get('/{team}/invitations', GetTeamInvitationsController::class);
    Route::post('/{team}/invitations', SendTeamInvitationController::class);
    Route::delete('/{team}/invitations/{invitation}', CancelPendingInvitationController::class);
});
