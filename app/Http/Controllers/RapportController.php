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
        $data = $this->statsService->getFinancialKPIs($mois);
        $chartData = $this->statsService->getChartData();

        $loyers = Loyer::with(['contrat.locataire', 'contrat.bien'])
            ->where('mois', $mois)
            ->get();

        return view('rapports.loyers', compact('data', 'chartData', 'loyers', 'mois'));
    }

    public function impayees(Request $request)
    {
        $mois = $request->get('mois', Carbon::now()->format('Y-m'));
        $data = $this->statsService->getFinancialKPIs($mois);

        $impayees = Loyer::with(['contrat.locataire', 'contrat.bien'])
            ->where('mois', $mois)
            ->whereIn('statut', ['en_retard', 'partiellement_payé', 'émis'])
            ->get()
            ->sortBy('date_echeance');

        return view('rapports.impayees', compact('data', 'impayees', 'mois'));
    }

    public function commissions(Request $request)
    {
        $mois = $request->get('mois', Carbon::now()->format('Y-m'));
        $tauxCommission = (float) config('real_estate.commission.rate', 0.10);

        $data = $this->statsService->getFinancialKPIs($mois);

        $encaissements = Loyer::with(['contrat.locataire', 'contrat.bien'])
            ->withSum('paiements', 'montant')
            ->where('mois', $mois)
            ->whereIn('statut', ['payé', 'partiellement_payé'])
            ->orderByDesc('paiements_sum_montant')
            ->get();

        $baseCommissionnable = (float) $encaissements->sum(function (Loyer $loyer) {
            return (float) min($loyer->montant, (float) ($loyer->paiements_sum_montant ?? 0));
        });

        $commissionHonoraires = round($baseCommissionnable * $tauxCommission, 2);

        return view('rapports.commissions', compact(
            'data',
            'mois',
            'tauxCommission',
            'encaissements',
            'baseCommissionnable',
            'commissionHonoraires'
        ));
    }
}
