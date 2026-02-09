<?php

namespace App\Observers;

use App\Services\DashboardStatsService;

class DashboardStatsObserver
{
    protected $statsService;

    public function __construct(DashboardStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function saved($model)
    {
        $this->statsService->clearCache();
    }

    public function deleted($model)
    {
        $this->statsService->clearCache();
    }
}
