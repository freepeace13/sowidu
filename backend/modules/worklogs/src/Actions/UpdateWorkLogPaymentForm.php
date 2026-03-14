<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Actions;

use Modules\WorkLogs\Contracts\Actions\UpdateWorkLogPaymentForm as ActionsUpdateWorkLogPaymentForm;
use Modules\WorkLogs\Contracts\External\AuthorizationContract;
use Modules\WorkLogs\Models\WorkLog;
use Modules\WorkLogs\Traits\AsAction;

class UpdateWorkLogPaymentForm implements ActionsUpdateWorkLogPaymentForm
{
    use AsAction;

    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function handle(mixed $user, mixed $company, WorkLog $workLog, array $inputs)
    {
        $this->authorization->authorize($user, 'update', $workLog);

        $validated = validator($inputs, [
            'payment_form' => ['nullable', 'array'],
            'payment_form.value' => ['nullable', 'integer', 'in:1,2'],
            'payment_form.document_number' => 'nullable|string',
            'payment_form.document_date' => 'nullable|date',
        ])->validate();

        $paymentFormValue = $validated['payment_form']['value'] ?? null;
        $documentNumber = $validated['payment_form']['document_number'] ?? null;
        $documentDate = $validated['payment_form']['document_date'] ?? null;

        $workLog->update([
            'payment_form' => $paymentFormValue,
            'document_number' => $documentNumber,
            'document_date' => $documentDate,
        ]);

        return $workLog->fresh();
    }
}
