<?php

namespace App\Events\Organization;

use App\Models\Company as Team;
use App\Models\Employee as TeamMember;
use App\Models\EmployeeRate as MemberRate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MembersRateUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Team $team,
        public TeamMember $member,
        public MemberRate $rate,
    ) {}
}
