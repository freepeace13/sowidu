<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\WorkLogs\Actions\CreateManualWorkLog;
use Modules\WorkLogs\Actions\DeleteManualWorkLog;
use Modules\WorkLogs\Actions\UpdateManualWorkLog;
use Modules\WorkLogs\Actions\UpdateWorkLogPaymentForm;
use Modules\WorkLogs\Contracts\Actions\CreateManualWorkLog as CreateManualWorkLogContract;
use Modules\WorkLogs\Contracts\Actions\DeleteManualWorkLog as DeleteManualWorkLogContract;
use Modules\WorkLogs\Contracts\Actions\UpdateManualWorkLog as UpdateManualWorkLogContract;
use Modules\WorkLogs\Contracts\Actions\UpdateWorkLogPaymentForm as UpdateWorkLogPaymentFormContract;
use Modules\WorkLogs\Contracts\WorkLogRepositoryInterface;
use Modules\WorkLogs\Contracts\WorkLogServiceInterface;
use Modules\WorkLogs\Repository\WorkLogRepository;
use Modules\WorkLogs\Services\WorkLogService;

class BindingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->services();
        $this->repositories();
        $this->actions();
    }

    protected function services(): void
    {
        $this->app->bind(WorkLogServiceInterface::class, WorkLogService::class);
    }

    protected function repositories(): void
    {
        $this->app->bind(WorkLogRepositoryInterface::class, WorkLogRepository::class);
    }

    protected function actions(): void
    {
        $this->app->bind(CreateManualWorkLogContract::class, CreateManualWorkLog::class);
        $this->app->bind(DeleteManualWorkLogContract::class, DeleteManualWorkLog::class);
        $this->app->bind(UpdateManualWorkLogContract::class, UpdateManualWorkLog::class);
        $this->app->bind(UpdateWorkLogPaymentFormContract::class, UpdateWorkLogPaymentForm::class);
    }
}
