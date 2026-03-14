<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Tests\Unit\Actions;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Modules\WorkLogs\Actions\UpdateWorkLogPaymentForm;
use Modules\WorkLogs\Enums\PaymentForm;
use Modules\WorkLogs\Models\WorkLog;
use Tests\TestCase;

class UpdateWorkLogPaymentFormTest extends TestCase
{
    /** @test */
    public function it_updates_payment_form_value_when_user_is_authorized(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
        ]);

        $inputs = [
            'payment_form' => [
                'value' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
                'document_number' => null,
                'document_date' => null,
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertInstanceOf(WorkLog::class, $result);
        $this->assertEquals(PaymentForm::PAID_VIA_INCOMING_INVOICE->value, $result->payment_form->value);
    }

    /** @test */
    public function it_throws_exception_when_user_is_not_authorized(): void
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'payment_form' => [
                'value' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once()
            ->andThrow(AuthorizationException::class);

        $action = new UpdateWorkLogPaymentForm;
        $action->handle($user, $company, $workLog, $inputs);
    }

    /** @test */
    public function it_updates_payment_form_with_document_number(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'payment_form' => [
                'value' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
                'document_number' => 'INV-2025-001',
                'document_date' => null,
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertEquals('INV-2025-001', $result->document_number);
    }

    /** @test */
    public function it_updates_payment_form_with_document_date(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'payment_form' => [
                'value' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
                'document_number' => null,
                'document_date' => '2025-01-15',
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertEquals('2025-01-15', $result->document_date);
    }

    /** @test */
    public function it_updates_payment_form_with_all_fields(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'payment_form' => [
                'value' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
                'document_number' => 'INV-2025-002',
                'document_date' => '2025-02-01',
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertEquals(PaymentForm::PAID_VIA_INCOMING_INVOICE->value, $result->payment_form->value);
        $this->assertEquals('INV-2025-002', $result->document_number);
        $this->assertEquals('2025-02-01', $result->document_date);
    }

    /** @test */
    public function it_clears_payment_form_when_value_is_null(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'document_number' => 'OLD-001',
            'document_date' => '2024-12-01',
        ]);

        $inputs = [
            'payment_form' => [
                'value' => null,
                'document_number' => null,
                'document_date' => null,
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertNull($result->payment_form);
        $this->assertNull($result->document_number);
        $this->assertNull($result->document_date);
    }

    /** @test */
    public function it_fails_validation_when_payment_form_value_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $inputs = [
            'payment_form' => [
                'value' => 999,
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $action->handle($user, $company, $workLog, $inputs);
    }

    /** @test */
    public function it_accepts_paid_via_payroll_value(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'payment_form' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
        ]);

        $inputs = [
            'payment_form' => [
                'value' => PaymentForm::PAID_VIA_PAYROLL->value,
                'document_number' => null,
                'document_date' => null,
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertEquals(PaymentForm::PAID_VIA_PAYROLL->value, $result->payment_form->value);
    }

    /** @test */
    public function it_returns_fresh_model_after_update(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
        ]);

        $originalId = $workLog->id;

        $inputs = [
            'payment_form' => [
                'value' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
                'document_number' => 'DOC-001',
                'document_date' => '2025-01-15',
            ],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertEquals($originalId, $result->id);
        $this->assertDatabaseHas('work_logs', [
            'id' => $originalId,
            'payment_form' => PaymentForm::PAID_VIA_INCOMING_INVOICE->value,
            'document_number' => 'DOC-001',
            'document_date' => '2025-01-15',
        ]);
    }

    /** @test */
    public function it_handles_empty_payment_form_array(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
        ]);

        $inputs = [
            'payment_form' => [],
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertNull($result->payment_form);
    }

    /** @test */
    public function it_handles_null_payment_form(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $workLog = WorkLog::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
        ]);

        $inputs = [
            'payment_form' => null,
        ];

        Gate::shouldReceive('forUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        Gate::shouldReceive('authorize')
            ->with('update', $workLog)
            ->once();

        $action = new UpdateWorkLogPaymentForm;
        $result = $action->handle($user, $company, $workLog, $inputs);

        $this->assertNull($result->payment_form);
    }
}
