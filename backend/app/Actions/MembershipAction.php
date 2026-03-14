<?php

namespace App\Actions;

use App\Events\CompanyInvitationAccepted;
use App\Models\CompanyInvitation as Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class MembershipAction
{
    public function execute(array $data, $company)
    {
        return function () use ($company, $data) {
            $this->registerUser($data['email']);
            $this->createInvitation($company, $data['email'], $data['role'], $data['note']);

            return redirect()->route('desktop');
        };
    }

    protected function registerUser(string $email)
    {
        $user = User::create(['email' => $email]);
        $user->markEmailAsVerified();
        Auth::guard('web')->login($user);
    }

    protected function createInvitation($company, $email, $role, $note = null)
    {
        Invitation::whereEmail($email)->delete();
        Invitation::create([
            'email' => $email,
            'company_id' => $company,
            'note' => $note,
            'role' => $role,
        ]);
    }

    public function accept(User $user)
    {
        $invitation = Invitation::whereEmail($user->email)->firstOrFail();
        $invitation->accept();
        event(new CompanyInvitationAccepted($invitation));
        event(new Registered($user));
    }
}
