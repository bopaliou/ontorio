<?php

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Loyer;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bien = Bien::latest('updated_at')->first();
if (! $bien) {
    echo "Aucun bien trouvé.\n";
    exit;
}

echo 'Dernier Bien Modifié : '.$bien->nom.' (ID: '.$bien->id.")\n";
echo 'Loyer Mensuel (DB) : '.$bien->loyer_mensuel."\n";
echo 'Mise à jour le : '.$bien->updated_at."\n\n";

$contrats = Contrat::where('bien_id', $bien->id)->get();
echo 'Contrats associés ('.$contrats->count().") :\n";
foreach ($contrats as $c) {
    echo "- ID: {$c->id}, Statut: {$c->statut}, Loyer Montant: {$c->loyer_montant}\n";

    $loyers = Loyer::where('contrat_id', $c->id)->where('mois', '>=', date('Y-m'))->get();
    foreach ($loyers as $l) {
        echo "  > Loyer {$l->mois} (Statut: {$l->statut}) : {$l->montant}\n";
    }
}
