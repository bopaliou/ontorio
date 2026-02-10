<?php

namespace App\Observers;

use App\Services\DashboardStatsService;
use Illuminate\Database\Eloquent\Model;

class DashboardStatsObserver
{
    protected $statsService;

    public function __construct(DashboardStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /** @param  Model  $model  Observed model instance (unused — cache is global) */
    public function saved(Model $model): void
    {
        $this->statsService->clearCache();
    }

    /** @param  Model  $model  Observed model instance (unused — cache is global) */
    public function deleted(Model $model): void
    {
        $this->statsService->clearCache();
    }
}
