<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Gate;
use Mockery;
use Modules\WorkLogs\Actions\UpdateManualWorkLog;
use Modules\WorkLogs\Enums\PaymentForm;
use Modules\WorkLogs\Enums\WorkLogEvent;
use Modules\WorkLogs\Models\WorkLog;
use Tests\TestCase;

class UpdateManualWorkLogTest extends TestCase
{
    use WithFaker;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_updates_a_work_log_when_user_is_authorized(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '10:00',
            'time_end' => '18:00',
            'started_at' => '2025-01-15 10:00',
            'ended_at' => '2025-01-15 18:00',
            'notes' => 'Updated work log',
            'event' => WorkLogEvent::SICK->value,
            'payment_form' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(UpdateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(28800);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertInstanceOf(WorkLog::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Updated work log', $result->notes);
        $this->assertEquals(WorkLogEvent::SICK->value, $result->event);
        $this->assertEquals(28800, $result->duration_in_seconds);
    }

    /** @test */
    public function it_throws_exception_when_user_is_not_authorized_to_update(): void
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '10:00',
            'time_end' => '18:00',
            'started_at' => '2025-01-15 10:00',
            'ended_at' => '2025-01-15 18:00',
            'notes' => 'Updated work log',
            'event' => WorkLogEvent::SICK->value,
            'payment_form' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = new UpdateManualWorkLog;
        $action->handle($user, $company, $workLog, $inputs);
    }

    /** @test */
    public function it_creates_a_report_when_notes_are_provided_and_no_report_exists(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'notes' => null,
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => 'New notes added',
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(UpdateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(28800);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertInstanceOf(WorkLog::class, $result);
        $this->assertEquals('New notes added', $result->notes);
        $this->assertDatabaseHas('activity_log_reports', [
            'work_log_id' => $workLog->id,
            'note' => 'New notes added',
        ]);
    }

    /** @test */
    public function it_updates_existing_report_when_notes_change(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'notes' => 'Original notes',
        ]);

        $workLog->reports()->create([
            'note' => 'Original notes',
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => 'Updated notes',
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(UpdateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(28800);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertInstanceOf(WorkLog::class, $result);
        $this->assertEquals('Updated notes', $result->notes);
        $this->assertDatabaseHas('activity_log_reports', [
            'work_log_id' => $workLog->id,
            'note' => 'Updated notes',
        ]);
        $this->assertEquals(1, $workLog->reports()->count());
    }

    /** @test */
    public function it_can_update_the_assigned_employee(): void
    {
        $author = User::factory()->create();
        $originalEmployee = User::factory()->create();
        $newEmployee = User::factory()->create();
        $company = Company::factory()->create();

        $workLog = WorkLog::factory()->create([
            'user_id' => $originalEmployee->id,
            'author_id' => $author->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'employee' => $newEmployee->id,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(UpdateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(28800);

        Gate::shouldReceive('forUser')
            ->with($author)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $result = $action->handle($author, $company, $workLog, $inputs);

        $this->assertEquals($newEmployee->id, $result->user_id);
        $this->assertEquals($author->id, $result->author_id);
    }

    /** @test */
    public function it_updates_the_author_to_current_user(): void
    {
        $originalAuthor = User::factory()->create();
        $newAuthor = User::factory()->create();
        $employee = User::factory()->create();
        $company = Company::factory()->create();

        $workLog = WorkLog::factory()->create([
            'user_id' => $employee->id,
            'author_id' => $originalAuthor->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'employee' => $employee->id,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(UpdateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(28800);

        Gate::shouldReceive('forUser')
            ->with($newAuthor)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $result = $action->handle($newAuthor, $company, $workLog, $inputs);

        $this->assertEquals($newAuthor->id, $result->author_id);
    }

    /** @test */
    public function it_updates_duration_in_seconds_based_on_time_range(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'duration_in_seconds' => 3600,
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '08:00',
            'time_end' => '20:00',
            'started_at' => '2025-01-15 08:00',
            'ended_at' => '2025-01-15 20:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(UpdateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(43200);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertEquals(43200, $result->duration_in_seconds);
    }

    /** @test */
    public function it_updates_payment_form(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(UpdateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(28800);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertEquals(PaymentForm::PAID_VIA_INCOMING_INVOICE->value, $result->payment_form->value);
    }

    /** @test */
    public function it_updates_event_type(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'event' => WorkLogEvent::HOLIDAY->value,
        ]);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::SICK->value,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $action = Mockery::mock(UpdateManualWorkLog::class)->makePartial();
        $action->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('validate')->andReturn($inputs);
        $action->shouldReceive('getDurationInSeconds')->andReturn(28800);

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertEquals(WorkLogEvent::SICK->value, $result->event);
    }
}
