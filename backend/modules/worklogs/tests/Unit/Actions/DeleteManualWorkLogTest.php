<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\WorkLogs\Actions\DeleteManualWorkLog;
use Modules\WorkLogs\Models\WorkLog;
use Tests\TestCase;

class DeleteManualWorkLogTest extends TestCase
{
    /** @test */
    public function it_deletes_a_work_log_when_user_is_authorized()
    {
        $user = User::factory()->create();
        $workLog = WorkLog::factory()->create(['user_id' => $user->id]);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('delete', $workLog)
            ->once();

        $action = new DeleteManualWorkLog;
        $action->handle($user, $workLog);

        $this->assertDatabaseMissing('work_logs', [
            'id' => $workLog->id,
        ]);
    }

    /** @test */
    public function it_throws_exception_if_user_is_not_authorized()
    {
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $user = User::factory()->create();
        $workLog = WorkLog::factory()->create(['user_id' => $user->id]);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('delete', $workLog)
            ->once()
            ->andThrow(\Illuminate\Auth\Access\AuthorizationException::class);

        $action = new DeleteManualWorkLog;
        $action->handle($user, $workLog);
    }
}
