<?php

namespace App\Http\Controllers;

use App\Models\Loyer;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    protected $statsService;

    public function __construct(DashboardStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function loyers(Request $request)
    {
        $mois = $request->get('mois', Carbon::now()->format('Y-m'));
        $data = $this->statsService->getFinancialKPIs();
        $chartData = $this->statsService->getChartData();

        $loyers = Loyer::with(['contrat.locataire', 'contrat.bien'])
            ->where('mois', $mois)
            ->get();

        return view('rapports.loyers', compact('data', 'chartData', 'loyers', 'mois'));
    }

    public function impayees(Request $request)
    {
        $data = $this->statsService->getFinancialKPIs();

        $impayees = Loyer::with(['contrat.locataire', 'contrat.bien'])
            ->whereIn('statut', ['en_retard', 'partiel'])
            ->orderBy('date_echeance')
            ->get();

        return view('rapports.impayees', compact('data', 'impayees'));
    }

    public function commissions(Request $request)
    {
        // TODO: Implement commission logic
        // For now, simple mockup of data
        $data = $this->statsService->getFinancialKPIs();

        return view('rapports.commissions', compact('data'));
    }
}
