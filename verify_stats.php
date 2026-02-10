<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bien;
use App\Models\Depense;
use App\Models\Loyer;
use App\Models\Paiement;
use Carbon\Carbon;

$date = Carbon::now();
$moisStr = $date->format('Y-m');
$month = $date->month;
$year = $date->year;

echo "\n--- ANALYSE FINANCIÈRE ".$date->format('F Y')." ---\n";

// 1. ENCAISSEMENTS
$paiements = Paiement::whereMonth('date_paiement', $month)
    ->whereYear('date_paiement', $year)
    ->get();
$totalEncaisse = $paiements->sum('montant');
echo 'ENC (Paiements): '.number_format($totalEncaisse, 0, ',', ' ')." F\n";

// 2. DÉPENSES
$depenses = Depense::whereMonth('date_depense', $month)
    ->whereYear('date_depense', $year)
    ->get();
$totalDepenses = $depenses->sum('montant');
echo 'DEC (Dépenses): '.number_format($totalDepenses, 0, ',', ' ')." F\n";

echo "-------------------------------------\n";
echo 'SOLDE NET (NOI) = '.number_format($totalEncaisse - $totalDepenses, 0, ',', ' ')." F\n\n";

// 3. GROSS POTENTIAL RENT
$grossPotential = Bien::sum('loyer_mensuel');
echo 'GROSS POTENTIAL RENT: '.number_format($grossPotential, 0, ',', ' ')." F\n";

// 4. LOYERS FACTURÉS (Mois en cours)
$loyers = Loyer::where('mois', $moisStr)->where('statut', '!=', 'annulé')->get();
$totalFacture = $loyers->sum('montant');
echo 'LOYERS FACTURÉS: '.number_format($totalFacture, 0, ',', ' ')." F\n";

// 5. TAUX DE RECOUVREMENT FINANCIER
$tauxRecouvrement = $totalFacture > 0 ? ($totalEncaisse / $totalFacture) * 100 : 0;
echo 'TAUX RECOUVREMENT FIN.: '.number_format($tauxRecouvrement, 1)." %\n";

// 6. TAUX OCCUPATION FINANCIER
$tauxOc = $grossPotential > 0 ? ($totalFacture / $grossPotential) * 100 : 0;
echo 'TAUX OCCUPATION FIN.: '.number_format($tauxOc, 1)." %\n\n";

// 7. ARREARS AGING
echo "--- ARREARS AGING ---\n";
$loyersImpayes = Loyer::whereIn('statut', ['émis', 'en_retard', 'partiellement_payé'])
    ->where('statut', '!=', 'annulé')
    ->withSum('paiements', 'montant')
    ->get();

$aging = ['0-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0];

foreach ($loyersImpayes as $loyer) {
    $reste = $loyer->montant - ($loyer->paiements_sum_montant ?? 0);
    if ($reste <= 0.5) {
        continue;
    }

    $dateLoyer = Carbon::parse($loyer->mois.'-01');
    $ageJours = $dateLoyer->diffInDays(Carbon::now());

    $cat = '90+';
    if ($ageJours <= 30) {
        $cat = '0-30';
    } elseif ($ageJours <= 60) {
        $cat = '31-60';
    } elseif ($ageJours <= 90) {
        $cat = '61-90';
    }

    $aging[$cat] += $reste;
}

foreach ($aging as $k => $v) {
    echo "$k jours : ".number_format($v, 0, ',', ' ')." F\n";
}
