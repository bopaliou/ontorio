<?php

use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Schema::getColumnListing('paiements');
echo "Colonnes table paiements :\n";
print_r($columns);

if (in_array('preuve', $columns)) {
    echo "\nLa colonne 'preuve' EXISTE déjà.\n";
} else {
    echo "\nLa colonne 'preuve' N'EXISTE PAS.\n";
}
