<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Modules\WorkLogs\Actions\BaseManualWorkLog;
use Modules\WorkLogs\Enums\PaymentForm;
use Modules\WorkLogs\Enums\WorkLogEvent;
use Tests\TestCase;

class BaseManualWorkLogTest extends TestCase
{
    private BaseManualWorkLog $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new BaseManualWorkLog;
    }

    /** @test */
    public function it_validates_valid_input_successfully(): void
    {
        $user = User::factory()->create();

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => 'Test work log',
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $validated = $this->action->validate($inputs);

        $this->assertEquals($inputs['date_start'], $validated['date_start']);
        $this->assertEquals($inputs['date_end'], $validated['date_end']);
        $this->assertEquals($inputs['time_start'], $validated['time_start']);
        $this->assertEquals($inputs['time_end'], $validated['time_end']);
        $this->assertEquals($inputs['started_at'], $validated['started_at']);
        $this->assertEquals($inputs['ended_at'], $validated['ended_at']);
        $this->assertEquals($inputs['notes'], $validated['notes']);
        $this->assertEquals($inputs['event'], $validated['event']);
        $this->assertEquals($inputs['payment_form'], $validated['payment_form']);
        $this->assertEquals($inputs['employee'], $validated['employee']);
        $this->assertEquals($inputs['timezone'], $validated['timezone']);
    }

    /** @test */
    public function it_validates_with_null_notes(): void
    {
        $user = User::factory()->create();

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::SICK->value,
            'payment_form' => null,
            'employee' => $user->id,
            'timezone' => 'Europe/Berlin',
        ];

        $validated = $this->action->validate($inputs);

        $this->assertNull($validated['notes']);
        $this->assertNull($validated['payment_form']);
    }

    /** @test */
    public function it_fails_validation_when_date_start_is_missing(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        $inputs = [
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $this->action->validate($inputs);
    }

    /** @test */
    public function it_fails_validation_when_date_end_is_before_date_start(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        $inputs = [
            'date_start' => '2025-01-16',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $this->action->validate($inputs);
    }

    /** @test */
    public function it_fails_validation_when_ended_at_is_not_after_started_at(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '17:00',
            'time_end' => '09:00',
            'started_at' => '2025-01-15 17:00',
            'ended_at' => '2025-01-15 09:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $this->action->validate($inputs);
    }

    /** @test */
    public function it_fails_validation_when_employee_does_not_exist(): void
    {
        $this->expectException(ValidationException::class);

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'employee' => 999999,
            'timezone' => 'UTC',
        ];

        $this->action->validate($inputs);
    }

    /** @test */
    public function it_fails_validation_when_event_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => 'invalid_event',
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $this->action->validate($inputs);
    }

    /** @test */
    public function it_fails_validation_when_timezone_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'employee' => $user->id,
            'timezone' => 'Invalid/Timezone',
        ];

        $this->action->validate($inputs);
    }

    /** @test */
    public function it_fails_validation_when_payment_form_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        $inputs = [
            'date_start' => '2025-01-15',
            'date_end' => '2025-01-15',
            'time_start' => '09:00',
            'time_end' => '17:00',
            'started_at' => '2025-01-15 09:00',
            'ended_at' => '2025-01-15 17:00',
            'notes' => null,
            'event' => WorkLogEvent::HOLIDAY->value,
            'payment_form' => 999,
            'employee' => $user->id,
            'timezone' => 'UTC',
        ];

        $this->action->validate($inputs);
    }

    /** @test */
    public function it_calculates_duration_in_seconds_for_one_hour(): void
    {
        $startedAt = '2025-01-15 09:00:00';
        $endedAt = '2025-01-15 10:00:00';

        $duration = $this->action->getDurationInSeconds($startedAt, $endedAt);

        $this->assertEquals(3600, $duration);
    }

    /** @test */
    public function it_calculates_duration_in_seconds_for_eight_hours(): void
    {
        $startedAt = '2025-01-15 09:00:00';
        $endedAt = '2025-01-15 17:00:00';

        $duration = $this->action->getDurationInSeconds($startedAt, $endedAt);

        $this->assertEquals(28800, $duration);
    }

    /** @test */
    public function it_calculates_duration_in_seconds_spanning_multiple_days(): void
    {
        $startedAt = '2025-01-15 22:00:00';
        $endedAt = '2025-01-16 06:00:00';

        $duration = $this->action->getDurationInSeconds($startedAt, $endedAt);

        $this->assertEquals(28800, $duration);
    }

    /** @test */
    public function it_calculates_duration_in_seconds_for_minutes(): void
    {
        $startedAt = '2025-01-15 09:00:00';
        $endedAt = '2025-01-15 09:30:00';

        $duration = $this->action->getDurationInSeconds($startedAt, $endedAt);

        $this->assertEquals(1800, $duration);
    }

    /** @test */
    public function it_calculates_zero_duration_for_same_time(): void
    {
        $startedAt = '2025-01-15 09:00:00';
        $endedAt = '2025-01-15 09:00:00';

        $duration = $this->action->getDurationInSeconds($startedAt, $endedAt);

        $this->assertEquals(0, $duration);
    }
}
