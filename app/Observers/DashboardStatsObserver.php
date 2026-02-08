<?php

namespace App\Observers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Depense;
use App\Models\Loyer;
use App\Models\Paiement;
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
