<?php

use App\Http\Api\Controllers\V1\User\TeamController;
use App\Http\Api\Controllers\V1\User\TeamInvitationController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'private_user',
])->group(function () {
    /**
     * @route /api/v1/user/teams
     */
    Route::controller(TeamController::class)
        ->prefix('teams')
        ->group(function () {
            Route::get('', 'index');
            Route::delete('{team}/leave', 'leave');
        });

    /**
     * @route /api/v1/user/teams/invitations
     */
    Route::controller(TeamInvitationController::class)
        ->prefix('teams/invitations')
        ->group(function () {
            Route::get('', 'index');
            Route::post('{companyInvitation}/accept', 'accept');
            Route::delete('{companyInvitation}/decline', 'decline');
        });

});
