<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Models\Concerns;

use Modules\WorkLogs\Contracts\External\ModelConfigContract;

/**
 * Trait for resolving external model relationships.
 *
 * Uses ModelConfigContract to get model class names
 * instead of importing them directly from main app.
 */
trait HasExternalRelationships
{
    protected function getModelConfig(): ModelConfigContract
    {
        return app(ModelConfigContract::class);
    }

    public function user()
    {
        return $this->belongsTo($this->getModelConfig()->getUserModel());
    }

    public function causer()
    {
        return $this->belongsTo($this->getModelConfig()->getUserModel(), 'user_id');
    }

    public function author()
    {
        return $this->belongsTo($this->getModelConfig()->getUserModel(), 'author_id');
    }

    public function company()
    {
        return $this->belongsTo($this->getModelConfig()->getCompanyModel());
    }

    public function order()
    {
        return $this->belongsTo($this->getModelConfig()->getOrderModel());
    }

    public function reports()
    {
        return $this->hasMany($this->getModelConfig()->getActivityLogReportModel());
    }

    public function item()
    {
        return $this->morphOne($this->getModelConfig()->getInvoiceItemModel(), 'item');
    }
}
