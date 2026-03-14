<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Gate;
use Mockery;
use Modules\WorkLogs\Actions\CreateManualWorkLog;
use Modules\WorkLogs\Enums\PaymentForm;
use Modules\WorkLogs\Enums\WorkLogEvent;
use Modules\WorkLogs\Models\WorkLog;
use Tests\TestCase;

class CreateManualWorkLogTest extends TestCase
{
    use WithFaker;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_a_manual_work_log_for_the_same_user_without_gate_check()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $inputs = [
            'employee' => $user->id,
            'started_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'ended_at' => now()->format('Y-m-d H:i:s'),
            'notes' => null,
            'event' => 'manual',
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(CreateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(3600);
        $action->shouldReceive('convert_to_timezone')->andReturnUsing(fn ($time, $tz) => $time);

        Gate::shouldReceive('forUser')->never();

        $workLog = $action->handle($user, $company, $inputs);

        $this->assertInstanceOf(WorkLog::class, $workLog);
        $this->assertEquals($user->id, $workLog->user_id);
        $this->assertEquals($company->id, $workLog->company_id);
        $this->assertEquals(3600, $workLog->duration_in_seconds);
        $this->assertNull($workLog->notes);
    }

    /** @test */
    public function it_checks_gate_when_creating_for_other_users_and_creates_report_if_notes_exist()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $inputs = [
            'employee' => $user->id + 100,
            'started_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'ended_at' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Worked on project A',
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(CreateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(7200);
        $action->shouldReceive('convert_to_timezone')->andReturnUsing(fn ($time, $tz) => $time);

        Gate::shouldReceive('forUser')->with($user)->once()->andReturnSelf();
        Gate::shouldReceive('authorize')->with('createForOtherEmployees', WorkLog::class)->once();

        $workLog = $action->handle($user, $company, $inputs);

        $this->assertInstanceOf(WorkLog::class, $workLog);
        $this->assertEquals($inputs['employee'], $workLog->user_id);
        $this->assertEquals($company->id, $workLog->company_id);
        $this->assertEquals(7200, $workLog->duration_in_seconds);
        $this->assertEquals('Worked on project A', $workLog->notes);
    }
}
