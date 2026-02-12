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

        $allUnpaid = Loyer::with(['contrat.locataire', 'contrat.bien'])
            ->where('mois', $mois)
            ->whereIn('statut', ['en_retard', 'partiellement_payé', 'émis'])
            ->get();

        $impayees = [
            'en_retard' => $allUnpaid->filter(fn($l) => $l->statut === 'en_retard' || ($l->statut === 'émis' && now()->gt($l->date_echeance))),
            'partiellement_paye' => $allUnpaid->filter(fn($l) => $l->statut === 'partiellement_payé'),
            'non_echu' => $allUnpaid->filter(fn($l) => $l->statut === 'émis' && now()->lte($l->date_echeance)),
        ];

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

    public function exportLoyersCSV(Request $request)
    {
        $mois = $request->get('mois', Carbon::now()->format('Y-m'));
        $loyers = Loyer::with(['contrat.locataire', 'contrat.bien'])
            ->where('mois', $mois)
            ->get();

        $filename = "rapport_loyers_{$mois}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Bien', 'Locataire', 'Montant', 'Statut', 'Date Echeance'];

        $callback = function() use($loyers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            foreach ($loyers as $loyer) {
                fputcsv($file, [
                    $loyer->contrat->bien->nom ?? 'N/A',
                    $loyer->contrat->locataire->nom ?? 'N/A',
                    $loyer->montant,
                    $loyer->statut,
                    $loyer->date_echeance->format('d/m/Y'),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportImpayeesCSV(Request $request)
    {
        $mois = $request->get('mois', Carbon::now()->format('Y-m'));
        $allUnpaid = Loyer::with(['contrat.locataire', 'contrat.bien'])
            ->where('mois', $mois)
            ->whereIn('statut', ['en_retard', 'partiellement_payé', 'émis'])
            ->get();

        $filename = "rapport_impayes_{$mois}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Locataire', 'Bien', 'Montant Total', 'Reste à Payer', 'Jours de Retard', 'Statut'];

        $callback = function() use($allUnpaid, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            foreach ($allUnpaid as $loyer) {
                fputcsv($file, [
                    $loyer->contrat->locataire->nom ?? 'N/A',
                    $loyer->contrat->bien->nom ?? 'N/A',
                    $loyer->montant,
                    $loyer->reste_a_payer,
                    $loyer->jours_retard,
                    $loyer->statut,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
