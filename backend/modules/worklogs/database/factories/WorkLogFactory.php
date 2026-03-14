<?php

namespace Modules\WorkLogs\Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\WorkLogs\Enums\PaymentForm;
use Modules\WorkLogs\Models\WorkLog;

class WorkLogFactory extends Factory
{
    protected $model = WorkLog::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'author_id' => User::factory(),
            'company_id' => Company::factory(),
            'started_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'ended_at' => $this->faker->dateTimeBetween('now', '+1 day'),
            'duration_in_seconds' => 3600,
            'notes' => $this->faker->sentence(),
            'event' => 'manual',
            'payment_form' => PaymentForm::PAID_VIA_PAYROLL->value,
            'is_shared' => false,
            'is_paid' => true,
        ];
    }
}
